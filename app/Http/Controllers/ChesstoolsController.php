<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Deportista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChesstoolsController extends Controller
{
    public function show($fideId)
    {
        $url = env('API_CHESSTOOLS_URL') . "/fide/player_info/?fide_id={$fideId}&history=true";
        $response = Http::get($url);
        if (!$response->successful()) {
            return response()->json([
                'message' => 'Error al consultar API externa'
            ], 500);
        }

        $data = $response->json();
        $dataDeportista = Deportista::where('fide_id', $fideId)->first();
        if (!$dataDeportista) {
            $deportista = [
                'fide_id' => $data['fide_id'] ?? null,
                'nombre' => $data['name'] ?? null,
                'fide_titulo' => $data['fide_title'] ?? null,
                'año_nacimiento' => $data['birth_year'] ?? null,
                'genero' => $data['sex'] ?? null,
                'nacionalidad' => $data['federation'] ?? null,
            ];

            $message = 'Deportista no registrado en el sistema';
            $registrado = false;
        } else {
            $deportista = [
                'fide_id' => $dataDeportista->fide_id,
                'nombre' => optional($dataDeportista->usuario)->nombre . ' ' . optional($dataDeportista->usuario)->apellido,
                'fide_titulo' => optional($dataDeportista->titulo)->nombre ?? 'Sin título',
                'año_nacimiento' => $dataDeportista->fecha_nacimiento
                    ? Carbon::parse($dataDeportista->fecha_nacimiento)->format('Y')
                    : null,
                'genero' => optional($dataDeportista->genero)->nombre,
                'nacionalidad' => optional($dataDeportista->nacionalidad)->nombre,
            ];
            $message = 'Deportista registrado';
            $registrado = true;
        }

        return response()->json([
            'data' => array_slice($data['history'] ?? [], 0, 12),
            'deportista' => $deportista,
            'registrado' => $registrado,
            'club' => $dataDeportista && $dataDeportista->club
                ? [
                    'id' => $dataDeportista->club->id,
                    'nombre' => $dataDeportista->club->nombre,
                ]
                : [
                    'id' => null,
                    'nombre' => 'Sin club',
                ],
            'message' => $message
        ]);
    }
}
