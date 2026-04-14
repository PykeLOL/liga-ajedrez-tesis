<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EloHistorico extends Model
{
    use HasFactory;

    protected $table = 'elo_historico';

    protected $fillable = [
        'deportista_id',
        'periodo',
        'clasico_elo',
        'clasico_juegos',
        'rapido_elo',
        'rapido_juegos',
        'blitz_elo',
        'blitz_juegos',
        'fecha',
    ];

    public $timestamps = true;

    public function deportista()
    {
        return $this->belongsTo(Deportista::class);
    }
}
