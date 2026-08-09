<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Rol;
use App\Models\Club;
use App\Models\Estado;
use App\Models\Titulo;
use App\Models\Usuario;
use App\Models\Solicitud;
use App\Models\ClubMedia;
use App\Models\Categoria;
use App\Models\Deportista;
use Illuminate\Http\Request;
use App\Models\SolicitudClub;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Services\NotificacionDomainService;

class SolicitudController extends Controller
{
    protected NotificacionDomainService $notificacionDomainService;

    public function __construct(NotificacionDomainService $notificacionDomainService)
    {
        $this->notificacionDomainService = $notificacionDomainService;
    }

    public function index()
    {
        return response()->json(
            $this->querySolicitudes()->get()
        );
    }

    public function show(int $id)
    {
        return response()->json(
            $this->querySolicitudes()->findOrFail($id)
        );
    }

    private function querySolicitudes()
    {
        $usuario = auth()->user();
        $rol = $usuario->rol->nombre;

        $query = Solicitud::with([
            'usuario',
            'club',
            'club.municipio',
            'club.municipio.departamento',
            'deportista',
            'deportista.club',
            'deportista.genero',
            'deportista.nacionalidad',
        ]);

        if (in_array($rol, ['Admin', 'Presidente Liga'])) {
            return $query;
        }

        if ($rol === 'Presidente Club') {
            $clubId = Club::where('presidente_id', $usuario->id)->value('id');

            return $query
                ->where('tipo', Solicitud::TIPO_DEPORTISTA)
                ->whereHas('deportista', fn($q) => $q->where('club_id', $clubId));
        }

        abort(403, 'No tienes permiso para consultar las solicitudes.');
    }

    public function actualizarEstado(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'estado' => 'required|in:' . implode(',', Solicitud::ESTADOS),
            'comentario' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $solicitud = Solicitud::findOrFail($id);

            if ($solicitud->estado !== Solicitud::ESTADO_PENDIENTE) {
                return response()->json(['message' => 'Solo se pueden actualizar solicitudes en estado pendiente'], 400);
            }

            $solicitud->estado = $request->estado;
            $solicitud->comentario = $request->comentario;
            $solicitud->save();

            if($request->estado === Solicitud::ESTADO_APROBADA) {
                if($solicitud->tipo === Solicitud::TIPO_CLUB) {
                    $club = Club::create([
                        'liga_id' => $solicitud->club->liga_id,
                        'nombre' => $solicitud->club->nombre,
                        'ubicacion' => $solicitud->club->municipio->nombre . ', ' . $solicitud->club->municipio->departamento->nombre,
                        'direccion' => $solicitud->club->direccion,
                        'descripcion' => $solicitud->club->descripcion,
                        'presidente_id' => $solicitud->usuario_id,
                        'contacto' => $solicitud->usuario->email,
                        'estado_id' => Estado::ACTIVO,
                    ]);

                    $nombreArchivo = basename($solicitud->club->documento_path);

                    Storage::disk('public')->copy($solicitud->club->documento_path, "clubes/{$club->id}/{$nombreArchivo}");
                    $club->documento_path = "clubes/{$club->id}/{$nombreArchivo}";

                    Log::info('$solicitud->club->imagen_path:' . json_encode($solicitud->club->imagen_path));
                    $nombreImagen = basename($solicitud->club->imagen_path);

                    Storage::disk('public')->copy($solicitud->club->imagen_path, "clubes/{$club->id}/logo/{$nombreImagen}");
                    $club->logo = "clubes/{$club->id}/logo/{$nombreImagen}";
                    $club->save();

                    $mediaPath = "clubes/media/club_{$club->id}/{$nombreImagen}";
                    Storage::disk('public')->copy($solicitud->club->imagen_path, $mediaPath);

                    ClubMedia::create([
                        'club_id' => $club->id,
                        'tipo' => 'imagen',
                        'path' => $mediaPath,
                        'orden' => 1,
                        'descripcion' => null,
                    ]);

                    $rolPresidenteClub = Rol::where('nombre', 'Presidente Club')->first();
                    $usuario = Usuario::find($solicitud->usuario_id);
                    $usuario->rol_id = $rolPresidenteClub->id;
                    $usuario->save();
                } elseif($solicitud->tipo === Solicitud::TIPO_DEPORTISTA) {
                    $edad = Carbon::parse($solicitud->deportista->fecha_nacimiento)->age;
                    $categoria = Categoria::where('nombre', '!=', 'Libre')
                        ->where('edad_minima', '<=', $edad)
                        ->where('edad_maxima', '>=', $edad)
                        ->first();

                    $classical = 0;
                    $rapid = 0;
                    $blitz = 0;

                    $eloMasAlto = max($classical, $rapid, $blitz);
                    $tituloId = Titulo::where('abreviacion', 'ST')->value('id');

                    if ($solicitud->deportista->fide_id) {
                        try {
                            $url = env('API_CHESSTOOLS_URL') . "/fide/player/{$solicitud->deportista->fide_id}";
                            $response = Http::timeout(1)->get($url);

                            if ($response->successful()) {
                                $data = $response->json();

                                $standard = $data['standard'] ?? 0;
                                $rapid = $data['rapid'] ?? 0;
                                $blitz = $data['blitz'] ?? 0;

                                $eloMasAlto = max($standard, $rapid, $blitz);

                                $tituloId = Titulo::where('abreviacion', $data['title'] ?? null)
                                    ->value('id') ?? $tituloId;
                            }
                        } catch (\Throwable $e) {
                            // La API está caída o no responde.
                            // Se usan los valores por defecto.
                        }
                    }

                    $deportista = Deportista::create([
                        'usuario_id' => $solicitud->usuario_id,
                        'club_id' => $solicitud->deportista->club_id ?? null,
                        'categoria_id' => $categoria ? $categoria->id : null,
                        'fecha_nacimiento' => $solicitud->deportista->fecha_nacimiento,
                        'genero_id' => $solicitud->deportista->genero_id,
                        'nacionalidad_id' => $solicitud->deportista->nacionalidad_id,
                        'elo_nacional' => $eloMasAlto ?? 0,
                        'elo_internacional' => $eloMasAlto ?? 0,
                        'fide_id' => $solicitud->deportista->fide_id ?? null,
                        'titulo_id' => $tituloId,
                        'estado' => true,
                    ]);

                    $nombreArchivo = basename($solicitud->deportista->documento_path);

                    Storage::disk('public')->copy($solicitud->deportista->documento_path, "deportistas/{$deportista->id}/{$nombreArchivo}");
                    $deportista->documento_path = "deportistas/{$deportista->id}/{$nombreArchivo}";
                    $deportista->save();

                    $rolDeportista = Rol::where('nombre', 'Deportista')->first();
                    $usuario = Usuario::find($solicitud->usuario_id);
                    $usuario->rol_id = $rolDeportista->id;
                    $usuario->save();
                }
                $solicitud->load('usuario');
                $this->notificacionDomainService->solicitudAprobada($solicitud);
            } else {
                $solicitud->load('usuario');
                $this->notificacionDomainService->solicitudRechazada($solicitud);
            }

            DB::commit();

            return response()->json([
                'message' => 'Solicitud actualizada exitosamente',
                'solicitud' => $solicitud
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function misSolicitudes()
    {
        $solicitudes = Solicitud::with('usuario', 'club', 'deportista')
            ->where('usuario_id', auth()->id())
            ->get();

        return response()->json($solicitudes);
    }

    public function verSolicitud($id)
    {
        $solicitud = Solicitud::with('usuario', 'club', 'deportista')
            ->where('usuario_id', auth()->id())
            ->where('id', $id)
            ->first();

        return response()->json($solicitud);
    }

    public function storeClub(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'liga_id' => 'required|exists:ligas,id',
            'nombre' => 'required|string',
            'municipio_id' => 'required|exists:municipios,id',
            'direccion' => 'required|string',
            'descripcion' => 'required|string',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'documento' => 'required|file|mimes:pdf|max:10000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if(Solicitud::where('usuario_id', auth()->id())->where('tipo', Solicitud::TIPO_CLUB)->whereIn('estado', [Solicitud::ESTADO_PENDIENTE, Solicitud::ESTADO_APROBADA])->exists()) {
            return response()->json(['message' => 'Ya tienes una solicitud de club en proceso o aprobada'], 400);
        }

        $path = $request->file('documento')->store('solicitudes', 'public');
        $logoPath = $request->file('logo')->store('logos', 'public');

        $solicitud = SolicitudClub::create([
            'solicitud_id' => Solicitud::create([
                'usuario_id' => auth()->id(),
                'tipo' => Solicitud::TIPO_CLUB,
                'estado' => Solicitud::ESTADO_PENDIENTE,
            ])->id,
            'liga_id' => $request->liga_id,
            'nombre' => $request->nombre,
            'municipio_id' => $request->municipio_id,
            'direccion' => $request->direccion,
            'descripcion' => $request->descripcion,
            'imagen_path' => $logoPath,
            'documento_path' => $path,
        ]);

        $solicitudBase = Solicitud::with([
            'usuario',
            'club.liga.presidente'
        ])->find($solicitud->solicitud_id);

        $this->notificacionDomainService->solicitudClubCreada($solicitudBase);

        return response()->json([
            'message' => 'Solicitud de Club creada exitosamente',
            'solicitud' => $solicitud
        ], 201);
    }

    public function storeDeportista(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'club_id' => 'required|exists:clubes,id',
            'fecha_nacimiento' => 'required|date',
            'genero_id' => 'required|exists:generos,id',
            'nacionalidad_id' => 'required|exists:nacionalidades,id',
            'fide_id' => 'nullable|string|unique:deportistas,fide_id',
            'documento' => 'required|file|mimes:pdf|max:10000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if(Solicitud::where('usuario_id', auth()->id())->where('tipo', Solicitud::TIPO_DEPORTISTA)->whereIn('estado', [Solicitud::ESTADO_PENDIENTE, Solicitud::ESTADO_APROBADA])->exists()) {
            return response()->json(['message' => 'Ya tienes una solicitud de deportista en proceso o aprobada'], 400);
        }

        if(auth()->user()->deportista) {
            return response()->json(['message' => 'Ya tienes un perfil de deportista asociado a tu cuenta'], 400);
        }

        $rolDeportista = Rol::where('nombre', 'Deportista')->first();
        if(auth()->user()->rol_id != $rolDeportista->id) {
            return response()->json(['message' => 'Solo los usuarios con el rol de Deportista pueden solicitar ser deportistas'], 400);
        }

        $path = $request->file('documento')->store('solicitudes', 'public');

        $solicitud = Solicitud::create([
            'usuario_id' => auth()->id(),
            'tipo' => Solicitud::TIPO_DEPORTISTA,
            'estado' => Solicitud::ESTADO_PENDIENTE,
        ]);

        $tituloId = Titulo::where('abreviacion', 'ST')->value('id');

        $solicitud->deportista()->create([
            'club_id' => $request->club_id,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'genero_id' => $request->genero_id,
            'nacionalidad_id' => $request->nacionalidad_id,
            'fide_id' => $request->fide_id,
            'titulo_id' => $tituloId,
            'documento_path' => $path,
        ]);

        $solicitud = $solicitud->load([
            'usuario',
            'deportista.club.presidente'
        ]);

        $this->notificacionDomainService->solicitudDeportistaCreada($solicitud);

        return response()->json([
            'message' => 'Solicitud de Deportista creada exitosamente',
            'solicitud' => $solicitud
        ], 201);
    }
}
