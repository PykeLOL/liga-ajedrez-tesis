<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoEvento extends Model
{
    use HasFactory;

    protected $table = 'tipos_evento';

    protected $fillable = [
        'nombre',
        'abreviacion',
        'descripcion',
        'slug',
        'icono'
    ];

    public $timestamps = true;

    public function eventos()
    {
        return $this->hasMany(Evento::class);
    }

    protected static function booted()
    {
        static::creating(function ($tipo) {
            $tipo->slug = Str::slug($tipo->nombre);
        });
    }
}
