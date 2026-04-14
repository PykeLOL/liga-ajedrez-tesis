<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoOrganizador extends Model
{
    use HasFactory;

    protected $table = 'evento_organizadores';

    protected $fillable = [
        'evento_id',
        'club_id'
    ];

    public $timestamps = true;

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
