<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Usuario;
use App\Models\Entrenador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EntrenadorController extends Controller
{
    public function index()
    {
        $entrenadores = Entrenador::with(['genero', 'usuario', 'club', 'certificaciones', 'categorias', 'usuario.tipoIdentificacion'])->get();
        $entrenadores = $entrenadores->map(function ($entrenador) {
            return [
                'id' => $entrenador->id,
                'fecha_nacimiento' => $entrenador->fecha_nacimiento,
                'genero' => $entrenador->genero->nombre,
                'nacionalidad' => $entrenador->nacionalidad->codigo,
                'tipo_identificacion' => $entrenador->usuario->tipoIdentificacion->abreviacion,
                'numero_identificacion' => $entrenador->usuario->numero_identificacion,
                'estado' => $entrenador->estado,

                'usuario' => [
                    'nombre' => $entrenador->usuario->nombre,
                    'apellido' => $entrenador->usuario->apellido,
                    'email' => $entrenador->usuario->email,
                    'telefono' => $entrenador->usuario->telefono,
                ],

                'club' => [
                    'nombre' => $entrenador->club->nombre,
                    'descripcion' => $entrenador->club->ubicacion,
                ],

                'categorias' => $entrenador->categorias->map(function ($categoria) {
                    return [
                        'nombre' => $categoria->nombre,
                        'edad_minima' => $categoria->edad_minima,
                        'edad_maxima' => $categoria->edad_maxima,
                        'descripcion' => $categoria->descripcion,
                        'activo' => $categoria->pivot->estado,
                    ];
                }),

                'certificaciones' => $entrenador->certificaciones->map(function ($certificacion) {
                    return [
                        'nombre' => $certificacion->nombre,
                        'entidad' => $certificacion->entidad,
                        'descripcion' => $certificacion->descripcion,
                    ];
                }),
            ];
        });

        return response()->json($entrenadores);
    }

    public function show($id)
    {
        $entrenador = Entrenador::with(['usuario', 'club', 'genero', 'categorias', 'nacionalidad', 'usuario.tipoIdentificacion', 'certificaciones'])
            ->where('id', $id)
            ->first();
        if (!$entrenador) {
            return response()->json(['message' => 'Entrenador no encontrado'], 404);
        }
        return response()->json($entrenador, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usuario_id' => 'required|numeric|exists:usuarios,id|unique:entrenadores,usuario_id',
            'club_id' => 'nullable|numeric|exists:clubes,id',
            'fecha_nacimiento' => 'required|date',
            'genero_id' => 'required|numeric|exists:generos,id',
            'nacionalidad_id' => 'required|numeric|exists:nacionalidades,id',
            'elo_nacional' => 'nullable|integer|min:0',
            'elo_internacional' => 'nullable|integer|min:0',
            'fide_id' => 'nullable|string|max:50|unique:entrenadores,fide_id',
            'titulo_id' => 'nullable|numeric|exists:titulo,id',
            'estado' => 'required|boolean',
            'categorias' => 'nullable|array',
            'categorias.*.categoria_id' => 'numeric|exists:categorias,id',
            'certificaciones' => 'nullable|array',
            'certificaciones.*.nombre' => 'required_with:certificaciones|string|max:100',
            'certificaciones.*.entidad_id' => 'required_with:certificaciones|numeric|exists:entidades_certificacion,id',
            'certificaciones.*.descripcion' => 'nullable|string|max:255',
        ],
        [
            'usuario_id.unique' => 'El usuario seleccionado ya está asociado a un entrenador.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $usuario = Usuario::find($request->usuario_id);
        if ($usuario->rol->nombre !== 'Entrenador') {
            return response()->json([
                'errors' => ['usuario_id' => ['El usuario seleccionado no tiene el rol de Entrenador.']]
            ], 422);
        }

        $validated = $validator->validated();

        $entrenador = Entrenador::create([
            'usuario_id' => $validated['usuario_id'],
            'club_id' => $validated['club_id'] ?? null,
            'fecha_nacimiento' => $validated['fecha_nacimiento'],
            'genero_id' => $validated['genero_id'],
            'nacionalidad_id' => $validated['nacionalidad_id'],
            'experiencia_anios' => $validated['experiencia_anios'] ?? 0,
            'especialidad' => $validated['especialidad'] ?? null,
            'estado' => $validated['estado'],
        ]);

        $entrenador->categorias()->attach(collect($validated['categorias'] ?? [])->pluck('categoria_id')->toArray());
        foreach ($validated['certificaciones'] ?? [] as $certificacionData) {
            $entrenador->certificaciones()->create([
                'nombre' => $certificacionData['nombre'],
                'entidad_id' => $certificacionData['entidad_id'],
                'descripcion' => $certificacionData['descripcion'] ?? null,
            ]);
        }

        return response()->json([
            'message' => 'Entrenador creada exitosamente',
            'entrenador' => $entrenador
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $entrenador = Entrenador::find($id);
        if (!$entrenador) {
            return response()->json(['message' => 'Entrenador no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'club_id' => 'nullable|numeric|exists:clubes,id',
            'fecha_nacimiento' => 'required|date',
            'genero_id' => 'required|numeric|exists:generos,id',
            'nacionalidad_id' => 'required|numeric|exists:nacionalidades,id',
            'elo_nacional' => 'nullable|integer|min:0',
            'elo_internacional' => 'nullable|integer|min:0',
            'fide_id' => 'nullable|string|max:50|unique:entrenadores,fide_id,' . $id,
            'titulo_id' => 'nullable|numeric|exists:titulo,id',
            'estado' => 'required|boolean',
            'categorias' => 'nullable|array',
            'categorias.*.categoria_id' => 'numeric|exists:categorias,id',
            'certificaciones' => 'nullable|array',
            'certificaciones.*.nombre' => 'required_with:certificaciones|string|max:100',
            'certificaciones.*.entidad_id' => 'required_with:certificaciones|numeric|exists:entidades_certificacion,id',
            'certificaciones.*.descripcion' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $entrenador->update([
            'club_id' => $validated['club_id'] ?? null,
            'fecha_nacimiento' => $validated['fecha_nacimiento'],
            'genero_id' => $validated['genero_id'],
            'nacionalidad_id' => $validated['nacionalidad_id'],
            'elo_nacional' => $validated['elo_nacional'] ?? null,
            'elo_internacional' => $validated['elo_internacional'] ?? null,
            'fide_id' => $validated['fide_id'] ?? null,
            'titulo_id' => $validated['titulo_id'] ?? null,
            'estado' => $validated['estado'],
        ]);

        if (isset($validated['categorias'])) {
            $entrenador->categorias()->sync(collect($validated['categorias'])->pluck('categoria_id')->toArray());
        }

        if (isset($validated['certificaciones'])) {
            $entrenador->certificaciones()->delete();
            foreach ($validated['certificaciones'] as $certificacionData) {
                $entrenador->certificaciones()->create([
                    'nombre' => $certificacionData['nombre'],
                    'entidad_id' => $certificacionData['entidad_id'],
                    'descripcion' => $certificacionData['descripcion'] ?? null,
                ]);
            }
        }

        return response()->json([
            'message' => 'Entrenador actualizado correctamente',
            'entrenador' => $entrenador
        ], 200);
    }


    public function destroy($id)
    {
        $entrenador = Entrenador::find($id);
        if (!$entrenador) {
            return response()->json(['message' => 'Entrenador no encontrado'], 404);
        }

        $entrenador->delete();

        return response()->json(['message' => 'Entrenador eliminado correctamente'], 200);
    }
}
