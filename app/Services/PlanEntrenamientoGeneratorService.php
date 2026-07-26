<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\EstadoPlan;
use InvalidArgumentException;
use App\Models\Entrenamiento;
use App\Models\EstadoAsistencia;
use App\Models\PlanEntrenamiento;
use Illuminate\Support\Facades\DB;
use App\Models\EstadoEntrenamiento;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class PlanEntrenamientoGeneratorService
{
    public function generar(PlanEntrenamiento $plan): int
    {
        return DB::transaction(function () use ($plan) {
            $plan->load([
                'horarios',
                'deportistas' => function ($query) {
                    $query->wherePivot('estado', true);
                },
            ]);

            $this->validarRegeneracion($plan);

            $plan->entrenamientosGenerados()->delete();
            $estadoEntrenamiento = $this->obtenerEstadoProgramado();
            $estadoAsistencia = $this->obtenerEstadoPendiente();

            $entrenamientos = $this->obtenerEntrenamientosGenerados($plan);

            $cantidad = 0;

            foreach ($entrenamientos as $item) {
                $entrenamiento = Entrenamiento::create([
                    'nombre' => $plan->nombre,
                    'descripcion' => $plan->descripcion,
                    'observaciones' => null,
                    'plan_entrenamiento_id' => $plan->id,
                    'club_id' => $plan->club_id,
                    'categoria_id' => $plan->categoria_id,
                    'genero_id' => $plan->genero_id,
                    'entrenador_id' => $plan->entrenador_id,

                    'fecha' => $item['fecha'],
                    'hora_inicio' => $item['hora_inicio'],
                    'hora_fin' => $item['hora_fin'],

                    'ubicacion' => $plan->ubicacion,
                    'url_mapa' => $plan->url_mapa,
                    'tipo_entrenamiento_id' => $plan->tipo_entrenamiento_id,
                    'evento_id' => $plan->evento_id,
                    'estado_entrenamiento_id' => $estadoEntrenamiento,
                    'generado_automaticamente' => true,
                ]);

                $this->crearAsistencias(
                    $entrenamiento,
                    $plan,
                    $estadoAsistencia
                );

                $cantidad++;
            }

            $plan->estado_plan_id = EstadoPlan::where('nombre', EstadoPlan::ACTIVO)->value('id');
            $plan->save();

            return $cantidad;
        });
    }

    private function validarRegeneracion(PlanEntrenamiento $plan): void
    {
        $estadoPendiente = $this->obtenerEstadoPendiente();
        $entrenamientos = $this->obtenerEntrenamientosGenerados($plan);

        if (!empty($entrenamientos)) {
            $primerEntrenamiento = Carbon::parse(
                $entrenamientos[0]['fecha'] . ' ' . $entrenamientos[0]['hora_inicio'],
                'America/Bogota'
            );

            if ($primerEntrenamiento->lessThanOrEqualTo(Carbon::now('America/Bogota'))) {
                throw new HttpResponseException(
                    response()->json([
                        'message' => 'No es posible regenerar los entrenamientos porque el plan ya tiene historial de ejecución.'
                    ], 422)
                );
            }
        }

        $tieneAsistencias = $plan->entrenamientosGenerados()
            ->whereHas('deportistas', function ($query) use ($estadoPendiente) {
                $query->where(function ($sub) use ($estadoPendiente) {
                    $sub->where('entrenamiento_asistencias.estado_asistencia_id', '!=', $estadoPendiente)
                        ->orWhereNotNull('entrenamiento_asistencias.hora_llegada')
                        ->orWhereNotNull('entrenamiento_asistencias.observaciones');
                });
            })
            ->exists();

        if ($tieneAsistencias) {
            throw ValidationException::withMessages([
                'plan' => 'No es posible regenerar los entrenamientos porque existen asistencias registradas.',
            ]);
        }
    }

    private function agruparHorarios(PlanEntrenamiento $plan): array
    {
        $dias = [];

        foreach ($plan->horarios as $horario) {
            $dia = $this->mapearDiaSemana($horario->dia_semana_id);

            if (!isset($dias[$dia])) {
                $dias[$dia] = [];
            }

            $dias[$dia][] = $horario;
        }

        return $dias;
    }

    private function crearAsistencias(Entrenamiento $entrenamiento, PlanEntrenamiento $plan, int $estadoPendiente): void {
        $sync = [];

        foreach ($plan->deportistas as $deportista) {
            $sync[$deportista->id] = [
                'estado_asistencia_id' => $estadoPendiente,
                'hora_llegada' => null,
                'observaciones' => null,
            ];
        }

        $entrenamiento->deportistas()->sync($sync);
    }

    private function obtenerEstadoProgramado(): int
    {
        return EstadoEntrenamiento::where('nombre', EstadoEntrenamiento::PROGRAMADO)->value('id');
    }

    private function obtenerEstadoPendiente(): int
    {
        return EstadoAsistencia::where('nombre', EstadoAsistencia::PENDIENTE)->value('id');
    }

    private function mapearDiaSemana(int $diaSemanaId): int
    {
        switch ($diaSemanaId) {
            case 1:
                return 1;
            case 2:
                return 2;
            case 3:
                return 3;
            case 4:
                return 4;
            case 5:
                return 5;
            case 6:
                return 6;
            case 7:
                return 7;
            default:
                throw new InvalidArgumentException('Día de la semana inválido.');
        }
    }

    private function obtenerEntrenamientosGenerados(PlanEntrenamiento $plan): array
    {
        $horariosPorDia = $this->agruparHorarios($plan);

        $periodo = CarbonPeriod::create(
            $plan->fecha_inicio,
            $plan->fecha_fin
        );

        $entrenamientos = [];

        foreach ($periodo as $fecha) {
            $diaSemana = $fecha->dayOfWeekIso;
            if (!isset($horariosPorDia[$diaSemana])) {
                continue;
            }

            foreach ($horariosPorDia[$diaSemana] as $horario) {
                $entrenamientos[] = [
                    'fecha' => $fecha->format('Y-m-d'),
                    'dia' => $horario->diaSemana->nombre,
                    'hora_inicio' => $horario->hora_inicio,
                    'hora_fin' => $horario->hora_fin,
                ];
            }
        }

        return $entrenamientos;
    }

    public function preview(PlanEntrenamiento $plan): array
    {
        $plan->load([
            'horarios.diaSemana',
            'deportistas' => function ($query) {
                $query->wherePivot('estado', true);
            },
            'deportistas.usuario',
            'deportistas.titulo',
            'deportistas.categoria',
        ]);

        return [
            'ya_generados' => $plan->entrenamientos()
                ->where('generado_automaticamente', true)
                ->exists(),

            'total_existentes' => $plan->entrenamientos()
                ->where('generado_automaticamente', true)
                ->count(),

            'total_deportistas' => $plan->deportistas->count(),

            'entrenamientos' => $this->obtenerEntrenamientosGenerados($plan),
            'deportistas' => $plan->deportistas->map(function ($deportista) {
                return [
                    'id' => $deportista->id,
                    'nombre' => optional($deportista->usuario)->nombre_completo,
                    'numero_identificacion' => optional($deportista->usuario)->numero_identificacion,
                    'titulo' => optional($deportista->titulo)->abreviacion,
                    'categoria' => optional($deportista->categoria)->nombre,
                ];
            })->values(),
        ];
    }
}
