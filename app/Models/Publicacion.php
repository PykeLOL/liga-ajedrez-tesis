<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publicacion extends Model
{
    use HasFactory;

    protected $table = 'publicaciones';

    protected $fillable = [
        'liga_id',
        'usuario_id',
        'titulo',
        'contenido',
        'fecha',
    ];

    public $timestamps = true;

    public function getFechaAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function liga()
    {
        return $this->belongsTo(Liga::class, 'liga_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function media()
    {
        return $this->hasMany(PublicacionMedia::class, 'publicacion_id');
    }

    public function comentarios()
    {
        return $this->hasMany(PublicacionComentario::class, 'publicacion_id');
    }
}
