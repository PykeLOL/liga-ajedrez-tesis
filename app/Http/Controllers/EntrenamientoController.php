<?php

namespace App\Http\Controllers;

use App\Models\Deportista;
use App\Models\Entrenamiento;
use App\Models\EstadoAsistencia;
use App\Models\PlanEntrenamiento;
use Illuminate\Support\Facades\DB;
use App\Models\EstadoEntrenamiento;
use App\Http\Resources\Entrenamiento\EntrenamientoResource;
use App\Http\Requests\Entrenamiento\StoreEntrenamientoRequest;
use App\Http\Requests\Entrenamiento\UpdateEntrenamientoRequest;
use App\Http\Resources\Entrenamiento\EntrenamientoIndexResource;

class EntrenamientoController extends Controller
{
    public function index()
    {
        $entrenamientos = Entrenamiento::with([
            'planEntrenamiento',
            'club',
            'categoria',
            'genero',
            'tipo',
            'estado',
            'evento',
            'entrenador.usuario',
            'deportistas.usuario',
            'deportistas.titulo',
        ])->get();

        return EntrenamientoIndexResource::collection($entrenamientos);
    }

    public function show(int $id)
    {
        $entrenamiento = Entrenamiento::with([
            'planEntrenamiento',
            'club',
            'categoria',
            'genero',
            'tipo',
            'estado',
            'evento',
            'evento.tipoEvento',
            'entrenador.usuario',
            'deportistas.usuario',
            'deportistas.titulo',
            'deportistas.categoria',
        ])->find($id);

        if (!$entrenamiento) {
            return response()->json([
                'message' => 'Entrenamiento no encontrado.'
            ], 404);
        }

        return new EntrenamientoResource($entrenamiento);
    }

    public function showAsistencias(int $id)
    {
        $entrenamiento = Entrenamiento::with([
            'estado',
            'deportistas.usuario',
            'deportistas.titulo',
            'deportistas.categoria',
        ])->find($id);

        if (!$entrenamiento) {
            return response()->json([
                'message' => 'Entrenamiento no encontrado.'
            ], 404);
        }

        return new EntrenamientoResource($entrenamiento);
    }

    public function store(StoreEntrenamientoRequest $request)
    {
        $entrenamiento = DB::transaction(function () use ($request) {
            $data = $request->validated();

            $plan = null;

            if (!empty($data['plan_entrenamiento_id'])) {
                $plan = $this->completarDatosDesdePlan($data);
            }

            $data['estado_entrenamiento_id'] = $this->obtenerEstadoProgramado();

            $entrenamiento = Entrenamiento::create($data);

            if ($plan) {
                $this->crearAsistenciasDeportista($plan->deportistas, $entrenamiento);
            } else {
                $this->crearAsistenciasDeportista($data['deportistas'] ?? [], $entrenamiento);
            }

            return $entrenamiento;
        });

        $entrenamiento->load([
            'planEntrenamiento',
            'club',
            'categoria',
            'genero',
            'tipo',
            'estado',
            'evento',
            'entrenador.usuario',
            'deportistas.usuario',
            'deportistas.titulo',
        ]);

        return response()->json([
            'message' => 'Entrenamiento creado correctamente.',
            'entrenamiento' => new EntrenamientoResource($entrenamiento),
        ], 201);
    }

    public function update(UpdateEntrenamientoRequest $request, int $id)
    {
        $entrenamiento = Entrenamiento::find($id);
        if (!$entrenamiento) {
            return response()->json([
                'message' => 'Entrenamiento no encontrado.'
            ], 404);
        }

        DB::transaction(function () use ($request, $entrenamiento) {
            $data = $request->validated();
            $entrenamiento->update($data);

            $this->crearAsistenciasDeportista(
                $data['deportistas'] ?? [],
                $entrenamiento
            );
        });

        $entrenamiento->load([
            'planEntrenamiento',
            'club',
            'categoria',
            'genero',
            'tipo',
            'estado',
            'evento',
            'entrenador.usuario',
            'deportistas.usuario',
            'deportistas.titulo',
        ]);

        return response()->json([
            'message' => 'Entrenamiento actualizado correctamente.',
            'entrenamiento' => new EntrenamientoResource($entrenamiento),
        ]);
    }

    public function destroy(int $id)
    {
        $entrenamiento = Entrenamiento::find($id);

        if (!$entrenamiento) {
            return response()->json([
                'message' => 'Entrenamiento no encontrado.'
            ], 404);
        }

        $entrenamiento->delete();

        return response()->json([
            'message' => 'Entrenamiento eliminado correctamente.'
        ]);
    }

    private function completarDatosDesdePlan(array &$data): PlanEntrenamiento
    {
        $plan = PlanEntrenamiento::with([
            'deportistas' => function ($query) {
                $query->wherePivot('estado', true);
            }
        ])->findOrFail($data['plan_entrenamiento_id']);

        $data['club_id'] = $plan->club_id;
        $data['categoria_id'] = $plan->categoria_id;
        $data['genero_id'] = $plan->genero_id;
        $data['entrenador_id'] = $plan->entrenador_id;
        $data['tipo_entrenamiento_id'] = $plan->tipo_entrenamiento_id;
        $data['evento_id'] = $plan->evento_id;
        $data['nombre'] = $plan->nombre;
        $data['descripcion'] = $plan->descripcion;
        $data['ubicacion'] = $plan->ubicacion;
        $data['url_mapa'] = $plan->url_mapa;

        return $plan;
    }

    private function obtenerEstadoProgramado(): int
    {
        return EstadoEntrenamiento::where('nombre', EstadoEntrenamiento::PROGRAMADO)->firstOrFail()->id;
    }

    private function crearAsistenciasDeportista(iterable $deportistas, Entrenamiento $entrenamiento): void
    {
        $estadoPendiente = EstadoAsistencia::where('nombre', EstadoAsistencia::PENDIENTE)->value('id');
        $actuales = $entrenamiento->deportistas()
            ->withPivot([
                'estado_asistencia_id',
                'hora_llegada',
                'observaciones',
            ])
            ->get()
            ->keyBy('id');

        $data = [];

        foreach ($deportistas as $deportista) {
            $deportistaId = $deportista instanceof Deportista
                ? $deportista->id
                : $deportista;

            if ($actuales->has($deportistaId)) {
                $pivot = $actuales[$deportistaId]->pivot;

                $data[$deportistaId] = [
                    'estado_asistencia_id' => $pivot->estado_asistencia_id,
                    'hora_llegada' => $pivot->hora_llegada,
                    'observaciones' => $pivot->observaciones,
                ];
            } else {
                $data[$deportistaId] = [
                    'estado_asistencia_id' => $estadoPendiente,
                    'hora_llegada' => null,
                    'observaciones' => null,
                ];
            }
        }

        $entrenamiento->deportistas()->sync($data);
    }
}
