<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanEntrenamiento extends Model
{
    use HasFactory;

    protected $table = 'planes_entrenamiento';

    protected $fillable = [
        'club_id', 'categoria_id', 'genero_id',
        'entrenador_id', 'nombre', 'descripcion',
        'fecha_inicio', 'fecha_fin', 'evento_id'
    ];

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

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function horarios()
    {
        return $this->hasMany(PlanEntrenamientoHorario::class);
    }

    public function deportistas()
    {
        return $this->belongsToMany(Deportista::class, 'plan_deportistas')
            ->withPivot('estado')
            ->withTimestamps();
    }

    public function entrenamientos()
    {
        return $this->hasMany(Entrenamiento::class);
    }
}
