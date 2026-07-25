<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanEntrenamiento extends Model
{
    use HasFactory;

    protected $table = 'planes_entrenamiento';

    protected $fillable = [
        'club_id',
        'categoria_id',
        'genero_id',
        'entrenador_id',
        'tipo_entrenamiento_id',
        'nombre',
        'descripcion',
        'ubicacion',
        'url_mapa',
        'fecha_inicio',
        'fecha_fin',
        'evento_id',
        'estado_plan_id',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public $timestamps = true;

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

    public function estado()
    {
        return $this->belongsTo(EstadoPlan::class, 'estado_plan_id');
    }

    public function horarios()
    {
        return $this->hasMany(PlanEntrenamientoHorario::class);
    }

    public function deportistas()
    {
        return $this->belongsToMany(
            Deportista::class,
            'planes_entrenamiento_deportistas'
        )->withPivot('estado')
         ->withTimestamps();
    }

    public function entrenamientos()
    {
        return $this->hasMany(Entrenamiento::class);
    }
}
