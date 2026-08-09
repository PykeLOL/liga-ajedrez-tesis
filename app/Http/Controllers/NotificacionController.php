<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Services\NotificacionService;
use App\Http\Resources\Notificaciones\NotificacionResource;

class NotificacionController extends Controller
{
    protected NotificacionService $notificacionService;

    public function __construct(NotificacionService $notificacionService)
    {
        $this->notificacionService = $notificacionService;
    }

    public function index()
    {
        $notificaciones = $this->notificacionService->listarPorUsuario(auth()->id());
        return NotificacionResource::collection($notificaciones);
    }

    public function noLeidas(): JsonResponse
    {
        return response()->json([
            'cantidad' => $this->notificacionService->contarNoLeidas(auth()->id())
        ]);
    }

    public function leer(int $id)
    {
        $resultado = $this->notificacionService->marcarComoLeida($id, auth()->id());
        if (!$resultado) {
            return false;
        }

        return true;
    }

    public function leerTodas(): JsonResponse
    {
        $this->notificacionService->marcarTodasComoLeidas(auth()->id());

        return response()->json([
            'message' => 'Todas las notificaciones fueron marcadas como leídas.'
        ]);
    }
}
