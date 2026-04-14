<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoRedSocial extends Model
{
    use HasFactory;

    protected $table = 'evento_redes_sociales';

    protected $fillable = [
        'evento_id',
        'red_social_id',
        'orden',
        'url'
    ];

    public $timestamps = true;

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function redSocial()
    {
        return $this->belongsTo(RedSocial::class);
    }
}
