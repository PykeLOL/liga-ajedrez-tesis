<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicacionComentario extends Model
{
    use HasFactory;

    protected $table = 'publicacion_comentarios';

    protected $fillable = [
        'publicacion_id',
        'usuario_id',
        'comentario',
        'imagen_path'
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

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function reacciones()
    {
        return $this->hasMany(PublicacionComentarioReaccion::class, 'publicacion_comentario_id');
    }
}
