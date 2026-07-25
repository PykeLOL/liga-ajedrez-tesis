<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntrenamientoAsistencia extends Model
{
    use HasFactory;

    protected $table = 'entrenamiento_asistencias';

    protected $fillable = [
        'entrenamiento_id',
        'deportista_id',
        'estado_asistencia_id',
        'hora_llegada',
        'observaciones',
    ];

    public $timestamps = true;

    public function entrenamiento()
    {
        return $this->belongsTo(Entrenamiento::class);
    }

    public function deportista()
    {
        return $this->belongsTo(Deportista::class);
    }

    public function estado()
    {
        return $this->belongsTo(EstadoAsistencia::class, 'estado_asistencia_id');
    }
}
