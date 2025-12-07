<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrenamiento extends Model
{
    use HasFactory;

    protected $table = 'entrenamientos';

    protected $fillable = [
        'plan_entrenamiento_id', 'club_id', 'categoria_id',
        'genero_id', 'entrenador_id', 'fecha',
        'hora_inicio', 'hora_fin', 'ubicacion',
        'url_mapa', 'tipo_entrenamiento_id',
        'evento_id', 'descripcion'
    ];

    public function plan()
    {
        return $this->belongsTo(PlanEntrenamiento::class, 'plan_entrenamiento_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);

    }
    public function genero()
    {
        return $this->belongsTo(Genero::class);
    }

    public function entrenador()
    {
        return $this->belongsTo(Entrenador::class);
    }

    public function tipo()
    {
        return $this->belongsTo(TipoEntrenamiento::class, 'tipo_entrenamiento_id');
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function asistencia()
    {
        return $this->hasMany(EntrenamientoDeportista::class);
    }

    public function evaluacionesDeportistas()
    {
        return $this->hasMany(EvaluacionEntrenamiento::class);
    }

    public function evaluacionesEntrenador()
    {
        return $this->hasMany(EvaluacionEntrenador::class);
    }
}
