<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionEntrenamiento extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones_entrenamiento';

    protected $fillable = [
        'entrenamiento_id', 'deportista_id',
        'calificacion', 'comentarios'
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
