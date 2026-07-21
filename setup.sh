#!/usr/bin/env bash
set -Eeuo pipefail

PROJECT_NAME="Liga de Ajedrez del Meta"
BACKEND_DIR="$(cd "$(dirname "$0")" && pwd)"
FRONTEND_DIR="$BACKEND_DIR/../liga-ajedrez-tesis-front"

GREEN="\033[0;32m";YELLOW="\033[1;33m";RED="\033[0;31m";BLUE="\033[1;34m";CYAN="\033[1;36m";NC="\033[0m"

msg(){ echo -e "${BLUE}➜ $1${NC}"; }
ok(){ echo -e "${GREEN}✔ $1${NC}"; }
warn(){ echo -e "${YELLOW}⚠ $1${NC}"; }
fail(){ echo -e "${RED}✖ $1${NC}"; exit 1; }

banner(){
clear
cat <<EOF
========================================================
        $PROJECT_NAME
 Docker Development Environment Installer
========================================================
EOF
}

require(){
command -v "$1" >/dev/null || fail "$1 no está instalado."
}

wait_container(){
local c="$1"
until docker exec "$c" true >/dev/null 2>&1; do
  printf "."
  sleep 2
done
echo
}

wait_postgres() {
    local c="$1"
    msg "Esperando PostgreSQL..."
    until docker exec "$c" pg_isready -U postgres >/dev/null 2>&1
    do
        printf "."
        sleep 2
    done
    echo
    ok "PostgreSQL disponible."
}

container_name(){
docker compose ps --format json | python3 - <<'PY'
import json,sys
rows=[json.loads(x) for x in sys.stdin if x.strip()]
name=sys.argv[1]
for r in rows:
    if r.get("Service")==name:
        print(r["Name"]);break
PY
}

banner
require docker
docker compose version >/dev/null || fail "Docker Compose no disponible."
docker info >/dev/null || fail "Docker Desktop no iniciado."

[ -f "$BACKEND_DIR/composer.json" ] || fail "Ejecute el script desde la raíz del backend."
[ -d "$FRONTEND_DIR" ] || fail "No existe el frontend: $FRONTEND_DIR"

ok "Validaciones completadas."

if docker compose ps -a --status running >/dev/null 2>&1 || docker compose ps -a | grep -q . ; then
echo
echo "Se detectó una instalación del proyecto."
echo "1) Reutilizar"
echo "2) Reconstruir imágenes"
echo "3) Reinstalar completamente (contenedores, imágenes y volúmenes del proyecto)"
echo "4) Cancelar"
read -rp "Seleccione una opción [1-4]: " op
case "$op" in
1) docker compose up -d;;
2) docker compose build && docker compose up -d;;
3)
docker compose down -v --remove-orphans || true
docker compose rm -f || true
docker compose images -q | xargs -r docker rmi -f || true
docker compose build --no-cache
docker compose up -d
;;
*) exit 0;;
esac
else
docker compose build
docker compose up -d
fi

BACKEND_CONTAINER=$(docker compose ps --format json | python3 -c 'import sys,json
rows=[json.loads(x) for x in sys.stdin if x.strip()]
print(next(r["Name"] for r in rows if r["Service"]=="backend"))')
FRONTEND_CONTAINER=$(docker compose ps --format json | python3 -c 'import sys,json
rows=[json.loads(x) for x in sys.stdin if x.strip()]
print(next(r["Name"] for r in rows if r["Service"]=="frontend"))')
DB_CONTAINER=$(docker compose ps --format json | python3 -c 'import sys,json
rows=[json.loads(x) for x in sys.stdin if x.strip()]
print(next(r["Name"] for r in rows if r["Service"]=="db"))')

wait_container "$BACKEND_CONTAINER"
wait_postgres "$DB_CONTAINER"

msg "Composer"
docker exec "$BACKEND_CONTAINER" test -d /var/www/vendor || docker exec "$BACKEND_CONTAINER" composer install

msg ".env"
docker exec "$BACKEND_CONTAINER" test -f /var/www/.env || docker exec "$BACKEND_CONTAINER" cp /var/www/.env.example /var/www/.env

msg "APP_KEY"
docker exec "$BACKEND_CONTAINER" php artisan key:generate --force

msg "Storage"
docker exec "$BACKEND_CONTAINER" php artisan storage:link || true

msg "Migraciones"
docker exec "$BACKEND_CONTAINER" php artisan migrate --force

read -rp "¿Ejecutar seeders? [y/N]: " s
if [[ "$s" =~ ^[Yy]$ ]]; then
 docker exec "$BACKEND_CONTAINER" php artisan db:seed --force
fi

if [ -f "$FRONTEND_DIR/package.json" ]; then
 if [ ! -d "$FRONTEND_DIR/node_modules" ]; then
   if docker exec "$FRONTEND_CONTAINER" sh -c "command -v pnpm" >/dev/null 2>&1; then
      docker exec "$FRONTEND_CONTAINER" pnpm install
   elif docker exec "$FRONTEND_CONTAINER" sh -c "command -v yarn" >/dev/null 2>&1; then
      docker exec "$FRONTEND_CONTAINER" yarn
   else
      docker exec "$FRONTEND_CONTAINER" npm install
   fi
 fi
fi

ok "Instalación completada."
echo
echo "Backend : http://localhost:8000"
echo "Frontend: http://localhost:8001"
echo
echo "Comandos útiles:"
echo "docker compose up -d"
echo "docker compose down"
echo "docker compose logs -f"
