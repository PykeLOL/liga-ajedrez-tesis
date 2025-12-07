<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanDeportista extends Model
{
    use HasFactory;

    protected $table = 'plan_deportistas';

    protected $fillable = [
        'plan_entrenamiento_id',
        'deportista_id',
        'estado'
    ];

    public function plan()
    {
        return $this->belongsTo(PlanEntrenamiento::class, 'plan_entrenamiento_id');
    }

    public function deportista()
    {
        return $this->belongsTo(Deportista::class);
    }
}
