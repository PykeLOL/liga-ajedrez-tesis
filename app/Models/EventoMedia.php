<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventoMedia extends Model
{
    use HasFactory;

    protected $table = 'evento_media';

    protected $fillable = [
        'evento_id',
        'tipo',
        'path',
        'descripcion',
        'orden',
    ];

    public $timestamps = true;

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }
}
