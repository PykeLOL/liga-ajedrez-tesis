<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Titulo;
use App\Models\Usuario;
use App\Models\Categoria;
use App\Models\Deportista;
use App\Traits\FiltraPorRol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DeportistaController extends Controller
{
    use FiltraPorRol;

    public function index()
    {
        $deportistas = $this->filtrarPorClub(Deportista::with([
            'genero',
            'usuario',
            'club',
            'categoria',
            'nacionalidad',
            'usuario.tipoIdentificacion',
            'titulo'
        ]))->get();

        $deportistas = $deportistas->map(function ($deportista) {
            return [
                'id' => $deportista->id,
                'foto_perfil' => $deportista->usuario->imagen_path ? '/storage/'.$deportista->usuario->imagen_path : asset('img/deportistas/default.jpg'),
                'fecha_nacimiento' => $deportista->fecha_nacimiento,
                'genero' => $deportista->genero->nombre,
                'nacionalidad' => $deportista->nacionalidad->codigo,
                'tipo_identificacion' => $deportista->usuario->tipoIdentificacion->abreviacion,
                'numero_identificacion' => $deportista->usuario->numero_identificacion,
                'elo_nacional' => $deportista->elo_nacional,
                'elo_internacional' => $deportista->elo_internacional,
                'fide_id' => $deportista->fide_id,
                'titulo' => $deportista->titulo ? $deportista->titulo->nombre : null,
                'estado' => $deportista->estado,

                'usuario' => [
                    'nombre' => $deportista->usuario->nombre,
                    'apellido' => $deportista->usuario->apellido,
                    'email' => $deportista->usuario->email,
                    'telefono' => $deportista->usuario->telefono,
                ],

                'club' => [
                    'nombre' => $deportista->club->nombre,
                    'descripcion' => $deportista->club->descripcion,
                    'logo' => $deportista->club->logo ? '/storage/'.$deportista->club->logo : asset('img/clubes/default.jpg'),
                ],

                'categoria' => [
                    'nombre' => $deportista->categoria->nombre,
                    'edad_minima' => $deportista->categoria->edad_minima,
                    'edad_maxima' => $deportista->categoria->edad_maxima,
                    'descripcion' => $deportista->categoria->descripcion,
                    'activo' => $deportista->categoria->activo,
                ]
            ];
        });

        return response()->json($deportistas);
    }

    public function show(int $id)
    {
        $deportista = $this->filtrarPorClub(Deportista::with([
            'genero',
            'usuario',
            'club',
            'categoria',
            'nacionalidad',
            'usuario.tipoIdentificacion',
            'titulo'
        ]))->find($id);

        if (!$deportista) {
            return response()->json([
                'message' => 'Deportista no encontrado'
            ], 404);
        }

        return response()->json($deportista);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usuario_id' => 'required|numeric|exists:usuarios,id|unique:deportistas,usuario_id',
            'club_id' => 'nullable|numeric|exists:clubes,id',
            'fecha_nacimiento' => 'required|date',
            'genero_id' => 'required|numeric|exists:generos,id',
            'nacionalidad_id' => 'required|numeric|exists:nacionalidades,id',
            'elo_nacional' => 'required|integer|min:0',
            'elo_internacional' => 'nullable|integer|min:0',
            'fide_id' => 'nullable|string|max:50|unique:deportistas,fide_id',
            'titulo_id' => 'nullable|numeric|exists:titulos,id',
            'documento' => 'nullable|file|mimes:pdf|max:10000',
        ], [
            'usuario_id.unique' => 'El usuario seleccionado ya está asociado a un deportista.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $usuario = Usuario::find($validated['usuario_id']);
        if ($usuario->rol->nombre !== 'Deportista') {
            return response()->json([
                'errors' => [
                    'usuario_id' => ['El usuario seleccionado no tiene el rol de Deportista.']
                ]
            ], 422);
        }

        $edad = Carbon::parse($validated['fecha_nacimiento'])->age;
        $categoria = Categoria::where('nombre', '!=', 'Libre')
            ->where('edad_minima', '<=', $edad)
            ->where('edad_maxima', '>=', $edad)
            ->first();

        $tituloId = $validated['titulo_id']
            ?? Titulo::where('abreviacion', 'ST')->value('id');

        $documentoPath = null;
        if ($request->hasFile('documento')) {
            $documento = $request->file('documento');
            $documentoPath = $documento->store('deportistas/documentos', 'public');
        }

        $deportista = Deportista::create([
            'usuario_id' => $validated['usuario_id'],
            'club_id' => $validated['club_id'] ?? null,
            'categoria_id' => $categoria ? $categoria->id : null,
            'fecha_nacimiento' => $validated['fecha_nacimiento'],
            'genero_id' => $validated['genero_id'],
            'nacionalidad_id' => $validated['nacionalidad_id'],
            'elo_nacional' => $validated['elo_nacional'],
            'elo_internacional' => $validated['elo_internacional'] ?? 0,
            'fide_id' => $validated['fide_id'] ?? null,
            'titulo_id' => $tituloId,
            'documento_path' => $documentoPath,
            'estado' => true,
        ]);

        return response()->json([
            'message' => 'Deportista creado exitosamente',
            'deportista' => $deportista
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $deportista = $this->filtrarPorClub(Deportista::query())->find($id);
        if (!$deportista) {
            return response()->json([
                'message' => 'Deportista no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'club_id' => 'nullable|numeric|exists:clubes,id',
            'fecha_nacimiento' => 'required|date',
            'genero_id' => 'required|numeric|exists:generos,id',
            'nacionalidad_id' => 'required|numeric|exists:nacionalidades,id',
            'elo_nacional' => 'required|integer|min:0',
            'elo_internacional' => 'nullable|integer|min:0',
            'fide_id' => 'nullable|string|max:50|unique:deportistas,fide_id,' . $id,
            'titulo_id' => 'nullable|numeric|exists:titulos,id',
            'documento' => 'nullable|file|mimes:pdf|max:10000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $edad = Carbon::parse($validated['fecha_nacimiento'])->age;
        $categoria = Categoria::where('nombre', '!=', 'Libre')
            ->where('edad_minima', '<=', $edad)
            ->where('edad_maxima', '>=', $edad)
            ->first();

        $tituloId = $validated['titulo_id']
            ?? Titulo::where('abreviacion', 'ST')->value('id');

        $deportista->update([
            'club_id' => $validated['club_id'] ?? null,
            'categoria_id' => $categoria ? $categoria->id : null,
            'fecha_nacimiento' => $validated['fecha_nacimiento'],
            'genero_id' => $validated['genero_id'],
            'nacionalidad_id' => $validated['nacionalidad_id'],
            'elo_nacional' => $validated['elo_nacional'],
            'elo_internacional' => $validated['elo_internacional'] ?? 0,
            'fide_id' => $validated['fide_id'] ?? null,
            'titulo_id' => $tituloId,
        ]);

        if ($request->hasFile('documento')) {
            if ($deportista->documento_path) {
                Storage::disk('public')->delete($deportista->documento_path);
            }
            $documento = $request->file('documento');
            $documentoPath = $documento->store('deportistas/documentos', 'public');
            $deportista->update(['documento_path' => $documentoPath]);
        }

        return response()->json([
            'message' => 'Deportista actualizado correctamente',
            'deportista' => $deportista
        ], 200);
    }

    public function destroy(int $id)
    {
        $deportista = $this->filtrarPorClub(Deportista::query())->find($id);
        if (!$deportista) {
            return response()->json(['message' => 'Deportista no encontrado'], 404);
        }

        $deportista->delete();

        return response()->json(['message' => 'Deportista eliminado correctamente'], 200);
    }

    public function actualizarCategoria(int $id)
    {
        $deportista = Deportista::find($id);
        if (!$deportista) {
            return response()->json(['message' => 'Deportista no encontrado'], 404);
        }

        $edad = Carbon::parse($deportista->fecha_nacimiento)->age;

        $categoria = Categoria::where('nombre', '!=', 'Libre')
            ->where('edad_minima', '<=', $edad)
            ->where('edad_maxima', '>=', $edad)
            ->first();

        if (!$categoria) {
            return response()->json(['message' => 'No se encontró una categoría adecuada para la edad del deportista'], 404);
        }

        $deportista->categoria_id = $categoria->id;
        $deportista->save();

        return response()->json([
            'message' => 'Categoría del deportista actualizada correctamente',
            'deportista' => $deportista
        ], 200);
    }

    public function sincronizarFide(int $fideId)
    {
        $url = env('API_CHESSTOOLS_URL') . "/fide/player/$fideId";
        $response = Http::get($url);
        if (!$response->successful()) {
            return response()->json([
                'message' => 'No se encontró información en FIDE'
            ], 404);
        }

        $data = $response->json();
        $standard = $data['standard'] ?? 0;
        $rapid = $data['rapid'] ?? 0;
        $blitz = $data['blitz'] ?? 0;

        $eloMasAlto = max($standard, $rapid, $blitz);
        $tituloId = Titulo::where('abreviacion', $data['title'] ?? null)
            ->value('id')
            ?? Titulo::where('abreviacion', 'ST')->value('id');

        return response()->json([
            'elo_internacional' => $eloMasAlto,
            'titulo_id' => $tituloId
        ]);
    }

    public function indexHome(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $deportistas = Deportista::with(['genero', 'usuario', 'club', 'categoria', 'nacionalidad', 'usuario.tipoIdentificacion', 'titulo'])
            ->paginate($perPage);

        return response()->json([
            'data' => $this->mapDeportistas($deportistas->getCollection()),
            'meta' => [
                'current_page' => $deportistas->currentPage(),
                'last_page' => $deportistas->lastPage(),
                'per_page' => $deportistas->perPage(),
                'total' => $deportistas->total(),
            ]
        ]);
    }

    private function mapDeportistas($deportistas)
    {
        return $deportistas->map(function ($deportista) {
            return [
                'id' => $deportista->id,
                'usuario_id' => $deportista->usuario_id,
                'nombre' => $deportista->usuario->nombre,
                'apellido' => $deportista->usuario->apellido,
                'fecha_nacimiento' => $deportista->fecha_nacimiento,
                'genero_id' => $deportista->genero_id,
                'nacionalidad_id' => $deportista->nacionalidad_id,
                'elo_nacional' => $deportista->elo_nacional,
                'elo_internacional' => $deportista->elo_internacional,
                'fide_id' => $deportista->fide_id,
                'titulo_id' => $deportista->titulo_id,
                'categoria_id' => $deportista->categoria_id,
                'estado' => $deportista->estado,
                'foto_perfil' => $deportista->usuario->imagen_path ? '/storage/'.$deportista->usuario->imagen_path : null,
            ];
        });
    }

    public function showPublic(int $id)
    {
        $deportista = Deportista::with(['genero', 'usuario', 'club', 'categoria', 'nacionalidad', 'usuario.tipoIdentificacion', 'titulo'])->findOrFail($id);
        return response()->json([
            'id' => $deportista->id,
            'usuario_id' => $deportista->usuario_id,
            'nombre' => $deportista->usuario->nombre,
            'apellido' => $deportista->usuario->apellido,
            'fecha_nacimiento' => $deportista->fecha_nacimiento,
            'genero' => $deportista->genero ? $deportista->genero->nombre : null,
            'nacionalidad' => $deportista->nacionalidad ? $deportista->nacionalidad->nombre : null,
            'elo_nacional' => $deportista->elo_nacional,
            'elo_internacional' => $deportista->elo_internacional,
            'fide_id' => $deportista->fide_id,
            'titulo' => $deportista->titulo ? $deportista->titulo->nombre : null,
            'categoria' => $deportista->categoria ? $deportista->categoria->nombre : null,
            'estado' => $deportista->estado,
            'foto_perfil' => $deportista->usuario->imagen_path ? '/storage/'.$deportista->usuario->imagen_path : null,
            'liga' => [
                'id' => $deportista->club->liga->id,
                'nombre' => $deportista->club->liga->nombre,
                'descripcion' => $deportista->club->liga->descripcion,
                'logo' => $deportista->club->liga->logo ? '/storage/'.$deportista->club->liga->logo : null,
            ],
        ]);
    }

    public function miElo()
    {
        $usuario = auth()->user();
        $deportista = Deportista::with([
            'usuario',
            'club',
            'titulo',
            'categoria',
            'genero',
            'nacionalidad',
        ])->where('usuario_id', $usuario->id)->first();

        if (!$deportista) {
            return response()->json([
                'message' => 'El usuario no tiene un deportista asociado.'
            ], 404);
        }

        if (!$deportista->fide_id) {
            return response()->json([
                'message' => 'El deportista no tiene un FIDE ID asociado.'
            ], 404);
        }

        [$playerResponse, $ratingsResponse] = Http::pool(function ($pool) use ($deportista) {
            return [
                $pool->get(env('API_CHESSTOOLS_URL') . "/fide/player/{$deportista->fide_id}"),
                $pool->get(env('API_CHESSTOOLS_URL') . "/fide/player/{$deportista->fide_id}/ratings"),
            ];
        });

        if (!$playerResponse->successful() || !$ratingsResponse->successful()) {
            return response()->json([
                'message' => 'Error al consultar la información FIDE.'
            ], 500);
        }

        $player = $playerResponse->json();
        $ratings = $ratingsResponse->json();

        $historial = $this->formatearHistorial($ratings);

        return response()->json([
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'apellido' => $usuario->apellido,
                'foto_perfil' => $usuario->imagen_path
                    ? '/storage/' . $usuario->imagen_path
                    : asset('img/usuarios/default.jpg'),
                'edad' => Carbon::parse($deportista->fecha_nacimiento)->age,
            ],

            'club' => [
                'id' => optional($deportista->club)->id,
                'nombre' => optional($deportista->club)->nombre ?? 'Sin club',
            ],

            'deportista' => [
                'fide_id' => $deportista->fide_id,
                'titulo' => optional($deportista->titulo)->nombre ?? 'Sin título',
                'elo_nacional' => $deportista->elo_nacional,
                'elo_internacional' => $deportista->elo_internacional,
                'categoria' => optional($deportista->categoria)->nombre,
                'genero' => optional($deportista->genero)->nombre,
                'nacionalidad' => optional($deportista->nacionalidad)->nombre,
            ],

            'elo_actual' => [
                'standard' => $player['standard'] ?? null,
                'rapid' => $player['rapid'] ?? null,
                'blitz' => $player['blitz'] ?? null,
            ],

            'historial' => $historial,
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

    public function eloDeportista(int $id)
    {
        $deportista = Deportista::with([
            'usuario',
            'club',
            'titulo',
            'categoria',
            'genero',
            'nacionalidad',
        ])->where('id', $id)->first();

        if (!$deportista) {
            return response()->json([
                'message' => 'El usuario no tiene un deportista asociado.'
            ], 404);
        }

        if (!$deportista->fide_id) {
            return response()->json([
                'message' => 'El deportista no tiene un FIDE ID asociado.'
            ], 404);
        }

        $usuario = Usuario::find($deportista->usuario_id);

        [$playerResponse, $ratingsResponse] = Http::pool(function ($pool) use ($deportista) {
            return [
                $pool->get(env('API_CHESSTOOLS_URL') . "/fide/player/{$deportista->fide_id}"),
                $pool->get(env('API_CHESSTOOLS_URL') . "/fide/player/{$deportista->fide_id}/ratings"),
            ];
        });

        if (!$playerResponse->successful() || !$ratingsResponse->successful()) {
            return response()->json([
                'message' => 'Error al consultar la información FIDE.'
            ], 500);
        }

        $player = $playerResponse->json();
        $ratings = $ratingsResponse->json();

        $historial = $this->formatearHistorial($ratings);

        return response()->json([
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'apellido' => $usuario->apellido,
                'foto_perfil' => $usuario->imagen_path
                    ? '/storage/' . $usuario->imagen_path
                    : asset('img/usuarios/default.jpg'),
                'edad' => Carbon::parse($deportista->fecha_nacimiento)->age,
            ],

            'club' => [
                'id' => optional($deportista->club)->id,
                'nombre' => optional($deportista->club)->nombre ?? 'Sin club',
            ],

            'deportista' => [
                'fide_id' => $deportista->fide_id,
                'titulo' => optional($deportista->titulo)->nombre ?? 'Sin título',
                'elo_nacional' => $deportista->elo_nacional,
                'elo_internacional' => $deportista->elo_internacional,
                'categoria' => optional($deportista->categoria)->nombre,
                'genero' => optional($deportista->genero)->nombre,
                'nacionalidad' => optional($deportista->nacionalidad)->nombre,
            ],

            'elo_actual' => [
                'standard' => $player['standard'] ?? null,
                'rapid' => $player['rapid'] ?? null,
                'blitz' => $player['blitz'] ?? null,
            ],

            'historial' => $historial,
        ]);
    }

    public function rankingElo()
    {
        $deportistas = Deportista::with([
            'usuario',
            'club',
            'titulo'
        ])
        ->where('estado', true)
        ->whereNotNull('fide_id')
        ->get();

        $ranking = [];

        foreach ($deportistas as $deportista) {

            try {
                $response = Http::get(
                    env('API_CHESSTOOLS_URL') . "/fide/player/{$deportista->fide_id}"
                );

                if (!$response->successful()) {
                    continue;
                }

                $player = $response->json();

                $ranking[] = [
                    'id' => $deportista->id,
                    'nombre' => trim($deportista->usuario->nombre . ' ' . $deportista->usuario->apellido),
                    'foto_perfil' => $deportista->usuario->imagen_path
                        ? '/storage/' . $deportista->usuario->imagen_path
                        : null,
                    'titulo' => optional($deportista->titulo)->abreviacion
                        ?? optional($deportista->titulo)->nombre
                        ?? 'ST',

                    'club_id' => optional($deportista->club)->id,
                    'club' => optional($deportista->club)->nombre ?? 'Sin club',

                    'elo_standard' => $player['standard'] ?? 0,
                    'elo_rapid' => $player['rapid'] ?? 0,
                    'elo_blitz' => $player['blitz'] ?? 0,
                ];

            } catch (\Throwable $e) {
                Log::warning("Error consultando FIDE {$deportista->fide_id}: ".$e->getMessage());
            }
        }

        usort($ranking, fn($a, $b) => $b['elo_standard'] <=> $a['elo_standard']);

        foreach ($ranking as $i => &$jugador) {
            $jugador['posicion'] = $i + 1;
        }

        return response()->json($ranking);
    }

    public function rankingEloHome()
    {
        $perPage = 4;
        $deportistas = Deportista::with([
            'usuario',
            'club',
            'titulo'
        ])
        ->where('estado', true)
        ->whereNotNull('fide_id')
        ->paginate($perPage);

        $ranking = [];

        foreach ($deportistas as $deportista) {

            try {
                $response = Http::get(
                    env('API_CHESSTOOLS_URL') . "/fide/player/{$deportista->fide_id}"
                );

                if (!$response->successful()) {
                    continue;
                }

                $player = $response->json();

                $ranking[] = [
                    'id' => $deportista->id,
                    'nombre' => trim($deportista->usuario->nombre . ' ' . $deportista->usuario->apellido),
                    'foto_perfil' => $deportista->usuario->imagen_path
                        ? '/storage/' . $deportista->usuario->imagen_path
                        : null,
                    'titulo' => optional($deportista->titulo)->abreviacion
                        ?? optional($deportista->titulo)->nombre
                        ?? 'ST',

                    'club_id' => optional($deportista->club)->id,
                    'club' => optional($deportista->club)->nombre ?? 'Sin club',

                    'elo_standard' => $player['standard'] ?? 0,
                    'elo_rapid' => $player['rapid'] ?? 0,
                    'elo_blitz' => $player['blitz'] ?? 0,
                ];

            } catch (\Throwable $e) {
                Log::warning("Error consultando FIDE {$deportista->fide_id}: ".$e->getMessage());
            }
        }

        usort($ranking, fn($a, $b) => $b['elo_standard'] <=> $a['elo_standard']);

        foreach ($ranking as $i => &$jugador) {
            $jugador['posicion'] = $i + 1;
        }

        return response()->json($ranking);
    }
}
