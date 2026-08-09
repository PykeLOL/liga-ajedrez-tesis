<?php

namespace App\Traits;

use App\Models\Club;

trait FiltraPorRol
{
    protected function filtrarPorClub($query)
    {
        $usuario = auth()->user();
        $rol = $usuario->rol->nombre;

        if (in_array($rol, ['Admin', 'Presidente Liga'])) {
            return $query;
        }

        if ($rol === 'Presidente Club') {
            $clubId = Club::where('presidente_id', $usuario->id)->value('id');
            return $query->where('club_id', $clubId);
        }

        if ($rol === 'Entrenador') {
            if (!$usuario->entrenador) {
                abort(403, 'No tienes un entrenador asociado.');
            }

            return $query->where('club_id', $usuario->entrenador->club_id);
        }

        abort(403, 'No tienes permiso para realizar acciones de otros clubes.');
    }

    protected function validarClubPropio(Club $club)
    {
        $usuario = auth()->user();
        $rol = $usuario->rol->nombre;

        if (in_array($rol, ['Admin', 'Presidente Liga'])) {
            return;
        }

        if ($rol === 'Presidente Club') {
            $clubId = Club::where('presidente_id', $usuario->id)->value('id');

            if ($club->id == $clubId) {
                return;
            }
        }

        abort(403, 'No tienes permiso para realizar acciones de otros clubes.');
    }
}
