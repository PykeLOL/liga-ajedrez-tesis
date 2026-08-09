<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\EstadoPlan;
use App\Traits\FiltraPorRol;
use App\Models\PlanEntrenamiento;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\PlanEntrenamientoHorario;
use App\Services\NotificacionDomainService;
use App\Services\PlanEntrenamientoGeneratorService;
use App\Http\Resources\PlanEntrenamiento\PlanEntrenamientoResource;
use App\Http\Requests\PlanEntrenamiento\StorePlanEntrenamientoRequest;
use App\Http\Requests\PlanEntrenamiento\UpdatePlanEntrenamientoRequest;
use App\Http\Resources\PlanEntrenamiento\PlanEntrenamientoIndexResource;
use App\Http\Resources\PlanEntrenamiento\PlanEntrenamientoResumenResource;
use App\Http\Resources\PlanEntrenamiento\PlanEntrenamientoGeneracionPreviewResource;

class PlanEntrenamientoController extends Controller
{
    use FiltraPorRol;

    protected NotificacionDomainService $notificacionDomainService;
    protected PlanEntrenamientoGeneratorService $planEntrenamientoGeneratorService;

    public function __construct(
        NotificacionDomainService $notificacionDomainService,
        PlanEntrenamientoGeneratorService $planEntrenamientoGeneratorService
    ) {
        $this->notificacionDomainService = $notificacionDomainService;
        $this->planEntrenamientoGeneratorService = $planEntrenamientoGeneratorService;
    }

    public function index()
    {
        $planes = $this->filtrarPorClub(
            PlanEntrenamiento::with([
                'club',
                'categoria',
                'genero',
                'entrenador.usuario',
                'tipo',
                'evento',
                'estado',
                'horarios',
                'horarios.diaSemana',
                'deportistas'
            ])
            ->withCount('deportistas')
            ->orderByDesc('id')
        )->get();

        return PlanEntrenamientoIndexResource::collection($planes);
    }

    public function show(int $id)
    {
        $plan = $this->filtrarPorClub(
            PlanEntrenamiento::with([
                'club',
                'categoria',
                'genero',
                'entrenador.usuario',
                'tipo',
                'evento',
                'estado',
                'horarios.diaSemana',
                'deportistas.usuario',
                'deportistas.titulo',
            ])
        )->find($id);

        if (!$plan) {
            return response()->json([
                'message' => 'Plan de entrenamiento no encontrado.'
            ], 404);
        }

        return new PlanEntrenamientoResource($plan);
    }

    public function store(StorePlanEntrenamientoRequest $request)
    {
        DB::beginTransaction();

        try {
            $plan = PlanEntrenamiento::create([
                'club_id' => $request->club_id,
                'categoria_id' => $request->categoria_id,
                'genero_id' => $request->genero_id,
                'entrenador_id' => $request->entrenador_id,
                'tipo_entrenamiento_id' => $request->tipo_entrenamiento_id,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'ubicacion' => $request->ubicacion,
                'url_mapa' => $request->url_mapa,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'evento_id' => $request->evento_id,
                'estado_plan_id' => $this->obtenerEstadoInicial(),
            ]);

            $this->guardarHorarios($plan, $request->horarios);
            $this->sincronizarDeportistas($plan, $request->deportistas);

            DB::commit();

            return response()->json([
                'message' => 'Plan de entrenamiento creado correctamente.',
                'data' => new PlanEntrenamientoResource($this->cargarRelaciones($plan))
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Ocurrió un error al crear el plan.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdatePlanEntrenamientoRequest $request, int $id)
    {
        $plan = $this->filtrarPorClub(PlanEntrenamiento::query())->find($id);
        if (!$plan) {
            return response()->json([
                'message' => 'Plan de entrenamiento no encontrado.'
            ], 404);
        }

        DB::beginTransaction();

        try {
            $plan->update([
                'club_id' => $request->club_id,
                'categoria_id' => $request->categoria_id,
                'genero_id' => $request->genero_id,
                'entrenador_id' => $request->entrenador_id,
                'tipo_entrenamiento_id' => $request->tipo_entrenamiento_id,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'ubicacion' => $request->ubicacion,
                'url_mapa' => $request->url_mapa,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'evento_id' => $request->evento_id,
            ]);

            $plan->horarios()->delete();

            $this->guardarHorarios($plan, $request->horarios);
            $this->sincronizarDeportistas($plan, $request->deportistas);

            DB::commit();

            return response()->json([
                'message' => 'Plan de entrenamiento actualizado correctamente.',
                'data' => new PlanEntrenamientoResource($this->cargarRelaciones($plan))
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Ocurrió un error al actualizar el plan.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        $plan = $this->filtrarPorClub(PlanEntrenamiento::query())->find($id);
        if (!$plan) {
            return response()->json([
                'message' => 'Plan de entrenamiento no encontrado.'
            ], 404);
        }

        $plan->delete();

        return response()->json([
            'message' => 'Plan de entrenamiento eliminado correctamente.'
        ]);
    }

    private function guardarHorarios(PlanEntrenamiento $plan, array $horarios)
    {
        foreach ($horarios as $horario) {
            PlanEntrenamientoHorario::create([
                'plan_entrenamiento_id' => $plan->id,
                'dia_semana_id' => $horario['dia_semana_id'],
                'hora_inicio' => $horario['hora_inicio'],
                'hora_fin' => $horario['hora_fin'],
            ]);
        }
    }

    private function sincronizarDeportistas(PlanEntrenamiento $plan, array $deportistas = [])
    {
        $sync = [];

        foreach ($deportistas as $deportistaId) {
            $sync[$deportistaId] = [
                'estado' => true
            ];
        }

        $plan->deportistas()->sync($sync);
    }

    private function cargarRelaciones(PlanEntrenamiento $plan)
    {
        return $plan->load([
            'club',
            'categoria',
            'genero',
            'entrenador.usuario',
            'tipo',
            'evento',
            'estado',
            'horarios.diaSemana',
            'deportistas.usuario',
            'deportistas.titulo',
        ]);
    }

    private function obtenerEstadoInicial()
    {
        return EstadoPlan::where('nombre', EstadoPlan::BORRADOR)->value('id');
    }

    public function resumen(int $id)
    {
        $plan = $this->filtrarPorClub(
            PlanEntrenamiento::with([
                'club:id,nombre',
                'categoria:id,nombre',
                'genero:id,nombre',
                'tipo:id,nombre',
                'entrenador.usuario:id,nombre,apellido,numero_identificacion',
                'evento',
                'evento.tipoEvento',
                'horarios',
                'deportistas.usuario:id,nombre,apellido,numero_identificacion',
                'deportistas.categoria:id,nombre',
            ])
        )->findOrFail($id);

        return new PlanEntrenamientoResumenResource($plan);
    }

    public function previewGeneracion(int $id)
    {
        $plan = $this->filtrarPorClub(PlanEntrenamiento::query())->findOrFail($id);

        return new PlanEntrenamientoGeneracionPreviewResource(
            $this->planEntrenamientoGeneratorService->preview($plan)
        );
    }

    public function generarEntrenamientos(int $id)
    {
        $plan = $this->filtrarPorClub(PlanEntrenamiento::query())->findOrFail($id);
        $total = $this->planEntrenamientoGeneratorService->generar($plan);

        $plan->load([
            'entrenador.usuario',
            'deportistas.usuario'
        ]);

        $this->notificacionDomainService->entrenamientosGenerados($plan, $total);

        return response()->json([
            'message' => 'Entrenamientos generados correctamente.',
            'total_generados' => $total,
        ]);
    }
}
