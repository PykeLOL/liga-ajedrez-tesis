<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoNotificacion extends Model
{
    use HasFactory;

    protected $table = 'tipos_notificaciones';

    public const APROBACION = 1;
    public const RECHAZO = 2;
    public const COMENTARIO = 3;
    public const EVENTO = 4;
    public const ENTRENAMIENTO = 5;
    public const SISTEMA = 6;

    public const APROBACION_NOMBRE = 'Aprobación';
    public const RECHAZO_NOMBRE = 'Rechazo';
    public const COMENTARIO_NOMBRE = 'Comentario';
    public const EVENTO_NOMBRE = 'Evento';
    public const ENTRENAMIENTO_NOMBRE = 'Entrenamiento';
    public const SISTEMA_NOMBRE = 'Sistema';

    protected $fillable = [
        'nombre',
        'icono'
    ];

    public $timestamps = true;

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'tipo_notificacion_id');
    }
}
