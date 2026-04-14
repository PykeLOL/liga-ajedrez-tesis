<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoAsistencia extends Model
{
    use HasFactory;

    protected $table = 'eventos_asistencia';

    protected $fillable = [
        'evento_id',
        'usuario_id'
    ];

    public $timestamps = true;

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
