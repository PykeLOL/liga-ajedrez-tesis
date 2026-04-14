<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Evento;
use App\Models\TipoEvento;
use App\Models\EventoMedia;
use App\Models\EstadoEvento;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\EventoDocumento;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TorneoController extends Controller
{
    public function index()
    {
        $eventos = Evento::whereHas('tipoEvento', function ($q) {
            $q->where('nombre', 'Torneo');
        })->get();

        $eventos = $eventos->map(function ($evento) {
            return [
                'id' => $evento->id,
                'imagen_principal' => $evento->media->sortBy('orden')->first()
                    ? '/storage/'.$evento->media->sortBy('orden')->first()->path
                    : asset('img/eventos/default.jpg'),
                'nombre' => $evento->nombre,
                'descripcion' => $evento->descripcion,
                'lugar' => $evento->lugar,
                'direccion' => $evento->direccion,
                'url_mapa' => $evento->url_mapa,
                'fecha_inicio' => $evento->fecha_inicio->format('Y-m-d'),
                'hora_inicio' => $evento->hora_inicio,
                'fecha_fin' => $evento->fecha_fin->format('Y-m-d'),
                'organizador_nombre' => $evento->organizador_nombre,
                'organizador_contacto' => $evento->organizador_contacto,
                'inscritos' => $evento->inscripciones->count(),
                'max_participantes' => $evento->max_participantes,
                'estado_evento' => $evento->estadoEvento
                    ? $evento->estadoEvento->nombre
                    : 'Sin estado_evento',
                'publico' => $evento->publicado,
                'de_pago' => $evento->de_pago,
            ];
        });

        return response()->json($eventos);
    }

    public function show($id)
    {
        $evento = Evento::with([
                'tipoEvento',
                'estadoEvento',
                'organizadores',
                'media' => fn ($q) => $q->orderBy('orden'),
                'documentos' => fn ($q) => $q->orderBy('orden'),
                'categorias' => fn ($q) => $q->orderBy('orden'),
                'redesSociales' => fn ($q) => $q->orderBy('orden'),
                'inscripciones'
            ])
            ->whereHas('tipoEvento', function ($q) {
                $q->where('nombre', 'Torneo');
            })
            ->where('id', $id)
            ->first();

        if (!$evento) {
            return response()->json(['message' => 'Torneo no encontrado'], 404);
        }

        return response()->json($evento, 200);
    }

    public function store(Request $request)
    {
        $tipoTorneo = TipoEvento::where('nombre', 'Torneo')->firstOrFail();

        $validator = Validator::make($request->all(), [
            'liga_id' => 'required|exists:ligas,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'lugar' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'url_mapa' => 'nullable|url',
            'fecha_inicio' => 'required|date',
            'hora_inicio' => [
                'nullable',
                'regex:/^\d{2}:\d{2}(:\d{2})?$/'
            ],
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'de_pago' => 'required|boolean',
            'valor_rango' => 'nullable|string|max:255',
            'organizador_nombre' => 'nullable|string|max:255',
            'organizador_contacto' => 'nullable|string|max:255',
            'publicado' => 'required|boolean',
            'max_participantes' => 'nullable|integer|min:1',

            'media' => 'required|array|min:1',
            'media.*.tipo' => 'required|in:imagen,video,url',
            'media.*.archivo' => 'required_if:media.*.tipo,imagen,video|file|mimes:jpg,jpeg,png,webp,mp4,webm|max:10240',
            'media.*.url' => 'required_if:media.*.tipo,url|nullable|youtube_url',
            'media.*.orden' => 'required|integer|min:1',
            'media.*.descripcion' => 'nullable|string|max:255',

            'documentos' => 'nullable|array',
            'documentos.*.documento' => 'required|file|mimes:pdf,docx,doc,xlsx|max:5120',
            'documentos.*.orden' => 'required|integer|min:1',
            'documentos.*.descripcion' => 'nullable|string|max:255',

            'organizadores' => 'nullable|array',
            'organizadores.*' => 'exists:clubes,id',

            'categorias' => 'required|array|min:1',
            'categorias.*.categoria_id' => 'exists:categorias,id',
            'categorias.*.genero_id' => 'exists:generos,id',
            'categorias.*.ritmo_id' => 'nullable|exists:ritmos,id',
            'categorias.*.cupo_maximo' => 'nullable|integer|min:1',
            'categorias.*.costo_inscripcion' => 'nullable|numeric|min:0',

            'redes_sociales' => 'nullable|array',
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
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['tipo_evento_id'] = $tipoTorneo->id;

        $estadoBorrador = EstadoEvento::where('nombre', 'Borrador')->first();
        $estadoPublicado = EstadoEvento::where('nombre', 'Publicado')->first();
        $estadoEnCurso  = EstadoEvento::where('nombre', 'En Curso')->first();

        if (!$data['publicado']) {
            $data['estado_evento_id'] = $estadoBorrador->id;
        } else {
            $fechaInicio = Carbon::parse($data['fecha_inicio'], 'America/Bogota')->startOfDay();
            $hoy = now('America/Bogota')->startOfDay();
            if ($fechaInicio->equalTo($hoy)) {
                $data['estado_evento_id'] = $estadoEnCurso->id;
            } else {
                $data['estado_evento_id'] = $estadoPublicado->id;
            }
        }

        $evento = Evento::create($data);

        if ($request->filled('media')) {
            foreach ($request->media as $index => $m) {
                $path = null;

                if (!empty($m['archivo'])) {
                    $path = $m['archivo']->store(
                        "eventos/media/evento_{$evento->id}",
                        'public'
                    );
                }

                if (!empty($m['url'])) {
                    $path = $m['url'];
                }

                if (!$path) continue;

                EventoMedia::create([
                    'evento_id' => $evento->id,
                    'tipo' => $m['tipo'],
                    'path' => $path,
                    'orden' => $m['orden'] ?? ($index + 1),
                    'descripcion' => $m['descripcion'] ?? null,
                ]);
            }
        }

        if ($request->has('documentos')) {
            foreach ($request->documentos as $index => $docData) {
                if (!isset($docData['documento'])) {
                    continue;
                }

                $file = $docData['documento'];
                $path = $file->store(
                    "eventos/documentos/evento_{$evento->id}",
                    'public'
                );

                EventoDocumento::create([
                    'evento_id' => $evento->id,
                    'nombre' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'tipo' => $file->getClientOriginalExtension(),
                    'path' => $path,
                    'descripcion' => $docData['descripcion'] ?? null,
                    'orden' => $docData['orden'] ?? ($index + 1),
                ]);
            }
        }

        if ($request->filled('organizadores')) {
            $evento->organizadores()->delete();
            foreach ($request->organizadores as $clubId) {
                $evento->organizadores()->firstOrCreate([
                    'club_id' => $clubId,
                ]);
            }
        }

        if ($request->filled('categorias')) {
            foreach ($request->categorias as $categoria) {
                $evento->categorias()->create([
                    'categoria_id' => $categoria['categoria_id'],
                    'genero_id' => $categoria['genero_id'],
                    'ritmo_id' => $categoria['ritmo_id'] ?? null,
                    'cupo_maximo' => $categoria['cupo_maximo'] ?? null,
                    'costo_inscripcion' => $categoria['costo_inscripcion'] ?? null,
                ]);
            }
        }

        if ($request->filled('redes_sociales')) {
            foreach ($request->redes_sociales as $redSocial) {
                $evento->redesSociales()->create([
                    'red_social_id' => $redSocial['red_social_id'],
                    'url' => $redSocial['url'] ?? null,
                ]);
            }
        }

        return response()->json([
            'message' => 'Torneo creado exitosamente',
            'evento' => $evento->load(
                'media',
                'documentos',
                'organizadores',
                'categorias'
            )
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $evento = Evento::whereHas('tipoEvento', fn($q) =>
            $q->where('nombre', 'Torneo')
        )->find($id);

        if (!$evento) {
            return response()->json(['message' => 'Torneo no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'liga_id' => 'required|exists:ligas,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'lugar' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'url_mapa' => 'nullable|url',
            'fecha_inicio' => 'required|date',
            'hora_inicio' => [
                'nullable',
                'regex:/^\d{2}:\d{2}(:\d{2})?$/'
            ],
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'de_pago' => 'required|boolean',
            'valor_rango' => 'nullable|string|max:255',
            'organizador_nombre' => 'nullable|string|max:255',
            'organizador_contacto' => 'nullable|string|max:255',
            'publicado' => 'required|boolean',
            'max_participantes' => 'nullable|integer|min:1',

            'media' => 'nullable|array',
            'media.*.id' => 'nullable|exists:evento_media,id',
            'media.*.tipo' => 'required|in:imagen,video,url',
            'media.*.archivo' => 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,webm|max:10240',
            'media.*.url' => 'required_if:media.*.tipo,url|nullable|youtube_url',
            'media.*.orden' => 'required|integer|min:1',
            'media.*.descripcion' => 'nullable|string|max:255',
            'media_eliminados' => 'nullable|array',
            'media_eliminados.*' => [
                'integer',
                Rule::exists('evento_media', 'id')->where(fn ($q) =>
                    $q->where('evento_id', $id)
                ),
            ],

            'documentos' => 'nullable|array',
            'documentos.*.id' => 'nullable|exists:evento_documentos,id',
            'documentos.*.documento' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:5120',
            'documentos.*.orden' => 'required|integer|min:1',
            'documentos.*.descripcion' => 'nullable|string|max:255',
            'documentos_eliminados' => 'nullable|array',
            'documentos_eliminados.*' => [
                'integer',
                Rule::exists('evento_documentos', 'id')
                    ->where(fn ($q) => $q->where('evento_id', $id)),
            ],

            'organizadores' => 'nullable|array',
            'organizadores.*' => 'exists:clubes,id',

            'categorias' => 'required|array|min:1',
            'categorias.*.id' => 'nullable|exists:evento_categorias,id',
            'categorias.*.orden' => 'required|integer|min:1',
            'categorias.*.categoria_id' => 'required|exists:categorias,id',
            'categorias.*.genero_id' => 'required|exists:generos,id',
            'categorias.*.ritmo_id' => 'nullable|exists:ritmos,id',
            'categorias.*.cupo_maximo' => 'nullable|integer|min:1',
            'categorias.*.costo_inscripcion' => 'nullable|numeric|min:0',
            'categorias_eliminadas' => 'nullable|array',
            'categorias_eliminadas.*' => [
                'integer',
                Rule::exists('evento_categorias', 'id')
                    ->where(fn ($q) => $q->where('evento_id', $id)),
            ],

            'redes_sociales' => 'nullable|array',
            'redes_sociales.*.id' => 'nullable|exists:evento_redes_sociales,id',
            'redes_sociales.*.orden' => 'required|integer|min:1',
            'redes_sociales.*.red_social_id' => 'exists:redes_sociales,id',
            'redes_sociales.*.url' => 'url',
            'redes_sociales_eliminadas' => 'nullable|array',
            'redes_sociales_eliminadas.*' => [
                'integer',
                Rule::exists('evento_redes_sociales', 'id')
                    ->where(fn ($q) => $q->where('evento_id', $id)),
            ],
        ],
        [
            'media.*.url.youtube_url' => 'La URL del contenido multimedia debe ser de Youtube.',
            'media.*.url.required_if' => 'Debe ingresar una URL válida para el contenido multimedia.',

            'redes_sociales.*.url.url' => 'La URL de la red social no es válida.',
            'redes_sociales.*.url.required' => 'Debe ingresar una URL para la red social.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        $evento->fill($request->only([
            'nombre','descripcion','lugar','direccion','url_mapa',
            'fecha_inicio','hora_inicio','fecha_fin','de_pago', 'valor_rango',
            'organizador_nombre','organizador_contacto','publicado','max_participantes'
        ]));

        $estadoBorrador = EstadoEvento::where('nombre', 'Borrador')->first();
        $estadoPublicado = EstadoEvento::where('nombre', 'Publicado')->first();
        $estadoEnCurso  = EstadoEvento::where('nombre', 'En Curso')->first();

        if (!$request->publicado) {
            $evento->estado_evento_id = $estadoBorrador->id;
        } else {
            $fechaInicio = Carbon::parse($request->fecha_inicio, 'America/Bogota')->startOfDay();
            $hoy = now('America/Bogota')->startOfDay();
            if ($fechaInicio->equalTo($hoy)) {
                $evento->estado_evento_id = $estadoEnCurso->id;
            } else {
                $evento->estado_evento_id = $estadoPublicado->id;
            }
        }

        $evento->tipo_evento_id = $evento->tipo_evento_id;
        $evento->save();

        if ($request->filled('organizadores')) {
            $evento->organizadores()->delete();
            foreach ($request->organizadores as $clubId) {
                $evento->organizadores()->firstOrCreate([
                    'club_id' => $clubId,
                ]);
            }
        }

        if ($request->filled('media_eliminados')) {
            foreach ($request->media_eliminados as $mid) {
                $m = EventoMedia::find($mid);
                if ($m && $m->tipo !== 'url' && str_starts_with($m->path, 'eventos/')) {
                    Storage::disk('public')->delete($m->path);
                }
                optional($m)->delete();
            }
        }

        if ($request->filled('media')) {
            foreach ($request->media as $m) {
                $media = isset($m['id'])
                    ? EventoMedia::find($m['id'])
                    : new EventoMedia(['evento_id' => $evento->id]);

                if (in_array($m['tipo'], ['imagen', 'video'])) {
                    if (empty($m['archivo']) && empty($m['id'])) {
                        return response()->json([
                            'message' => 'Debe subir un archivo para imágenes o videos nuevos'
                        ], 422);
                    }

                    if (!empty($m['archivo'])) {
                        $file = $m['archivo'];
                        $media->tipo = $m['tipo'];
                        $media->path = $file->store("eventos/media/evento_{$evento->id}", 'public');
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

        if ($request->filled('documentos_eliminados')) {
            foreach ($request->documentos_eliminados as $did) {
                $doc = EventoDocumento::find($did);
                if ($doc) {
                    Storage::disk('public')->delete($doc->path);
                    $doc->delete();
                }
            }
        }

        if ($request->filled('documentos')) {
            foreach ($request->documentos as $d) {
                $doc = isset($d['id'])
                    ? EventoDocumento::find($d['id'])
                    : new EventoDocumento(['evento_id' => $evento->id]);
                if (!empty($d['documento'])) {
                    if ($doc->path) {
                        Storage::disk('public')->delete($doc->path);
                    }
                    $file = $d['documento'];
                    $doc->path = $file->store("eventos/documentos/evento_{$evento->id}", 'public');
                    $doc->nombre = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $doc->tipo = $file->getClientOriginalExtension();
                }

                $doc->orden = $d['orden'] ?? 1;
                $doc->descripcion = $d['descripcion'] ?? null;
                $doc->save();
            }
        }

        if ($request->filled('categorias_eliminadas')) {
            foreach ($request->categorias_eliminadas as $cid) {
                $evento->categorias()->where('id', $cid)->delete();
            }
        }

        if ($request->filled('categorias')) {
            foreach ($request->categorias as $cat) {
                $categoria = isset($cat['id'])
                    ? $evento->categorias()->find($cat['id'])
                    : $evento->categorias()->make();
                $categoria->categoria_id = $cat['categoria_id'];
                $categoria->genero_id = $cat['genero_id'];
                $categoria->orden = $cat['orden'];
                $categoria->ritmo_id = $cat['ritmo_id'] ?? null;
                $categoria->cupo_maximo = $cat['cupo_maximo'] ?? null;
                $categoria->costo_inscripcion = $cat['costo_inscripcion'] ?? null;
                $categoria->save();
            }
        }

        if ($request->filled('redes_sociales_eliminadas')) {
            foreach ($request->redes_sociales_eliminadas as $cid) {
                $evento->redesSociales()->where('id', $cid)->delete();
            }
        }

        if ($request->filled('redes_sociales')) {
            foreach ($request->redes_sociales as $red) {
                $redSocial = isset($red['id'])
                    ? $evento->redesSociales()->find($red['id'])
                    : $evento->redesSociales()->make();
                $redSocial->red_social_id = $red['red_social_id'];
                $redSocial->orden = $red['orden'];
                $redSocial->url = $red['url'] ?? null;
                $redSocial->save();
            }
        }

        return response()->json([
            'message' => 'Torneo actualizado correctamente',
            'evento' => $evento->load([
                'media' => fn($q) => $q->orderBy('orden'),
                'documentos' => fn($q) => $q->orderBy('orden')
            ]),
        ], 200);
    }

    public function destroy($id)
    {
        $evento = Evento::whereHas('tipoEvento', fn($q) =>
            $q->where('nombre', 'Torneo')
        )->find($id);

        if (!$evento) {
            return response()->json(['message' => 'Torneo no encontrado'], 404);
        }

        $evento->delete();

        return response()->json(['message' => 'Torneo eliminado correctamente'], 200);
    }

    public function indexHome()
    {
        $eventos = Evento::with(['tipoEvento', 'estadoEvento', 'organizadores', 'media', 'documentos', 'redesSociales', 'categorias', 'inscripciones'])
            ->where('publicado', true)
            ->whereHas('tipoEvento', fn($q) =>
                $q->where('nombre', 'Torneo')
            )
            ->get();
        return response()->json($eventos);
    }

    public function getEventosPorTipo(Request $request, $id)
    {
        $tipo = $request->get('tipo', 'proximos');
        $perPage = $request->get('per_page', 10);

        $query = Evento::with([
            'tipoEvento',
            'estadoEvento',
            'organizadores',
            'media',
            'documentos',
            'categorias.categoria',
            'categorias.ritmo',
            'inscripciones'
        ])
        ->where('tipo_evento_id', $id)
        ->where('estado_evento_id', '!=' , 1)
        ->where('publicado', true);

        $hoy = Carbon::today();

        if ($tipo === 'proximos') {
            $query->whereDate('fecha_inicio', '>=', $hoy);
        } else {
            $query->whereDate('fecha_inicio', '<', $hoy);
        }

        $eventos = $query->orderBy('fecha_inicio')->paginate($perPage);

        return response()->json([
            'data' => $this->mapEventos($eventos->getCollection()),
            'meta' => [
                'current_page' => $eventos->currentPage(),
                'last_page' => $eventos->lastPage(),
            ]
        ]);
    }

    private function mapEventos($eventos)
    {
        return $eventos->map(function ($evento) {

            $hoy = Carbon::today();
            $esProximo = Carbon::parse($evento->fecha_inicio)->gte($hoy);
            $tipoSlug = strtolower(optional($evento->tipoEvento)->slug ?? 'evento');

            $tieneClubes = $evento->organizadores && $evento->organizadores->count() > 0;

            if ($tipoSlug === 'torneo') {
                $ui = [
                    'label' => 'TORNEO',
                    'icon'  => 'bi-trophy-fill',
                    'badge' => 'bg-warning',
                    'clase' => 'meeting-type-torneo'
                ];
            } else {
                if ($tieneClubes) {
                    $ui = [
                        'label' => 'CLUB',
                        'icon'  => 'bi-shield-shaded',
                        'badge' => 'bg-primary',
                        'clase' => 'meeting-type-club'
                    ];
                } else {
                    $ui = [
                        'label' => 'LIGA',
                        'icon'  => 'bi-megaphone-fill',
                        'badge' => 'bg-success',
                        'clase' => 'meeting-type-liga'
                    ];
                }
            }
            return [
                'id' => $evento->id,
                'nombre' => $evento->nombre,
                'descripcion' => $evento->descripcion,
                'lugar' => $evento->lugar,

                'fecha_inicio' => $evento->fecha_inicio,
                'fecha_formateada' => Carbon::parse($evento->fecha_inicio)->isoFormat('D [de] MMMM, YYYY'),
                'hora_inicio' => $evento->hora_inicio
                    ? Carbon::parse($evento->hora_inicio)->format('h:i A')
                    : null,

                'categorias_ritmos' => $evento->categorias->map(fn($c)=>[
                    'categoria'=>optional($c->categoria)->nombre ?? 'General',
                    'ritmo'=>optional($c->ritmo)->nombre ?? 'No definido'
                ])->unique(fn($i)=>$i['categoria'].'-'.$i['ritmo'])->values(),

                'imagen_principal' => $evento->media->sortBy('orden')->first()
                    ? '/storage/'.$evento->media->sortBy('orden')->first()->path
                    : asset('img/eventos/default.jpg'),

                'max_participantes' => $evento->max_participantes,
                'inscritos' => $evento->inscripciones->count(),

                'estado_label' => $esProximo ? 'Abierto' : 'Cerrado',
                'estado_label_class' => $esProximo ? 'badge small badge-inscripcion-open' : 'badge small badge-inscripcion-soon',
                'permite_inscripcion' => $esProximo,

                'tipo_label' => $ui['label'],
                'tipo_icono' => $ui['icon'],
                'tipo_badge' => $ui['badge'],
                'tipo_clase' => $ui['clase'],
            ];
        })->values();
    }

    public function showPublic($id)
    {
        $evento = Evento::with([
            'tipoEvento',
            'estadoEvento',
            'organizadores.club',
            'media',
            'documentos',
            'categorias.categoria',
            'categorias.ritmo',
            'categorias.inscripciones.deportista.usuario',
            'inscripciones'
        ])
        ->where('publicado', true)
        ->findOrFail($id);

        $totalInscritos = $evento->inscripciones->count();
        $cuposDisponibles = max($evento->max_participantes - $totalInscritos, 0);

        return response()->json([
            'id' => $evento->id,
            'nombre' => $evento->nombre,
            'descripcion' => $evento->descripcion,
            'lugar' => $evento->lugar,
            'direccion' => $evento->direccion,
            'url_mapa' => $evento->url_mapa,

            'fecha_inicio' => $evento->fecha_inicio,
            'fecha_fin' => $evento->fecha_fin,
            'fecha_inicio_format' => $evento->fecha_inicio->translatedFormat('d \d\e F \d\e Y'),
            'fecha_fin_format' => $evento->fecha_fin->translatedFormat('d \d\e F \d\e Y'),
            'hora_inicio' => $evento->hora_inicio ? Carbon::parse($evento->hora_inicio)->format('h:i A') : null,

            'estado_evento' => optional($evento->estadoEvento)->nombre,
            'de_pago' => (bool)$evento->de_pago,
            'inscripciones_abiertas' => $evento->fecha_inicio->isFuture(),

            'max_participantes' => $evento->max_participantes,
            'total_inscritos' => $totalInscritos,
            'cupos_disponibles' => $cuposDisponibles,

            'organizador_principal' => $evento->organizador_nombre,
            'organizador_contacto' => $evento->organizador_contacto,

            'media' => $evento->media
                ->sortBy('orden')
                ->values()
                ->map(function ($m) {

                    $url = $m->tipo === 'url'
                        ? $m->path
                        : '/storage/' . ltrim($m->path, '/');

                    return [
                        'orden' => $m->orden,
                        'tipo' => $m->tipo, // imagen | video | url
                        'url' => $url,
                        'descripcion' => $m->descripcion,
                    ];
                }),

            'documentos' => $evento->documentos->sortBy('orden')->values()->map(fn($d) => [
                'nombre' => $d->nombre,
                'url' => '/storage/'.$d->path
            ]),

            'organizadores' => $evento->organizadores->map(fn($o) => [
                'club' => optional($o->club)->nombre,
                'logo' => optional($o->club)->logo ? '/storage/'.optional($o->club)->logo : null
            ]),

            'categorias' => $evento->categorias->map(function ($c) {
                $inscripciones = $c->inscripciones;
                return [
                    'id' => $c->id,
                    'categoria' => optional($c->categoria)->nombre,
                    'ritmo' => optional($c->ritmo)->nombre,
                    'cupo_maximo' => $c->cupo_maximo,
                    'inscritos' => $inscripciones->count(),
                    'cupos_disponibles' => $c->cupo_maximo ? max($c->cupo_maximo - $inscripciones->count(), 0) : null,
                    'costo_inscripcion' => number_format($c->costo_inscripcion, 0, ',', '.'),
                    'deportistas' => $inscripciones->map(fn($i) => [
                        'nombre' => optional($i->deportista->usuario)->nombre.' '.optional($i->deportista->usuario)->apellido,
                        'club' => optional($i->deportista->club)->nombre,
                        'elo' => $i->deportista->elo_nacional,
                        'fide_id' => $i->deportista->fide_id,
                        'estado_pago' => optional($i->estadoInscripcion)->nombre
                    ])
                ];
            })
        ]);
    }
}
