<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Liga extends Model
{
    use HasFactory;

    protected $table = 'ligas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'presidente_id',
        'logo',
    ];

    public $timestamps = true;

    public function clubes()
    {
        return $this->hasMany(Club::class, 'liga_id');
    }

    public function presidente()
    {
        return $this->belongsTo(Usuario::class, 'presidente_id');
    }
}
