<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    public const SOLICITUD_CLUB = 'Nueva solicitud de club';
    public const SOLICITUD_DEPORTISTA = 'Nueva solicitud de deportista';
    public const SOLICITUD_APROBADA = 'Solicitud aprobada';
    public const SOLICITUD_RECHAZADA = 'Solicitud rechazada';
    public const ENTRENAMIENTO = 'Nuevo entrenamiento';
    public const COMENTARIO_FORO ='Nuevo comentario';
    public const REACCION_FORO ='Nueva reacción';
    public const ACTIVIDAD_FORO ='Nueva actividad';
    public const PLAN_ENTRENAMIENTO = 'Plan de entrenamiento';
    public const INSCRIPCION_TORNEO = 'inscripcion_torneo';

    protected $fillable = [
        'usuario_id',
        'modulo_id',
        'titulo',
        'descripcion',
        'tipo_notificacion_id',
        'url',
        'leida',
        'fecha_lectura'
    ];

    public $timestamps = true;

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'modulo_id');
    }

    public function tipoNotificacion()
    {
        return $this->belongsTo(TipoNotificacion::class, 'tipo_notificacion_id');
    }
}
