<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\ClubMedia;
use Illuminate\Http\Request;
use App\Models\ClubRedSocial;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ClubController extends Controller
{
    public function index()
    {
        $clubes = Club::with('presidente')->get();
        $clubes = $clubes->map(function ($club) {
            return [
                'id' => $club->id,
                'nombre' => $club->nombre,
                'descripcion' => $club->descripcion,
                'ubicacion' => $club->ubicacion,
                'direccion' => $club->direccion,
                'url_mapa' => $club->url_mapa,
                'presidente' => $club->presidente->nombre . ' ' . $club->presidente->apellido,
                'contacto' => $club->contacto,
                'logo' => $club->logo ? '/storage/'.$club->logo : asset('img/clubes/default.jpg'),
            ];
        });

        return response()->json($clubes);
    }

    public function show($id)
    {
        $club = Club::with(['liga', 'presidente', 'media', 'redesSociales'])
            ->where('id', $id)
            ->first();
        if (!$club) {
            return response()->json(['message' => 'Club no encontrado'], 404);
        }
        return response()->json($club, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'presidente_id' => 'nullable|numeric|exists:usuarios,id',
            'liga_id' => 'nullable|numeric|exists:ligas,id',
            'ubicacion' => 'nullable|string|max:255',
            'direccion' => 'nullable|string',
            'url_mapa' => 'nullable|url',
            'contacto' => 'nullable|email|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'documento' => 'nullable|file|mimes:pdf|max:10000',

            'media' => 'required|array|min:1',
            'media.*.tipo' => 'required|in:imagen,video,url',
            'media.*.archivo' => 'required_if:media.*.tipo,imagen,video|file|mimes:jpg,jpeg,png,webp,mp4,webm|max:10240',
            'media.*.url' => 'required_if:media.*.tipo,url|nullable|youtube_url',
            'media.*.orden' => 'required|integer|min:1',
            'media.*.descripcion' => 'nullable|string|max:255',

            'redes_sociales' => 'nullable|array',
            'redes_sociales.*.orden' => 'required|integer|min:1',
            'redes_sociales.*.red_social_id' => 'exists:redes_sociales,id',
            'redes_sociales.*.url' => 'url',
        ],
        [
            'media.*.url.youtube_url' => 'La URL del contenido multimedia debe ser de Youtube.',
            'media.*.url.required_if' => 'Debe ingresar una URL válida para el contenido multimedia.',

            'redes_sociales.*.url.url' => 'La URL de la red social no es válida.',
            'redes_sociales.*.url.required' => 'Debe ingresar una URL para la red social.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $path = null;
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('clubes/logo', 'public');
        }

        if($request->hasFile('documento')) {
            $documentoPath = $request->file('documento')->store('clubes/documentos', 'public');
            $validated['documento_path'] = $documentoPath;
        }

        $club = Club::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'contacto' => $validated['contacto'],
            'presidente_id' => $validated['presidente_id'],
            'liga_id' => $validated['liga_id'],
            'ubicacion' => $validated['ubicacion'],
            'direccion' => $validated['direccion'],
            'url_mapa' => $validated['url_mapa'],
            'logo' => $path,
            'documento_path' => $validated['documento_path'] ?? null,
        ]);

        if ($request->filled('media')) {
            foreach ($request->media as $index => $m) {
                $path = null;

                if (!empty($m['archivo'])) {
                    $path = $m['archivo']->store(
                        "clubes/media/club_{$club->id}",
                        'public'
                    );
                }

                if (!empty($m['url'])) {
                    $path = $m['url'];
                }

                if (!$path) continue;

                ClubMedia::create([
                    'club_id' => $club->id,
                    'tipo' => $m['tipo'],
                    'path' => $path,
                    'orden' => $m['orden'] ?? ($index + 1),
                    'descripcion' => $m['descripcion'] ?? null,
                ]);
            }
        }

        if ($request->filled('redes_sociales')) {
            foreach ($request->redes_sociales as $redSocial) {
                $club->redesSociales()->create([
                    'red_social_id' => $redSocial['red_social_id'],
                    'url' => $redSocial['url'] ?? null,
                ]);
            }
        }

        return response()->json([
            'message' => 'Club creado exitosamente',
            'club' => $club
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $club = Club::find($id);
        if (!$club) {
            return response()->json(['message' => 'Club no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'presidente_id' => 'nullable|numeric|exists:usuarios,id',
            'liga_id' => 'nullable|numeric|exists:ligas,id',
            'ubicacion' => 'nullable|string|max:255',
            'direccion' => 'nullable|string',
            'url_mapa' => 'nullable|url',
            'contacto' => 'nullable|email|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'documento' => 'nullable|file|mimes:pdf|max:10000',

            'media' => 'nullable|array',
            'media.*.id' => 'nullable|exists:club_media,id',
            'media.*.tipo' => 'required|in:imagen,video,url',
            'media.*.archivo' => 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,webm|max:10240',
            'media.*.url' => 'required_if:media.*.tipo,url|nullable|youtube_url',
            'media.*.orden' => 'required|integer|min:1',
            'media.*.descripcion' => 'nullable|string|max:255',
            'media_eliminados' => 'nullable|array',
            'media_eliminados.*' => [
                'integer',
                Rule::exists('club_media', 'id')->where(fn ($q) =>
                    $q->where('club_id', $id)
                ),
            ],

            'redes_sociales' => 'nullable|array',
            'redes_sociales.*.id' => [
                'nullable',
                Rule::exists('club_redes_sociales', 'id')
                    ->where(function ($query) use ($club) {
                        $query->where('club_id', $club->id);
                    }),
            ],
            'redes_sociales.*.orden' => 'required|integer|min:1',
            'redes_sociales.*.red_social_id' => 'exists:redes_sociales,id',
            'redes_sociales.*.url' => 'url',
            'redes_sociales_eliminadas' => 'nullable|array',
            'redes_sociales_eliminadas.*' => [
                'integer',
                Rule::exists('club_redes_sociales', 'id')
                    ->where(fn ($q) => $q->where('club_id', $id)),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $club->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'contacto' => $validated['contacto'],
            'presidente_id' => $validated['presidente_id'],
            'liga_id' => $validated['liga_id'],
            'ubicacion' => $validated['ubicacion'],
            'direccion' => $validated['direccion'],
            'url_mapa' => $validated['url_mapa'],
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('clubes/logo', 'public');
            $club->logo = $path;
            $club->save();
        }

        if ($request->hasFile('documento')) {
            if ($club->documento_path) {
                Storage::disk('public')->delete($club->documento_path);
            }
            $documentoPath = $request->file('documento')->store('clubes/documentos', 'public');
            $club->documento_path = $documentoPath;
            $club->save();
        }

        if ($request->filled('media_eliminados')) {
            foreach ($request->media_eliminados as $mid) {
                $m = ClubMedia::find($mid);
                if ($m && $m->tipo !== 'url' && str_starts_with($m->path, 'clubes/')) {
                    Storage::disk('public')->delete($m->path);
                }
                optional($m)->delete();
            }
        }

        if ($request->filled('media')) {
            foreach ($request->media as $m) {
                $media = isset($m['id'])
                    ? ClubMedia::find($m['id'])
                    : new ClubMedia(['club_id' => $club->id]);

                if (in_array($m['tipo'], ['imagen', 'video'])) {
                    if (empty($m['archivo']) && empty($m['id'])) {
                        return response()->json([
                            'message' => 'Debe subir un archivo para imágenes o videos nuevos'
                        ], 422);
                    }

                    if (!empty($m['archivo'])) {
                        $file = $m['archivo'];
                        $media->tipo = $m['tipo'];
                        $media->path = $file->store("clubes/media/club_{$club->id}", 'public');
                    }

                    $media->orden = $m['orden'];
                    $media->descripcion = $m['descripcion'];
                    $media->save();
                }

                if ($m['tipo'] === 'url') {
                    if(empty($m['url'])) {
                        return response()->json([
                            'message' => 'Debe indicar la URL del video'
                        ], 422);
                    }

                    $media->tipo = $m['tipo'];
                    $media->path = $m['url'];
                    $media->orden = $m['orden'];
                    $media->descripcion = $m['descripcion'];
                    $media->save();
                }
            }
        }

        if ($request->filled('redes_sociales_eliminadas')) {
            foreach ($request->redes_sociales_eliminadas as $cid) {
                $club->redesSociales()->where('id', $cid)->delete();
            }
        }

        if ($request->filled('redes_sociales')) {
            foreach ($request->redes_sociales as $red) {
                $redSocial = isset($red['id'])
                    ? $club->redesSociales()->find($red['id'])
                    : $club->redesSociales()->make();
                $redSocial->red_social_id = $red['red_social_id'];
                $redSocial->orden = $red['orden'];
                $redSocial->url = $red['url'] ?? null;
                $redSocial->save();
            }
        }

        return response()->json([
            'message' => 'Club actualizado correctamente',
            'club' => $club
        ], 200);
    }


    public function destroy($id)
    {
        $club = Club::find($id);
        if (!$club) {
            return response()->json(['message' => 'Club no encontrado'], 404);
        }

        $club->delete();

        return response()->json(['message' => 'Club eliminado correctamente'], 200);
    }

    public function indexHome(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $clubes = Club::with(['liga', 'presidente'])
            ->paginate($perPage);

        return response()->json([
            'data' => $this->mapClubes($clubes->getCollection()),
            'meta' => [
                'current_page' => $clubes->currentPage(),
                'last_page' => $clubes->lastPage(),
                'per_page' => $clubes->perPage(),
                'total' => $clubes->total(),
            ]
        ]);
    }

    private function mapClubes($clubes)
    {
        return $clubes->map(function ($club) {
            return [
                'id' => $club->id,
                'nombre' => $club->nombre,
                'ubicacion' => $club->ubicacion,
                'direccion' => $club->direccion,
                'url_mapa' => $club->url_mapa,
                'contacto' => $club->contacto,
                'logo' => $club->logo ? '/storage/'.$club->logo : null,
                'presidente' => $club->presidente ? [
                    'id' => $club->presidente->id,
                    'nombre' => $club->presidente->nombre,
                    'apellido' => $club->presidente->apellido,
                    'foto_perfil' => $club->presidente->imagen_path ? '/storage/'.$club->presidente->imagen_path : null,
                ] : null,
                'deportistas' => $club->deportistas->map(function ($deportista) {
                    return [
                        'id' => $deportista->id,
                        'usuario_id' => $deportista->usuario_id,
                        'nombre' => $deportista->usuario->nombre,
                        'apellido' => $deportista->usuario->apellido,
                        'fecha_nacimiento' => $deportista->fecha_nacimiento,
                        'elo_nacional' => $deportista->elo_nacional,
                        'elo_internacional' => $deportista->elo_internacional,
                        'fide_id' => $deportista->fide_id,
                        'foto_perfil' => $deportista->usuario->imagen_path ? '/storage/'.$deportista->usuario->imagen_path : null,
                    ];
                }),
                'liga' => [
                    'id' => $club->liga->id,
                    'nombre' => $club->liga->nombre,
                    'descripcion' => $club->liga->descripcion,
                    'logo' => $club->liga->logo ? '/storage/'.$club->liga->logo : null,
                ],
            ];
        })->values();
    }

    public function showPublic($id)
    {
        $club = Club::with(['liga', 'presidente', 'deportistas'])->findOrFail($id);
        return response()->json([
            'id' => $club->id,
            'nombre' => $club->nombre,
            'ubicacion' => $club->ubicacion,
            'direccion' => $club->direccion,
            'url_mapa' => $club->url_mapa,
            'contacto' => $club->contacto,
            'logo' => $club->logo ? '/storage/'.$club->logo : null,
            'presidente' => $club->presidente ? [
                'id' => $club->presidente->id,
                'nombre' => $club->presidente->nombre,
                'apellido' => $club->presidente->apellido,
                'foto_perfil' => $club->presidente->imagen_path ? '/storage/'.$club->presidente->imagen_path : null,
            ] : null,
            'deportistas' => $club->deportistas->map(function ($deportista) {
                return [
                    'id' => $deportista->id,
                    'usuario_id' => $deportista->usuario_id,
                    'nombre' => $deportista->usuario->nombre,
                    'apellido' => $deportista->usuario->apellido,
                    'fecha_nacimiento' => $deportista->fecha_nacimiento,
                    'elo_nacional' => $deportista->elo_nacional,
                    'elo_internacional' => $deportista->elo_internacional,
                    'fide_id' => $deportista->fide_id,
                    'foto_perfil' => $deportista->usuario->imagen_path ? '/storage/'.$deportista->usuario->imagen_path : null,
                ];
            }),
            'media' => $club->media
                ->sortBy('orden')
                ->values()
                ->map(function ($c) {
                    $url = $c->tipo === 'url'
                        ? $c->path
                        : '/storage/' . ltrim($c->path, '/');

                    return [
                        'orden' => $c->orden,
                        'tipo' => $c->tipo,
                        'url' => $url,
                        'descripcion' => $c->descripcion,
                    ];
                }),
            'liga' => [
                'id' => $club->liga->id,
                'nombre' => $club->liga->nombre,
                'descripcion' => $club->liga->descripcion,
                'logo' => $club->liga->logo ? '/storage/'.$club->liga->logo : null,
            ],
        ]);
    }
}
