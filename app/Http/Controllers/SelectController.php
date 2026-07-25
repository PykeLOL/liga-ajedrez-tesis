<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Liga;
use App\Models\Club;
use App\Models\Ritmo;
use App\Models\Evento;
use App\Models\Titulo;
use App\Models\Modulo;
use App\Models\Genero;
use App\Models\Usuario;
use App\Models\RedSocial;
use App\Models\Categoria;
use App\Models\Entrenador;
use App\Models\Deportista;
use App\Models\TipoAccion;
use App\Models\TipoEvento;
use App\Models\EstadoEvento;
use App\Models\Nacionalidad;
use Illuminate\Http\Request;
use App\Models\PlanEntrenamiento;
use App\Models\TipoEntrenamiento;
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

    public function tiposAccion()
    {
        $tiposAccion = TipoAccion::select('id','nombre')->get();
        return response()->json($tiposAccion);
    }

    public function modulos()
    {
        $modulos = Modulo::select('id','nombre')->get();
        return response()->json($modulos);
    }

    public function categorias()
    {
        $categorias = Categoria::select('id','nombre')->get();
        return response()->json($categorias);
    }

    public function tiposEntrenamiento()
    {
        $tiposEntrenamiento = TipoEntrenamiento::select('id','nombre')->get();
        return response()->json($tiposEntrenamiento);
    }

    public function entrenadores()
    {
        $entrenadores = Entrenador::with('usuario:id,nombre,apellido')
            ->get()
            ->map(function ($entrenador) {
                return [
                    'id' => $entrenador->id,
                    'nombre' => $entrenador->usuario->nombre_completo,
                ];
            });

        return response()->json($entrenadores);
    }

    public function planesEntrenamiento()
    {
        $planesEntrenamiento = PlanEntrenamiento::select('id','nombre')->get();
        return response()->json($planesEntrenamiento);
    }

    public function eventos()
    {
        $eventos = Evento::select('id','nombre')->get();
        return response()->json($eventos);
    }

    public function deportistas(Request $request)
    {
        $query = Deportista::query()
                ->join('usuarios', 'usuarios.id', '=', 'deportistas.usuario_id')
                ->leftJoin('clubes', 'clubes.id', '=', 'deportistas.club_id')
                ->leftJoin('titulos', 'titulos.id', '=', 'deportistas.titulo_id')
                ->leftJoin('categorias', 'categorias.id', '=', 'deportistas.categoria_id')
                ->select(
                    'deportistas.id',
                    'usuarios.nombre',
                    'usuarios.apellido',
                    'usuarios.numero_identificacion',
                    'titulos.abreviacion as titulo',
                    'categorias.nombre as categoria'
                );

        if ($request->has('club_id') && !empty($request->club_id)) {
            $query->where('deportistas.club_id', $request->club_id);
        }

        $deportistas = $query
            ->orderBy('usuarios.nombre')
            ->orderBy('usuarios.apellido')
            ->get()
            ->map(function ($deportista) {
                return [
                    'id' => $deportista->id,
                    'nombre' => trim($deportista->nombre.' '.$deportista->apellido),
                    'numero_identificacion' => $deportista->numero_identificacion,
                    'titulo' => $deportista->titulo,
                    'categoria' => $deportista->categoria
                ];
            });

        return response()->json($deportistas);
    }
}
