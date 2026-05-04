<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Rol;
use App\Models\Club;
use App\Models\Estado;
use App\Models\Titulo;
use App\Models\Usuario;
use App\Models\Solicitud;
use App\Models\Categoria;
use App\Models\Deportista;
use Illuminate\Http\Request;
use App\Models\SolicitudClub;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = Solicitud::with('usuario', 'club', 'deportista')->get();
        return response()->json($solicitudes);
    }

    public function show($id)
    {
        $solicitud = Solicitud::with('usuario', 'club', 'deportista')->findOrFail($id);
        return response()->json($solicitud);
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

        $solicitud = Solicitud::findOrFail($id);
        if($solicitud->estado !== Solicitud::ESTADO_PENDIENTE) {
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
                    'logo' => $solicitud->club->imagen_path,
                    'presidente_id' => $solicitud->usuario_id,
                    'estado_id' => Estado::ACTIVO,
                ]);
                Storage::disk('public')->move($solicitud->club->documento_path, "clubes/{$club->id}/documento.pdf");
                $club->documento_path = "clubes/{$club->id}/documento.pdf";
                $club->save();

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

                $tituloId = Titulo::where('abreviacion', 'ST')->value('id');
                if($solicitud->deportista->fide_id) {
                    $url = env('API_CHESSTOOLS_URL') . "/fide/player_info/?fide_id={$solicitud->deportista->fide_id}&history=true";
                    $response = Http::get($url);
                    if (!$response->successful()) {
                        return response()->json([
                            'message' => 'No se encontró información en FIDE'
                        ], 404);
                    }

                    $data = $response->json();
                    $history = $data['history'][0] ?? [];
                    $classical = $history['classical_rating'] ?? 0;
                    $rapid = $history['rapid_rating'] ?? 0;
                    $blitz = $history['blitz_rating'] ?? 0;
                    $eloMasAlto = max($classical, $rapid, $blitz);
                    $tituloId = Titulo::where('nombre_fide', $data['fide_title'] ?? null)
                        ->value('id')
                        ?? Titulo::where('abreviacion', 'ST')->value('id');
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

                Storage::disk('public')->move($solicitud->deportista->documento_path, "deportistas/{$deportista->id}/documento.pdf");
                $deportista->documento_path = "deportistas/{$deportista->id}/documento.pdf";
                $deportista->save();

                $rolDeportista = Rol::where('nombre', 'Deportista')->first();
                $usuario = Usuario::find($solicitud->usuario_id);
                $usuario->rol_id = $rolDeportista->id;
                $usuario->save();
            }
        }

        return response()->json([
            'message' => 'Solicitud actualizada exitosamente',
            'solicitud' => $solicitud
        ], 201);
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

        $eloMasAlto = 0;
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

        return response()->json([
            'message' => 'Solicitud de Deportista creada exitosamente',
            'solicitud' => $solicitud
        ], 201);
    }
}
