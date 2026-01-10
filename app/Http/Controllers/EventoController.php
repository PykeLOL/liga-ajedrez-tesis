<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\TipoEvento;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function index()
    {
        $eventos = Evento::all();
        return response()->json($eventos);
    }

    public function tipoEventos()
    {
        $tiposEvento = TipoEvento::all();
        return response()->json($tiposEvento);
    }

    public function getEventosPorTipo($id)
    {
        $eventos = Evento::where('tipo_evento_id', $id)->get();
        return response()->json($eventos);
    }
}
