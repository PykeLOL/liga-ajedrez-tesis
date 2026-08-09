<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Deportista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChesstoolsController extends Controller
{
    public function show(int $fideId)
    {
        [$playerResponse, $ratingsResponse] = Http::pool(function ($pool) use ($fideId) {
            return [
                $pool->get(env('API_CHESSTOOLS_URL') . "/fide/player/$fideId"),
                $pool->get(env('API_CHESSTOOLS_URL') . "/fide/player/$fideId/ratings"),
            ];
        });

        if (!$playerResponse->successful() || !$ratingsResponse->successful()) {
            return response()->json([
                'message' => 'Error al consultar API externa'
            ], 500);
        }

        $data = $playerResponse->json();
        $dataHistorial = $ratingsResponse->json();

        $historial = $this->formatearHistorial($dataHistorial);
        $dataDeportista = Deportista::where('fide_id', $fideId)->first();
        if (!$dataDeportista) {
            $deportista = [
                'fide_id' => $data['id'] ?? null,
                'nombre' => $data['name'] ?? null,
                'fide_titulo' => $data['title'] ?? null,
                'año_nacimiento' => $data['year'] ?? null,
                'genero' => $data['gender'] ?? null,
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
            'elo_actual' => [
                'standard' => $data['standard'] ?? null,
                'rapid'    => $data['rapid'] ?? null,
                'blitz'    => $data['blitz'] ?? null,
            ],
            'data' => array_slice($historial, 0, 12),
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

    private function formatearHistorial(array $ratings): array
    {
        $historial = [];
        foreach (['standard', 'rapid', 'blitz'] as $tipo) {
            foreach ($ratings[$tipo] ?? [] as $valor) {
                $valor = str_pad((string)$valor, 10, '0', STR_PAD_LEFT);
                $historial[] = [
                    'tipo' => $tipo,
                    'anio' => substr($valor, 0, 4),
                    'mes'  => substr($valor, 4, 2),
                    'elo'  => (int) substr($valor, 6),
                ];
            }
        }

        usort($historial, function ($a, $b) {
            $fechaA = $a['anio'] . $a['mes'];
            $fechaB = $b['anio'] . $b['mes'];
            return strcmp($fechaB, $fechaA);
        });

        return $historial;
    }
}
