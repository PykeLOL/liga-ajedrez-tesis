<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\Liga;
use App\Models\Ritmo;
use App\Models\Genero;
use App\Models\Evento;
use App\Models\RedSocial;
use App\Models\Categoria;
use App\Models\TipoEvento;
use App\Models\EventoMedia;
use App\Models\EstadoEvento;
use App\Models\EventoCategoria;
use App\Models\EventoDocumento;
use App\Models\EventoRedSocial;

class EventoSeeder extends Seeder
{
    public function run(): void
    {
        $ligas         = Liga::all();
        $tiposEvento   = TipoEvento::all();
        $estadosEvento = EstadoEvento::all();
        $categorias    = Categoria::where('activo', true)->get();
        $generos       = Genero::all();
        $ritmos        = Ritmo::all();

        foreach ($tiposEvento as $tipo) {
            for ($i = 1; $i <= 5; $i++) {
                $estado = $estadosEvento[$i % $estadosEvento->count()];
                $fechaInicio = Carbon::now()->addDays(rand(-30, 60));
                $fechaFin = (clone $fechaInicio)->addDays(rand(1, 3));
                $horaInicio = sprintf('%02d:%02d:00', rand(8, 18), rand(0, 1) ? 30 : 0);

                $dePago = rand(0, 1);

                if (!$dePago) {
                    $valorRango = '0';
                    $minValor = 0;
                    $maxValor = 0;
                } else {
                    if (rand(0, 1)) {
                        $minValor = $maxValor = rand(10000, 50000);
                        $valorRango = (string) $minValor;
                    } else {
                        $minValor = rand(10000, 20000);
                        $maxValor = rand(30000, 60000);
                        $valorRango = $minValor . '-' . $maxValor;
                    }
                }

                $evento = Evento::create([
                    'liga_id' => $ligas->random()->id,
                    'nombre' => $tipo->nombre . ' #' . $i,
                    'descripcion' => 'Evento tipo ' . $tipo->nombre . ' organizado por la liga.',
                    'tipo_evento_id' => $tipo->id,
                    'lugar' => 'Escenario Deportivo ' . rand(1, 10),
                    'direccion' => 'Calle ' . rand(1, 50),
                    'url_mapa' => 'https://maps.google.com/?q=' . Str::random(10),
                    'fecha_inicio' => $fechaInicio,
                    'hora_inicio' => $horaInicio,
                    'fecha_fin' => $fechaFin,
                    'estado_evento_id' => $estado->id,
                    'de_pago' => $dePago,
                    'valor_rango' => $valorRango,
                    'organizador_nombre' => 'Organizador ' . rand(1, 5),
                    'organizador_contacto' => '300' . rand(1000000, 9999999),
                    'publicado' => true,
                    'es_oficial' => true,
                ]);

                for ($m = 1; $m <= 5; $m++) {
                    EventoMedia::create([
                        'evento_id' => $evento->id,
                        'tipo' => 'imagen',
                        'orden' => $m,
                        'path' => 'eventos/media/evento_seeder/' . rand(1, 6) . '.jpg',
                        'descripcion' => 'Imagen ' . $m . ' del evento',
                    ]);
                }

                $redesSociales = RedSocial::all();
                foreach ($redesSociales as $index => $red) {
                    EventoRedSocial::create([
                        'evento_id' => $evento->id,
                        'red_social_id' => $red->id,
                        'orden' => $index + 1,
                        'url' => 'https://www.' . $red->nombre . '.com/evento_' . $evento->id,
                    ]);
                }

                $documentos = [
                    ['Cronograma', 'pdf'],
                    ['Reglamento', 'pdf'],
                    ['Listado de Clubes permitidos', 'xlsx'],
                    ['Documento de Inscripción', 'docx'],
                ];

                foreach ($documentos as $index => $doc) {
                    EventoDocumento::create([
                        'evento_id' => $evento->id,
                        'nombre' => $doc[0],
                        'tipo' => $doc[1],
                        'orden' => $index + 1,
                        'path' => 'eventos/documentos/evento_seeder/' . ($index + 1) . '.' . $doc[1],
                        'descripcion' => 'Documento del evento',
                    ]);
                }

                $categoriasValidas = $this->categoriasPorTipo($tipo->nombre, $categorias);

                if ($categoriasValidas->count() > 1) {
                    $categoriasAsignadas = $categoriasValidas->random(
                        rand(1, min(3, $categoriasValidas->count()))
                    );
                } else {
                    $categoriasAsignadas = $categoriasValidas;
                }

                $categoriasAsignadas = collect($categoriasAsignadas)->values();

                $generosValidos = $this->generosPorTipo($tipo->nombre, $generos);
                $ritmosValidos  = $this->ritmosPorTipo($tipo->nombre, $ritmos);

                $totalCupo = 0;
                $totalCategorias = $categoriasAsignadas->count();

                foreach ($categoriasAsignadas as $index => $categoria) {

                    $cupo = rand(8, 64);
                    $totalCupo += $cupo;

                    if (!$dePago) {
                        $costo = 0;
                    } else {
                        if ($minValor === $maxValor) {
                            $costo = $minValor;
                        } else {
                            if ($index === 0) {
                                $costo = $minValor;
                            } elseif ($index === $totalCategorias - 1) {
                                $costo = $maxValor;
                            } else {
                                $costo = rand($minValor, $maxValor);
                            }
                        }
                    }

                    EventoCategoria::create([
                        'evento_id' => $evento->id,
                        'categoria_id' => $categoria->id,
                        'genero_id' => $generosValidos->random()->id,
                        'ritmo_id' => $ritmosValidos->random()->id,
                        'cupo_maximo' => $cupo,
                        'costo_inscripcion' => $costo,
                    ]);
                }

                $evento->update([
                    'max_participantes' => $totalCupo,
                ]);
            }
        }
    }

    private function categoriasPorTipo($tipoNombre, $categorias)
    {
        $tipo = strtolower($tipoNombre);

        switch ($tipo) {
            case 'torneo':
            case 'campeonato':
                return $categorias->whereIn('nombre', [
                    'Sub-8', 'Sub-10', 'Sub-12', 'Sub-14', 'Sub-16', 'Sub-18'
                ]);

            case 'open':
            case 'abierto':
                return $categorias->whereIn('nombre', ['Libre', 'Mayores']);

            case 'sénior':
            case 'senior':
                return $categorias->where('nombre', 'Sénior');

            case 'festival':
            case 'exhibición':
            case 'exhibicion':
            case 'entrenamiento':
                return $categorias->where('nombre', 'Libre');

            default:
                return $categorias->where('nombre', 'Libre');
        }
    }

    private function generosPorTipo($tipoNombre, $generos)
    {
        $tipo = strtolower($tipoNombre);

        switch ($tipo) {
            case 'torneo':
            case 'campeonato':
            case 'sénior':
            case 'senior':
                return $generos->whereIn('nombre', ['Masculino', 'Femenino']);

            case 'open':
            case 'abierto':
                return $generos->whereIn('nombre', ['Masculino', 'Femenino', 'Mixto']);

            default:
                return $generos->where('nombre', 'Mixto');
        }
    }

    private function ritmosPorTipo($tipoNombre, $ritmos)
    {
        $tipo = strtolower($tipoNombre);

        switch ($tipo) {
            case 'torneo':
            case 'campeonato':
                return $ritmos->whereIn('nombre', ['Clásico', 'Rápido', 'Blitz']);

            case 'open':
            case 'abierto':
            case 'festival':
            case 'exhibición':
            case 'exhibicion':
            case 'entrenamiento':
                return $ritmos->whereIn('nombre', ['Rápido', 'Blitz']);

            case 'sénior':
            case 'senior':
                return $ritmos->whereIn('nombre', ['Clásico', 'Rápido']);

            default:
                return $ritmos->where('nombre', 'Rápido');
        }
    }
}
