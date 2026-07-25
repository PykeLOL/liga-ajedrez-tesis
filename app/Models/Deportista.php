<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Deportista extends Model
{
    use HasFactory;

    protected $table = 'deportistas';

    protected $fillable = [
        'usuario_id',
        'club_id',
        'categoria_id',
        'fecha_nacimiento',
        'genero_id',
        'nacionalidad_id',
        'elo_nacional',
        'elo_internacional',
        'fide_id',
        'titulo_id',
        'documento_path',
        'estado',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public $timestamps = true;

    public function getFechaNacimientoAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function genero()
    {
        return $this->belongsTo(Genero::class, 'genero_id');
    }

    public function nacionalidad()
    {
        return $this->belongsTo(Nacionalidad::class, 'nacionalidad_id');
    }

    public function titulo()
    {
        return $this->belongsTo(Titulo::class, 'titulo_id');
    }

    public function eloHistorico()
    {
        return $this->hasMany(EloHistorico::class);
    }

    public function estadistica()
    {
        return $this->hasOne(EstadisticaDeportista::class);
    }

    public function entrenamientos()
    {
        return $this->belongsToMany(Entrenamiento::class, 'entrenamiento_asistencias', 'deportista_id', 'entrenamiento_id')
            ->withPivot('asistio', 'observaciones', 'hora_llegada')
            ->withTimestamps();
    }

    public function inscripciones()
    {
        return $this->hasMany(EventoInscripcion::class, 'deportista_id');
    }

    public function getEloMaximoAttribute()
    {
        if($this->elo_internacional >= $this->elo_nacional) {
            return $this->elo_internacional;
        } else {
            return $this->elo_nacional;
        }
    }
}
