<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    use HasFactory;

    protected $table = 'generos';

    protected $fillable = [
        'nombre',
    ];

    public $timestamps = true;

    public function entrenadores()
    {
        return $this->hasMany(Entrenador::class, 'genero_id');
    }

    public function deportistas()
    {
        return $this->hasMany(Deportista::class, 'genero_id');
    }
}
