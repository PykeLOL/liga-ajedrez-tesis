<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reaccion extends Model
{
    use HasFactory;

    protected $table = 'reacciones';

    protected $fillable = [
        'nombre',
        'icono',
    ];

    public $timestamps = true;

    public function reaccionesComentario()
    {
        return $this->hasMany(PublicacionComentarioReaccion::class, 'reaccion_id');
    }
}
