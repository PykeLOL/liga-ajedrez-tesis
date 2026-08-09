<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicacionComentarioReaccion extends Model
{
    use HasFactory;

    protected $table = 'publicacion_comentario_reaccion';

    protected $fillable = [
        'publicacion_comentario_id',
        'usuario_id',
        'reaccion_id',
    ];

    public $timestamps = true;

    public function comentario()
    {
        return $this->belongsTo(PublicacionComentario::class, 'publicacion_comentario_id');
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
