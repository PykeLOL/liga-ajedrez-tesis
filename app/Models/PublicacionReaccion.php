<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PublicacionReaccion extends Model
{
    protected $table = 'publicacion_reacciones';

    protected $fillable = [
        'publicacion_id',
        'usuario_id',
        'reaccion_id',
    ];

    public $timestamps = true;

    public function publicacion()
    {
        return $this->belongsTo(Publicacion::class, 'publicacion_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function reaccion()
    {
        return $this->belongsTo(Reaccion::class, 'reaccion_id');
    }
}
