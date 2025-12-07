<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nacionalidad extends Model
{
    use HasFactory;

    protected $table = 'nacionalidades';

    protected $fillable = [
        'nombre',
        'codigo'
    ];

    public $timestamps = true;

    public function entrenadores()
    {
        return $this->hasMany(Entrenador::class, 'nacionalidad_id');
    }

    public function deportistas()
    {
        return $this->hasMany(Deportista::class, 'nacionalidad_id');
    }
}
