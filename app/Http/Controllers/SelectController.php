<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Liga;
use App\Models\Club;
use App\Models\Ritmo;
use App\Models\Titulo;
use App\Models\Genero;
use App\Models\Usuario;
use App\Models\RedSocial;
use App\Models\Categoria;
use App\Models\TipoEvento;
use App\Models\EstadoEvento;
use App\Models\Nacionalidad;
use Illuminate\Http\Request;
use App\Models\TipoIdentificacion;
use Illuminate\Support\Facades\DB;

class SelectController extends Controller
{
    public function tipoEvento()
    {
        $tiposEvento = TipoEvento::all();
        return response()->json($tiposEvento);
    }

    public function tiposIdentificacion()
    {
        $tiposIdentificacion = TipoIdentificacion::all();
        return response()->json($tiposIdentificacion);
    }

    public function estadosEvento()
    {
        $estadosEvento = EstadoEvento::all();
        return response()->json($estadosEvento);
    }

    public function categoriasEvento()
    {
        $categoriasEvento = Categoria::all();
        return response()->json($categoriasEvento);
    }

    public function ritmosEvento()
    {
        $ritmosEvento = Ritmo::all();
        return response()->json($ritmosEvento);
    }

    public function tiposEvento()
    {
        $tiposEvento = TipoEvento::all();
        return response()->json($tiposEvento);
    }

    public function generos()
    {
        $generos = Genero::all();
        return response()->json($generos);
    }

    public function generosDeportista()
    {
        $generos = Genero::select('id', 'nombre')
                    ->where('nombre', '!=', 'Mixto')
                    ->get();
        return response()->json($generos);
    }

    public function clubes()
    {
        $clubes = Club::select('id','nombre')->get();
        return response()->json($clubes);
    }

    public function ligas()
    {
        $ligas = Liga::select('id','nombre')->get();
        return response()->json($ligas);
    }

    public function redesSociales()
    {
        $redesSociales = RedSocial::select('id','nombre')->get();
        return response()->json($redesSociales);
    }

    public function usuarios()
    {
        $usuarios = Usuario::select('id', DB::raw("CONCAT(nombre, ' ', apellido) AS nombre"))->get();
        return response()->json($usuarios);
    }

    public function usuariosDeportistas()
    {
        $rolId = Rol::where('nombre', 'Deportista')->value('id');
        $usuarios = Usuario::select('id', DB::raw("CONCAT(nombre, ' ', apellido) AS nombre"))
                    ->where('rol_id', $rolId)
                    ->get();
        return response()->json($usuarios);
    }

    public function nacionalidades()
    {
        $nacionalidades = Nacionalidad::select('id','nombre')->get();
        return response()->json($nacionalidades);
    }

    public function titulos()
    {
        $titulos = Titulo::select('id','nombre')->get();
        return response()->json($titulos);
    }
}
