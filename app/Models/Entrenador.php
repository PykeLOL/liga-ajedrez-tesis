<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrenador extends Model
{
    use HasFactory;

    protected $table = 'entrenadores';

    protected $fillable = [
        'usuario_id',
        'club_id',
        'fecha_nacimiento',
        'genero_id',
        'nacionalidad_id',
        'experiencia_anios',
        'especialidad',
        'fide_id',
        'estado',
    ];

    public $timestamps = true;

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function certificaciones()
    {
        return $this->hasMany(CertificacionEntrenador::class, 'entrenador_id');
    }

    public function genero()
    {
        return $this->belongsTo(Genero::class, 'genero_id');
    }

    public function nacionalidad()
    {
        return $this->belongsTo(Nacionalidad::class, 'nacionalidad_id');
    }

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'categorias_entrenador', 'entrenador_id', 'categoria_id')
                    ->withPivot('estado')
                    ->withTimestamps();
    }

    public function ritmos()
    {
        return $this->belongsToMany(Ritmo::class, 'categorias_entrenador', 'entrenador_id', 'ritmo_id')
                    ->withPivot('categoria_id', 'estado')
                    ->withTimestamps();
    }
}
