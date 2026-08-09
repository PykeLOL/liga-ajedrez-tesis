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
use App\Services\EventoService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Eventos\InscripcionResource;
use App\Http\Requests\Evento\ConfirmarInscripcionRequest;

class EventoController extends Controller
{
    protected EventoService $eventoService;

    public function __construct(EventoService $eventoService)
    {
        $this->eventoService = $eventoService;
    }

    public function index()
    {
        $eventos = Evento::whereHas('tipoEvento', function ($q) {
            $q->where('nombre', '!=', 'Torneo');
        })->get();

        $eventos = $eventos->map(function ($evento) {
            return [
                'id' => $evento->id,
                'tipo_evento_id' => $evento->tipo_evento_id,
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

    public function show(int $id)
    {
        $evento = Evento::with([
                'tipoEvento',
                'estadoEvento',
                'organizadores',
                'media' => fn ($q) => $q->orderBy('orden'),
                'documentos' => fn ($q) => $q->orderBy('orden'),
                'redesSociales' => fn ($q) => $q->orderBy('orden'),
                'asistencias'
            ])
            ->whereHas('tipoEvento', function ($q) {
                $q->where('nombre', '!=', 'Torneo');
            })
            ->where('id', $id)
            ->first();

        if (!$evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        return response()->json($evento, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'liga_id' => 'required|exists:ligas,id',
            'tipo_evento_id' => [
                'required',
                Rule::exists('tipos_evento', 'id')
                    ->where('nombre', '!=', 'Torneo'),
            ],
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'lugar' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'url_mapa' => 'nullable|url',
            'fecha_inicio' => 'required|date|after_or_equal:today',
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

            'redes_sociales' => 'nullable|array',
            'redes_sociales.*.orden' => 'required|integer|min:1',
            'redes_sociales.*.red_social_id' => 'exists:redes_sociales,id',
            'redes_sociales.*.url' => 'url',
        ],
        [
            'fecha_inicio.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',

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

        if ($request->filled('redes_sociales')) {
            foreach ($request->redes_sociales as $redSocial) {
                $evento->redesSociales()->create([
                    'red_social_id' => $redSocial['red_social_id'],
                    'url' => $redSocial['url'] ?? null,
                ]);
            }
        }

        return response()->json([
            'message' => 'Evento creado exitosamente',
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
            $q->where('nombre', '!=', 'Torneo')
        )->find($id);

        if (!$evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'liga_id' => 'required|exists:ligas,id',
            'tipo_evento_id' => 'required|exists:tipos_evento,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'lugar' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'url_mapa' => 'nullable|url',
            'fecha_inicio' => 'required|date|after_or_equal:today',
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
            'fecha_inicio.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',

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
            'organizador_nombre','organizador_contacto','publicado','max_participantes', 'tipo_evento_id'
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
            'message' => 'Evento actualizado correctamente',
            'evento' => $evento->load([
                'media' => fn($q) => $q->orderBy('orden'),
                'documentos' => fn($q) => $q->orderBy('orden')
            ]),
        ], 200);
    }

    public function destroy(int $id)
    {
        $evento = Evento::whereHas('tipoEvento', fn($q) =>
            $q->where('nombre', '!=', 'Torneo')
        )->find($id);

        if (!$evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $evento->delete();

        return response()->json(['message' => 'Evento eliminado correctamente'], 200);
    }

    public function indexHome()
    {
        $eventos = Evento::with(['tipoEvento', 'estadoEvento', 'organizadores', 'media', 'documentos', 'redesSociales', 'asistencias'])
            ->where('publicado', true)
            ->whereHas('tipoEvento', fn($q) =>
                $q->where('nombre', '!=', 'Torneo')
            )
            ->get();
        return response()->json($eventos);
    }

    public function indexHomeHome()
    {
        $perPage = 4;

        $eventos = Evento::with([
            'tipoEvento',
            'estadoEvento',
            'organizadores',
            'media',
            'documentos',
            'redesSociales',
            'asistencias'
        ])
        ->where('publicado', true)
        ->whereHas('tipoEvento', fn($q) =>
            $q->where('nombre', '!=', 'Torneo')
        )
        ->paginate($perPage);

        return response()->json($eventos->items());
    }

    public function getEventosPorTipo(Request $request, int $id)
    {
        $tipo = $request->get('tipo', 'proximos');
        $perPage = $request->get('per_page', 10);

        $query = Evento::with([
            'tipoEvento',
            'estadoEvento',
            'organizadores',
            'media' => fn ($q) => $q->orderBy('orden'),
            'documentos' => fn ($q) => $q->orderBy('orden'),
            'categorias' => fn ($q) => $q->orderBy('orden'),
            'redesSociales' => fn ($q) => $q->orderBy('orden'),
            'categorias.categoria',
            'categorias.ritmo',
            'inscripciones',
            'asistencias:id,evento_id,usuario_id'
        ])
        ->withCount('asistencias')
        ->where('tipo_evento_id', $id)
        ->whereHas('estadoEvento', fn($q) =>
            $q->where('nombre', '!=', 'Borrador')
        )
        ->where('publicado', true);

        $hoy = Carbon::today();

        if ($tipo === 'proximos') {
            $query->whereDate('fecha_inicio', '>=', $hoy);
        } else {
            $query->whereDate('fecha_inicio', '<', $hoy);
        }

        $eventos = $query
            ->orderBy('fecha_inicio')
            ->paginate($perPage);

        $tipoEvento = TipoEvento::find($id);
        if($tipoEvento->nombre == 'Torneo') {
            $data = $this->mapTorneoEventos($eventos->getCollection());
        } else {
            $data = $this->mapEventos($eventos->getCollection());
        }

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $eventos->currentPage(),
                'last_page' => $eventos->lastPage(),
            ]
        ]);
    }

    private function mapEventos($eventos)
    {
        $usuario = auth()->user();
        return $eventos->map(function ($evento) use ($usuario) {

            $hoy = Carbon::today();
            $esProximo = Carbon::parse($evento->fecha_inicio)->gte($hoy);
            $tipoSlug = strtolower(optional($evento->tipoEvento)->slug ?? 'evento');
            $tieneClubes = $evento->organizadores->isNotEmpty();

            if ($tipoSlug === 'torneo') {
                $ui = [
                    'label' => 'TORNEO',
                    'icon'  => 'bi-trophy-fill',
                    'badge' => 'bg-warning',
                    'clase' => 'meeting-type-torneo'
                ];
            } else {
                $ui = $tieneClubes
                    ? [
                        'label' => 'CLUB',
                        'icon'  => 'bi-shield-shaded',
                        'badge' => 'bg-primary',
                        'clase' => 'meeting-type-club'
                    ]
                    : [
                        'label' => 'LIGA',
                        'icon'  => 'bi-megaphone-fill',
                        'badge' => 'bg-success',
                        'clase' => 'meeting-type-liga'
                    ];
            }

            return [
                'id' => $evento->id,
                'nombre' => $evento->nombre,
                'descripcion' => $evento->descripcion,
                'lugar' => $evento->lugar,

                'fecha_inicio' => $evento->fecha_inicio,
                'fecha_inicio_formateada' => Carbon::parse($evento->fecha_inicio)->isoFormat('D [de] MMMM, YYYY'),
                'hora_inicio' => $evento->hora_inicio
                    ? Carbon::parse($evento->hora_inicio)->format('h:i A')
                    : null,
                'fecha_fin' => $evento->fecha_fin,
                'fecha_fin_formateada' => Carbon::parse($evento->fecha_fin)->isoFormat('D [de] MMMM, YYYY'),

                'categorias_ritmos' => $evento->categorias
                    ->map(fn($c) => [
                        'categoria' => optional($c->categoria)->nombre ?? 'General',
                        'ritmo' => optional($c->ritmo)->nombre ?? 'No definido'
                    ])
                    ->unique(fn($i) => $i['categoria'].'-'.$i['ritmo'])
                    ->values(),

                'documento' => $evento->documentos->first(),

                'imagen_principal' => $evento->media->sortBy('orden')->first()
                    ? '/storage/'.$evento->media->sortBy('orden')->first()->path
                    : asset('img/eventos/default.jpg'),

                'max_participantes' => $evento->max_participantes,

                'asistentes' => $evento->asistencias_count,

                'confirmo_asistencia' => $usuario
                    ? $evento->asistencias->contains('usuario_id', $usuario->id)
                    : false,

                'estado_label' => $esProximo ? 'Abierto' : 'Cerrado',
                'estado_label_class' => $esProximo
                    ? 'badge small badge-inscripcion-open'
                    : 'badge small badge-inscripcion-soon',

                'permite_inscripcion' => $esProximo,

                'tipo_label' => $ui['label'],
                'tipo_icono' => $ui['icon'],
                'tipo_badge' => $ui['badge'],
                'tipo_clase' => $ui['clase'],
            ];
        })->values();
    }

    private function mapTorneoEventos($eventos)
    {
        $usuario = auth()->user();
        return $eventos->map(function ($evento) use ($usuario) {

            $hoy = Carbon::today();
            $esProximo = Carbon::parse($evento->fecha_inicio)->gte($hoy);
            $tipoSlug = strtolower(optional($evento->tipoEvento)->slug ?? 'evento');
            $tieneClubes = $evento->organizadores->isNotEmpty();

            if ($tipoSlug === 'torneo') {
                $ui = [
                    'label' => 'TORNEO',
                    'icon'  => 'bi-trophy-fill',
                    'badge' => 'bg-warning',
                    'clase' => 'meeting-type-torneo'
                ];
            } else {
                $ui = $tieneClubes
                    ? [
                        'label' => 'CLUB',
                        'icon'  => 'bi-shield-shaded',
                        'badge' => 'bg-primary',
                        'clase' => 'meeting-type-club'
                    ]
                    : [
                        'label' => 'LIGA',
                        'icon'  => 'bi-megaphone-fill',
                        'badge' => 'bg-success',
                        'clase' => 'meeting-type-liga'
                    ];
            }

            return [
                'id' => $evento->id,
                'nombre' => $evento->nombre,
                'descripcion' => $evento->descripcion,
                'lugar' => $evento->lugar,

                'fecha_inicio' => $evento->fecha_inicio,
                'fecha_inicio_formateada' => Carbon::parse($evento->fecha_inicio)->isoFormat('D [de] MMMM, YYYY'),
                'hora_inicio' => $evento->hora_inicio
                    ? Carbon::parse($evento->hora_inicio)->format('h:i A')
                    : null,
                'fecha_fin' => $evento->fecha_fin,
                'fecha_fin_formateada' => Carbon::parse($evento->fecha_fin)->isoFormat('D [de] MMMM, YYYY'),

                'categorias_ritmos' => $evento->categorias
                    ->map(fn($c) => [
                        'categoria' => optional($c->categoria)->nombre ?? 'General',
                        'ritmo' => optional($c->ritmo)->nombre ?? 'No definido'
                    ])
                    ->unique(fn($i) => $i['categoria'].'-'.$i['ritmo'])
                    ->values(),

                'imagen_principal' => $evento->media->sortBy('orden')->first()
                    ? '/storage/'.$evento->media->sortBy('orden')->first()->path
                    : asset('img/eventos/default.jpg'),

                'max_participantes' => $evento->max_participantes,

                'inscritos' => $evento->inscripciones->count(),

                'confirmo_asistencia' => $usuario
                    ? $evento->asistencias->contains('usuario_id', $usuario->id)
                    : false,

                'estado_label' => $esProximo ? 'Abierto' : 'Cerrado',
                'estado_label_class' => $esProximo
                    ? 'badge small badge-inscripcion-open'
                    : 'badge small badge-inscripcion-soon',

                'permite_inscripcion' => $esProximo,

                'tipo_label' => $ui['label'],
                'tipo_icono' => $ui['icon'],
                'tipo_badge' => $ui['badge'],
                'tipo_clase' => $ui['clase'],
            ];
        })->values();
    }

    public function showPublic(int $id)
    {
        $evento = Evento::with([
            'tipoEvento',
            'estadoEvento',
            'organizadores.club',
            'media',
            'documentos',
            'categorias.ritmo',
            'categorias.genero',
            'categorias.categoria',
            'categorias.inscripciones.deportista.usuario',
            'inscripciones.estadoInscripcion'
        ])
        ->where('publicado', true)
        ->findOrFail($id);

        $totalInscritos = $evento->inscripcionesActivas->count();
        $cuposDisponibles = max($evento->max_participantes - $totalInscritos, 0);

        $inscripcion = null;
        if (auth()->check() && auth()->user()->deportista) {
            $registro = $evento->inscripciones
                ->where('deportista_id', auth()->user()->deportista->id)
                ->sortByDesc('id')
                ->first();

            if ($registro) {
                $inscripcion = [
                    'id' => $registro->id,
                    'categoria_id' => $registro->evento_categoria_id,
                    'estado_id' => $registro->estado_inscripcion_id,
                    'estado' => optional($registro->estadoInscripcion)->nombre,
                    'pago' => (bool) $registro->pago,
                    'comprobante' => $registro->comprobante_path
                        ? '/storage/' . $registro->comprobante_path
                        : null,
                ];
            }
        }

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
            'hora_inicio' => $evento->hora_inicio
                ? Carbon::parse($evento->hora_inicio)->format('h:i A')
                : null,

            'estado_evento' => optional($evento->estadoEvento)->nombre,
            'de_pago' => (bool) $evento->de_pago,
            'inscripciones_abiertas' => $evento->fecha_inicio->isFuture(),

            'max_participantes' => $evento->max_participantes,
            'total_inscritos' => $totalInscritos,
            'cupos_disponibles' => $cuposDisponibles,

            'organizador_principal' => $evento->organizador_nombre,
            'organizador_contacto' => $evento->organizador_contacto,

            'inscripcion' => $inscripcion,

            'media' => $evento->media
                ->sortBy('orden')
                ->values()
                ->map(function ($m) {
                    $url = $m->tipo === 'url'
                        ? $m->path
                        : '/storage/' . ltrim($m->path, '/');

                    return [
                        'orden' => $m->orden,
                        'tipo' => $m->tipo,
                        'url' => $url,
                        'descripcion' => $m->descripcion,
                    ];
                }),

            'documentos' => $evento->documentos
                ->sortBy('orden')
                ->values()
                ->map(fn($d) => [
                    'nombre' => $d->nombre,
                    'url' => '/storage/' . $d->path,
                    'tipo' => $d->tipo,
                ]),

            'organizadores' => $evento->organizadores->map(fn($o) => [
                'id' => $o->club_id,
                'club' => optional($o->club)->nombre,
                'logo' => optional($o->club)->logo
                    ? '/storage/' . optional($o->club)->logo
                    : null,
            ]),

            'categorias' => $evento->categorias->map(function ($c) {

                $inscripciones = $c->inscripciones;

                return [
                    'id' => $c->id,
                    'categoria' => optional($c->categoria)->nombre,
                    'genero' => optional($c->genero)->nombre,
                    'ritmo' => optional($c->ritmo)->nombre,
                    'cupo_maximo' => $c->cupo_maximo,
                    'inscritos' => $inscripciones->count(),
                    'cupos_disponibles' => $c->cupo_maximo
                        ? max($c->cupo_maximo - $inscripciones->count(), 0)
                        : null,
                    'costo_inscripcion' => number_format($c->costo_inscripcion, 0, ',', '.'),
                    'deportistas' => $inscripciones->map(fn($i) => [
                        'nombre' => optional($i->deportista->usuario)->nombre . ' ' . optional($i->deportista->usuario)->apellido,
                        'club' => optional($i->deportista->club)->nombre,
                        'elo' => $i->deportista->elo_nacional,
                        'fide_id' => $i->deportista->fide_id,
                        'estado_pago' => optional($i->estadoInscripcion)->nombre,
                    ]),
                ];
            }),
        ]);
    }

    public function confirmarAsistencia(int $id)
    {
        $evento = Evento::whereHas('tipoEvento', fn($q) =>
            $q->where('nombre', '!=', 'Torneo')
        )->find($id);

        if (!$evento) {
            return response()->json([
                'message' => 'Evento no encontrado'
            ], 404);
        }

        $usuarioId = auth()->id();

        $query = $evento->asistencias()
            ->where('usuario_id', $usuarioId);

        if ($query->exists()) {
            $query->delete();
            $asistira = false;
        } else {
            $evento->asistencias()->create([
                'usuario_id' => $usuarioId
            ]);
            $asistira = true;
        }

        return response()->json([
            'asistira' => $asistira,
            'total_asistentes' => $evento->asistencias()->count(),
        ]);
    }

    public function confirmarInscripcion(ConfirmarInscripcionRequest $request, int $id)
    {
        return new InscripcionResource(
            $this->eventoService->confirmarInscripcion(
                $id,
                $request->validated(),
                auth()->user()
            )
        );
    }

    public function cancelarInscripcion(int $id)
    {
        $this->eventoService->cancelarInscripcion($id, auth()->user());

        return response()->json([
            'message' => 'La inscripción fue cancelada correctamente.'
        ]);
    }
}
