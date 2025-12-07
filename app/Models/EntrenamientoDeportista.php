<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntrenamientoDeportista extends Model
{
    use HasFactory;

    protected $table = 'entrenamiento_deportista';

    protected $fillable = [
        'entrenamiento_id', 'deportista_id',
        'asistio', 'hora_llegada', 'observaciones'
    ];

    public function entrenamiento()
    {
        return $this->belongsTo(Entrenamiento::class);
    }

    public function deportista()
    {
        return $this->belongsTo(Deportista::class);
    }
}
