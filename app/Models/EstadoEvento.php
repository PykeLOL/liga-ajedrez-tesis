<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstadoEvento extends Model
{
    use HasFactory;

    protected $table = 'estados_evento';

    protected $fillable = ['nombre'];

    public function eventos()
    {
        return $this->hasMany(Evento::class);
    }
}
