<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Titulo extends Model
{
    use HasFactory;

    protected $table = 'titulos';

    protected $fillable = [
        'nombre',
        'abreviacion',
        'es_fide'
    ];

    public $timestamps = true;

    public function deportistas()
    {
        return $this->hasMany(Deportista::class, 'titulo_id');
    }
}
