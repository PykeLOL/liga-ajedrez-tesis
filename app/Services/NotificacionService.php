<?php

namespace App\Services;

use App\Models\Notificacion;
use Illuminate\Support\Collection;

class NotificacionService
{
    public function crear(array $datos): Notificacion
    {
        return Notificacion::create([
            'usuario_id' => $datos['usuario_id'],
            'modulo_id' => $datos['modulo_id'] ?? null,
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'tipo_notificacion_id' => $datos['tipo_notificacion_id'],
            'url' => $datos['url'] ?? null,
            'leida' => false,
            'fecha_lectura' => null,
        ]);
    }

    public function listarPorUsuario(int $usuarioId): Collection
    {
        return Notificacion::with(['tipoNotificacion', 'modulo'])
                ->where('usuario_id', $usuarioId)
                ->orderByDesc('created_at')
                ->get();
    }

    public function contarNoLeidas(int $usuarioId): int
    {
        return Notificacion::where('usuario_id', $usuarioId)
            ->where('leida', false)
            ->count();
    }


    public function marcarComoLeida(int $id, int $usuarioId): bool
    {
        $notificacion = Notificacion::where('id', $id)
            ->where('usuario_id', $usuarioId)
            ->first();

        if (!$notificacion || $notificacion->leida) {
            return false;
        }

        $notificacion->update([
            'leida' => true,
            'fecha_lectura' => now(),
        ]);

        return true;
    }

    public function marcarTodasComoLeidas(int $usuarioId): void
    {
        Notificacion::where('usuario_id', $usuarioId)
            ->where('leida', false)
            ->update([
                'leida' => true,
                'fecha_lectura' => now(),
            ]);
    }
}
