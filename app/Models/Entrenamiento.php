<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrenamiento extends Model
{
    use HasFactory;

    protected $table = 'entrenamientos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'observaciones',
        'plan_entrenamiento_id',
        'club_id',
        'categoria_id',
        'genero_id',
        'entrenador_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'ubicacion',
        'url_mapa',
        'tipo_entrenamiento_id',
        'evento_id',
        'estado_entrenamiento_id',
        'generado_automaticamente',
    ];

    protected $casts = [
        'generado_automaticamente' => 'boolean',
    ];

    public $timestamps = true;

    public function planEntrenamiento()
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

    public function asistencias()
    {
        return $this->hasMany(EntrenamientoAsistencia::class);
    }

    public function deportistas()
    {
        return $this->belongsToMany(Deportista::class, 'entrenamiento_asistencias', 'entrenamiento_id', 'deportista_id')
                    ->withPivot([
                        'estado_asistencia_id',
                        'hora_llegada',
                        'observaciones',
                    ])->withTimestamps();
    }

    public function estado()
    {
        return $this->belongsTo(EstadoEntrenamiento::class, 'estado_entrenamiento_id');
    }

    public function googleEvents()
    {
        return $this->hasMany(EntrenamientoGoogleEvent::class);
    }
}
