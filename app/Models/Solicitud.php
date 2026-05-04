<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'tipo',
        'estado',
        'comentario',
        'usuario_id',
    ];

    public $timestamps = true;

    const TIPO_CLUB = 'club';
    const TIPO_DEPORTISTA = 'deportista';

    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_APROBADA = 'aprobada';
    const ESTADO_RECHAZADA = 'rechazada';

    const ESTADOS = [
        self::ESTADO_APROBADA,
        self::ESTADO_RECHAZADA
    ];

    const TIPOS = [
        self::TIPO_CLUB,
        self::TIPO_DEPORTISTA
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function club()
    {
        return $this->hasOne(SolicitudClub::class, 'solicitud_id');
    }

    public function deportista()
    {
        return $this->hasOne(SolicitudDeportista::class, 'solicitud_id');
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}
