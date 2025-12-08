<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrenamiento;

class EntrenamientoController extends Controller
{
    public function index()
    {
        $entrenamientos = Entrenamiento::with('club', 'categoria', 'genero', 'entrenador', 'deportistas', 'deportistas.usuario', 'deportista.titulo')->get();
        $entrenamientos = $entrenamientos->map(function ($entrenamiento) {
            return [
                'id' => $entrenamiento->id,
                'club' => $entrenamiento->club->nombre,
                'categoria' => $entrenamiento->categoria->nombre,
                'genero' => $entrenamiento->genero->nombre,
                'entrenador' => $entrenamiento->entrenador->usuario->nombre . ' ' . $entrenamiento->entrenador->usuario->apellido,
                'fecha' => $entrenamiento->fecha,
                'hora_inicio' => $entrenamiento->hora_inicio,
                'hora_fin' => $entrenamiento->hora_fin,
                'ubicacion' => $entrenamiento->ubicacion,
                'url_mapa' => $entrenamiento->url_mapa,
                'tipo_entrenamiento' => $entrenamiento->tipo->nombre,
                'deportistas' => $entrenamiento->deportistas->map(function ($deportista) {
                    return [
                        'id' => $deportista->id,
                        'nombre' => $deportista->usuario->nombre,
                        'apellido' => $deportista->usuario->apellido,
                        'numero_identificacion' => $deportista->usuario->numero_identificacion,
                        'titulo' => $deportista->titulo->abreviacion,
                    ];
                }),
            ];
        });

        return response()->json($entrenamientos);
    }
}
