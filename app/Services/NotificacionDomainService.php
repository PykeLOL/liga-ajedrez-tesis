<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\Solicitud;
use App\Models\Notificacion;
use App\Models\Entrenamiento;
use App\Models\TipoNotificacion;
use App\Models\PlanEntrenamiento;
use App\Models\EventoInscripcion;
use App\Models\PublicacionComentario;
use App\Models\PublicacionComentarioReaccion;

class NotificacionDomainService
{
    protected NotificacionService $notificacionService;

    public function __construct(NotificacionService $notificacionService)
    {
        $this->notificacionService = $notificacionService;
    }

    public function solicitudClubCreada(Solicitud $solicitud): void
    {
        $usuario = $solicitud->club->liga->presidente ?? null;
        if (!$usuario) return;

        $this->crear($usuario, Notificacion::SOLICITUD_CLUB, "El club {$solicitud->club->nombre} ha enviado una solicitud de registro.", TipoNotificacion::SISTEMA, 'admin/solicitudes');
    }

    public function solicitudDeportistaCreada(Solicitud $solicitud): void
    {
        $usuario = $solicitud->deportista->club->presidente ?? null;
        if (!$usuario) return;

        $this->crear($usuario, Notificacion::SOLICITUD_DEPORTISTA, "{$solicitud->usuario->nombre} {$solicitud->usuario->apellido} solicitó unirse a tu club.", TipoNotificacion::SISTEMA, 'admin/solicitudes');
    }

    public function solicitudAprobada(Solicitud $solicitud): void
    {
        if ($solicitud->tipo === Solicitud::TIPO_CLUB) {
            $descripcion = "Tu solicitud de registro del club '{$solicitud->club->nombre}' fue aprobada.";
        } else {
            $descripcion = "Tu solicitud para unirte al club '{$solicitud->deportista->club->nombre}' fue aprobada.";
        }

        $this->crear(
            $solicitud->usuario,
            Notificacion::SOLICITUD_APROBADA,
            $descripcion,
            TipoNotificacion::APROBACION,
            'mis-solicitudes'
        );
    }

    public function solicitudRechazada(Solicitud $solicitud): void
    {
        if ($solicitud->tipo === Solicitud::TIPO_CLUB) {
            $descripcion = "Tu solicitud de registro del club '{$solicitud->club->nombre}' fue rechazada.";
        } else {
            $descripcion = "Tu solicitud para unirte al club '{$solicitud->deportista->club->nombre}' fue rechazada.";
        }

        $this->crear(
            $solicitud->usuario,
            Notificacion::SOLICITUD_RECHAZADA,
            $descripcion,
            TipoNotificacion::RECHAZO,
            'mis-solicitudes'
        );
    }

    public function entrenamientoCreado(Entrenamiento $entrenamiento): void
    {
        if ($entrenamiento->entrenador && $entrenamiento->entrenador->usuario) {
            $this->crear(
                $entrenamiento->entrenador->usuario,
                Notificacion::ENTRENAMIENTO,
                "Has sido asignado como entrenador del entrenamiento '{$entrenamiento->nombre}'.",
                TipoNotificacion::ENTRENAMIENTO,
                'admin/entrenamientos'
            );
        }

        foreach ($entrenamiento->deportistas->unique('usuario_id') as $deportista) {
            if (!$deportista->usuario) continue;
            $this->crear(
                $deportista->usuario,
                Notificacion::ENTRENAMIENTO,
                "Has sido convocado al entrenamiento '{$entrenamiento->nombre}'.",
                TipoNotificacion::ENTRENAMIENTO,
                'entrenamientos'
            );
        }
    }

    public function entrenamientosGenerados(PlanEntrenamiento $plan, int $cantidad): void
    {
        $descripcionEntrenador = $cantidad == 1
            ? "Se programó 1 entrenamiento del plan '{$plan->nombre}'."
            : "Se programaron {$cantidad} entrenamientos del plan '{$plan->nombre}'.";

        if ($plan->entrenador && $plan->entrenador->usuario) {
            $this->crear(
                $plan->entrenador->usuario,
                Notificacion::PLAN_ENTRENAMIENTO,
                $descripcionEntrenador,
                TipoNotificacion::ENTRENAMIENTO,
                'admin/entrenamientos'
            );
        }

        $descripcionDeportista = $cantidad == 1
            ? "Se ha programado 1 entrenamiento para ti del plan '{$plan->nombre}'."
            : "Se han programado {$cantidad} entrenamientos para ti del plan '{$plan->nombre}'.";

        foreach ($plan->deportistas->unique('usuario_id') as $deportista) {
            if (!$deportista->usuario) continue;

            $this->crear(
                $deportista->usuario,
                Notificacion::PLAN_ENTRENAMIENTO,
                $descripcionDeportista,
                TipoNotificacion::ENTRENAMIENTO,
                'entrenamientos'
            );
        }
    }

    public function foroComentado(PublicacionComentario $comentario): void
    {
        $publicacion = $comentario->publicacion;
        if (!$publicacion || $publicacion->usuario_id == $comentario->usuario_id) return;

        $this->crear(
            $publicacion->usuario,
            Notificacion::COMENTARIO_FORO,
            "{$comentario->usuario->nombre} {$comentario->usuario->apellido} comentó tu publicación '{$publicacion->titulo}'.",
            TipoNotificacion::COMENTARIO,
            'foro'
        );
    }

    public function actividadForo(PublicacionComentario $comentario): void
    {
        $publicacion = $comentario->publicacion;
        if (!$publicacion) return;

        $usuarios = $publicacion->comentarios()
            ->where('usuario_id', '!=', $comentario->usuario_id)
            ->pluck('usuario_id')
            ->push($publicacion->usuario_id)
            ->unique()
            ->filter(function ($id) use ($comentario) {
                return $id != $comentario->usuario_id;
            });

        foreach ($usuarios as $usuarioId) {
            if ($usuarioId == $publicacion->usuario_id) continue;

            $usuario = Usuario::find($usuarioId);
            if (!$usuario) continue;

            $this->crear(
                $usuario,
                Notificacion::ACTIVIDAD_FORO,
                "{$comentario->usuario->nombre} {$comentario->usuario->apellido} comentó en una publicación donde participas.",
                TipoNotificacion::COMENTARIO,
                'foro'
            );
        }
    }

    public function comentarioReaccionado(PublicacionComentarioReaccion $reaccion): void
    {
        $comentario = $reaccion->comentario;

        if (!$comentario || $comentario->usuario_id == $reaccion->usuario_id) return;

        $this->crear(
            $comentario->usuario,
            Notificacion::REACCION_FORO,
            "{$reaccion->usuario->nombre} {$reaccion->usuario->apellido} reaccionó a tu comentario en '{$comentario->publicacion->titulo}'.",
            TipoNotificacion::COMENTARIO,
            'foro'
        );
    }

    protected function crear(Usuario $usuario, string $titulo, string $descripcion, int $tipoNotificacionId, ?string $url = null, ?int $moduloId = null): void
    {
        if ($url && !str_starts_with($url, '/')) {
            $url = '/' . $url;
        }

        $this->notificacionService->crear([
            'usuario_id' => $usuario->id,
            'modulo_id' => $moduloId,
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'tipo_notificacion_id' => $tipoNotificacionId,
            'url' => $url
        ]);
    }

    public function inscripcionTorneoActualizada(EventoInscripcion $inscripcion): void
    {
        $usuario = optional($inscripcion->deportista)->usuario;

        if (!$usuario) return;

        $torneo = $inscripcion->evento;
        $estado = optional($inscripcion->estadoInscripcion)->nombre;

        $descripcion = $estado
            ? "Tu inscripción al torneo '{$torneo->nombre}' ha sido actualizada. Estado: {$estado}."
            : "Tu inscripción al torneo '{$torneo->nombre}' ha sido actualizada.";

        $this->crear(
            $usuario,
            Notificacion::INSCRIPCION_TORNEO,
            $descripcion,
            TipoNotificacion::SISTEMA,
            'torneos'
        );
    }
}
