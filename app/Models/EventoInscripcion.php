<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventoInscripcion extends Model
{
    use HasFactory;

    protected $table = 'evento_inscripciones';

    protected $fillable = [
        'evento_categoria_id',
        'deportista_id',
        'fecha_inscripcion',
        'pago',
        'comprobante_path',
        'valor_pagado',
        'referencia_pago',
        'estado_inscripcion_id',
    ];

    public $timestamps = true;

    public function eventoCategoria()
    {
        return $this->belongsTo(EventoCategoria::class, 'evento_categoria_id');
    }

    public function deportista()
    {
        return $this->belongsTo(Deportista::class);
    }

    public function estadoInscripcion()
    {
        return $this->belongsTo(EstadoInscripcion::class, 'estado_inscripcion_id');
    }
}
