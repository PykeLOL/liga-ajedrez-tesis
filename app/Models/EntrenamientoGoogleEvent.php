<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntrenamientoGoogleEvent extends Model
{
    protected $fillable = [
        'usuario_id',
        'entrenamiento_id',
        'google_event_id',
    ];

    public function user()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function entrenamiento()
    {
        return $this->belongsTo(Entrenamiento::class, 'entrenamiento_id');
    }
}
