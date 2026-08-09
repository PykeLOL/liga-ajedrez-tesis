<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Evento;
use App\Models\Usuario;
use App\Models\EventoCategoria;
use App\Models\EventoInscripcion;
use App\Models\EstadoInscripcion;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EventoService
{
    public function confirmarInscripcion(int $eventoId, array $data, Usuario $usuario): EventoInscripcion
    {
        $evento = Evento::find($eventoId);
        if (optional($evento->tipoEvento)->nombre !== 'Torneo') {
            throw ValidationException::withMessages([
                'evento' => 'El evento seleccionado no es un torneo.'
            ]);
        }

        if (!$evento->publicado) {
            throw ValidationException::withMessages([
                'evento' => 'El torneo no se encuentra disponible.'
            ]);
        }

        if (Carbon::parse($evento->fecha_inicio)->isPast()) {
            throw ValidationException::withMessages([
                'evento' => 'Las inscripciones para este torneo ya finalizaron.'
            ]);
        }

        $deportista = $usuario->deportista;

        if (!$deportista) {
            throw ValidationException::withMessages([
                'usuario' => 'El usuario no posee un perfil de deportista.'
            ]);
        }

        $categoria = EventoCategoria::find($data['evento_categoria_id']);

        if (!$categoria || $categoria->evento_id != $evento->id) {
            throw ValidationException::withMessages([
                'evento_categoria_id' => 'La categoría seleccionada no pertenece al torneo.'
            ]);
        }

        $existe = EventoInscripcion::where('evento_categoria_id', $categoria->id)
            ->where('deportista_id', $deportista->id)
            ->where('estado_inscripcion_id', '!=', EstadoInscripcion::CANCELADO)
            ->exists();

        if ($existe) {
            throw ValidationException::withMessages([
                'evento' => 'Ya te encuentras inscrito en esta categoría.'
            ]);
        }

        $ocupados = EventoInscripcion::where('evento_categoria_id', $categoria->id)
            ->whereNotIn('estado_inscripcion_id', [
                EstadoInscripcion::RECHAZADO,
                EstadoInscripcion::CANCELADO
            ])
            ->count();

        if ($categoria->cupo_maximo && $ocupados >= $categoria->cupo_maximo) {
            throw ValidationException::withMessages([
                'evento' => 'No hay cupos disponibles para esta categoría.'
            ]);
        }


        return DB::transaction(function () use ($evento, $categoria, $deportista, $data) {
            $comprobante = null;

            if (!empty($data['comprobante'])) {
                $comprobante = $data['comprobante']->store(
                    "eventos/comprobantes/evento_{$evento->id}",
                    'public'
                );
            }

            return EventoInscripcion::create([
                'evento_id' => $evento->id,
                'evento_categoria_id' => $categoria->id,
                'deportista_id' => $deportista->id,
                'fecha_inscripcion' => now(),
                'pago' => !is_null($comprobante),
                'comprobante_path' => $comprobante,
                'valor_pagado' => null,
                'referencia_pago' => null,
                'estado_inscripcion_id' => EstadoInscripcion::PENDIENTE,
            ]);
        });
    }

    public function cancelarInscripcion(int $inscripcionId, Usuario $usuario): void
    {
        $deportista = $usuario->deportista;

        if (!$deportista) {
            throw ValidationException::withMessages([
                'usuario' => 'El usuario no posee un perfil de deportista.'
            ]);
        }

        $inscripcion = EventoInscripcion::with('evento')
            ->where('id', $inscripcionId)
            ->where('deportista_id', $deportista->id)
            ->first();

        if (!$inscripcion) {
            throw ValidationException::withMessages([
                'inscripcion' => 'La inscripción no fue encontrada.'
            ]);
        }

        if ($inscripcion->estado_inscripcion_id === EstadoInscripcion::CANCELADO) {
            throw ValidationException::withMessages([
                'inscripcion' => 'La inscripción ya fue cancelada.'
            ]);
        }

        if ($inscripcion->estado_inscripcion_id !== EstadoInscripcion::PENDIENTE) {
            throw ValidationException::withMessages([
                'inscripcion' => 'Solo es posible cancelar inscripciones pendientes.'
            ]);
        }

        if (Carbon::parse($inscripcion->evento->fecha_inicio)->isPast()) {
            throw ValidationException::withMessages([
                'inscripcion' => 'El torneo ya inició y la inscripción no puede cancelarse.'
            ]);
        }

        DB::transaction(function () use ($inscripcion) {
            $inscripcion->update([
                'estado_inscripcion_id' => EstadoInscripcion::CANCELADO,
                'observacion' => 'Cancelada por el deportista.'
            ]);
        });
    }
}
