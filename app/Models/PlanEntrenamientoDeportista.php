<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanEntrenamientoDeportista extends Model
{
    use HasFactory;

    protected $table = 'planes_entrenamiento_deportistas';

    protected $fillable = [
        'plan_entrenamiento_id',
        'deportista_id',
        'estado'
    ];

    public $timestamps = true;

    public function planEntrenamiento()
    {
        return $this->belongsTo(PlanEntrenamiento::class, 'plan_entrenamiento_id');
    }

    public function deportista()
    {
        return $this->belongsTo(Deportista::class);
    }
}
