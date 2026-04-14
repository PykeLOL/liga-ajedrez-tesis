<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventoDocumento extends Model
{
    use HasFactory;

    protected $table = 'evento_documentos';

    protected $fillable = [
        'evento_id',
        'nombre',
        'path',
        'tipo',
        'descripcion',
        'orden',
    ];

    public $timestamps = true;

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }
}
