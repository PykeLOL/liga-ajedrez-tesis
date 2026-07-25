<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanEntrenamientoHorario extends Model
{
    use HasFactory;

    protected $table = 'planes_entrenamiento_horarios';

    protected $fillable = [
        'plan_entrenamiento_id',
        'dia_semana_id',
        'hora_inicio',
        'hora_fin'
    ];

    public $timestamps = true;

    public function planEntrenamiento()
    {
        return $this->belongsTo(PlanEntrenamiento::class, 'plan_entrenamiento_id');
    }

    public function diaSemana()
    {
        return $this->belongsTo(DiaSemana::class, 'dia_semana_id');
    }
}
