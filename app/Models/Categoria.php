<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'edad_minima',
        'edad_maxima',
        'descripcion',
        'activo',
    ];

    public $timestamps = true;

    public function deportistas()
    {
        return $this->hasMany(Deportista::class, 'categoria_id');
    }
}
