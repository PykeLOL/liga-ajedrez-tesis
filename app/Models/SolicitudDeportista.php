<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudDeportista extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_deportista';

    protected $fillable = [
        'solicitud_id',
        'club_id',
        'fecha_nacimiento',
        'genero_id',
        'nacionalidad_id',
        'fide_id',
        'documento_path',
    ];

    public $timestamps = true;

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function genero()
    {
        return $this->belongsTo(Genero::class, 'genero_id');
    }

    public function nacionalidad()
    {
        return $this->belongsTo(Nacionalidad::class, 'nacionalidad_id');
    }
}
