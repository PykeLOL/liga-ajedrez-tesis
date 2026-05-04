<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    use HasFactory;

    protected $table = 'departamentos';

    protected $fillable = [
        'nombre',
        'pais_id',
    ];

    public $timestamps = true;

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }
}
