<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionEntrenador extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones_entrenador';

    protected $fillable = [
        'entrenamiento_id', 'deportista_id',
        'entrenador_id', 'rendimiento', 'comentarios'
    ];

    public function entrenamiento()
    {
        return $this->belongsTo(Entrenamiento::class);
    }

    public function deportista()
    {
        return $this->belongsTo(Deportista::class);
    }

    public function entrenador()
    {
        return $this->belongsTo(Entrenador::class);
    }
}
