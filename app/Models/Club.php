<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;

    protected $table = 'clubes';

    protected $fillable = [
        'liga_id',
        'nombre',
        'ubicacion',
        'presidente_id',
        'contacto',
        'logo',
    ];

    public $timestamps = true;

    public function liga()
    {
        return $this->belongsTo(Liga::class, 'liga_id');
    }

    public function presidente()
    {
        return $this->belongsTo(Usuario::class, 'presidente_id');
    }
}
