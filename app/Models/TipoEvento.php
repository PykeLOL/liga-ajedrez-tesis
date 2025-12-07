<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEvento extends Model
{
    use HasFactory;

    protected $table = 'tipos_evento';

    protected $fillable = [
        'nombre',
        'abreviacion',
        'descripcion'
    ];

    public function eventos()
    {
        return $this->hasMany(Evento::class);
    }
}
