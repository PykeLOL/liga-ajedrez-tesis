<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ritmo extends Model
{
    use HasFactory;

    protected $table = 'ritmos';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public $timestamps = true;

    public function categoriasEntrenador()
    {
        return $this->hasMany(CategoriaEntrenador::class, 'ritmo_id');
    }

    public function entrenadores()
    {
        return $this->belongsToMany(Entrenador::class, 'categorias_entrenador', 'ritmo_id', 'entrenador_id')
                    ->withPivot(['categoria_id', 'estado'])
                    ->withTimestamps();
    }
}
