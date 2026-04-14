<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    use HasFactory;

    protected $table = 'parametros';

    protected $fillable = [
        'liga_id',
        'nombre',
        'valor'
    ];

    public $timestamps = true;

    public function liga()
    {
        return $this->belongsTo(Liga::class);
    }
}
