<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudClub extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_club';

    protected $fillable = [
        'solicitud_id',
        'liga_id',
        'nombre',
        'municipio_id',
        'direccion',
        'descripcion',
        'imagen_path',
        'documento_path',
    ];

    public $timestamps = true;

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id');
    }

    public function liga()
    {
        return $this->belongsTo(Liga::class, 'liga_id');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }
}
