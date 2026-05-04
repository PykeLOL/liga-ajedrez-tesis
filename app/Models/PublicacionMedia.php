<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicacionMedia extends Model
{
    use HasFactory;

    protected $table = 'publicacion_media';

    protected $fillable = [
        'publicacion_id',
        'tipo',
        'path',
        'descripcion',
        'orden',
    ];

    public $timestamps = true;

    public function publicacion()
    {
        return $this->belongsTo(Publicacion::class, 'publicacion_id');
    }
}
