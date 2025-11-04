<?php

namespace App\Http\Controllers;

use App\Models\Deportista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DeportistaController extends Controller
{
    public function index()
    {
        $deportistas = Deportista::with('presidente')->get();
        $deportistas = $deportistas->map(function ($deportista) {
            return [
                'id' => $deportista->id,
                'fecha_nacimiento' => $deportista->fecha_nacimiento,
                'genero' => $deportista->genero,
                'nacionalidad' => $deportista->nacionalidad,
                'tipo_identificacion' => $deportista->tipo_identificacion,
                'numero_identificacion' => $deportista->numero_identificacion,
                'elo_nacional' => $deportista->elo_nacional,
                'elo_internacional' => $deportista->elo_internacional,
                'fide_id' => $deportista->fide_id,
                'titulo' => $deportista->titulo,
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

    public function show($id)
    {
        $deportista = Deportista::find($id)
            ->with('usuario', 'club', 'categoria')
            ->first();
        if (!$deportista) {
            return response()->json(['message' => 'Deportista no encontrado'], 404);
        }
        return response()->json($deportista, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usuario_id' => 'required|numeric|exists:usuarios,id',
            'club_id' => 'nullable|numeric|exists:clubes,id',
            'categoria_id' => 'required|numeric|exists:categorias,id',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|string|max:50',
            'nacionalidad' => 'required|string|max:100',
            'tipo_identificacion' => 'required|string|max:100',
            'numero_identificacion' => 'required|string|max:100|unique:deportistas,numero_identificacion',
            'elo_nacional' => 'nullable|integer|min:0',
            'elo_internacional' => 'nullable|integer|min:0',
            'fide_id' => 'nullable|string|max:50|unique:deportistas,fide_id',
            'titulo' => 'nullable|string|max:50',
            'estado' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $deportista = Deportista::create([
            'usuario_id' => $validated['usuario_id'],
            'club_id' => $validated['club_id'] ?? null,
            'categoria_id' => $validated['categoria_id'],
            'fecha_nacimiento' => $validated['fecha_nacimiento'],
            'genero' => $validated['genero'],
            'nacionalidad' => $validated['nacionalidad'],
            'tipo_identificacion' => $validated['tipo_identificacion'],
            'numero_identificacion' => $validated['numero_identificacion'],
            'elo_nacional' => $validated['elo_nacional'] ?? null,
            'elo_internacional' => $validated['elo_internacional'] ?? null,
            'fide_id' => $validated['fide_id'] ?? null,
            'titulo' => $validated['titulo'] ?? null,
            'estado' => $validated['estado'],
        ]);

        return response()->json([
            'message' => 'Deportista creada exitosamente',
            'deportista' => $deportista
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $deportista = Deportista::find($id);
        if (!$deportista) {
            return response()->json(['message' => 'Deportista no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'usuario_id' => 'required|numeric|exists:usuarios,id',
            'club_id' => 'nullable|numeric|exists:clubes,id',
            'categoria_id' => 'required|numeric|exists:categorias,id',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|string|max:50',
            'nacionalidad' => 'required|string|max:100',
            'tipo_identificacion' => 'required|string|max:100',
            'numero_identificacion' => 'required|string|max:100|unique:deportistas,numero_identificacion',
            'elo_nacional' => 'nullable|integer|min:0',
            'elo_internacional' => 'nullable|integer|min:0',
            'fide_id' => 'nullable|string|max:50|unique:deportistas,fide_id',
            'titulo' => 'nullable|string|max:50',
            'estado' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $path = null;
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('deportistas', 'public');
        }

        $deportista->update([
            'usuario_id' => $validated['usuario_id'],
            'club_id' => $validated['club_id'] ?? null,
            'categoria_id' => $validated['categoria_id'],
            'fecha_nacimiento' => $validated['fecha_nacimiento'],
            'genero' => $validated['genero'],
            'nacionalidad' => $validated['nacionalidad'],
            'tipo_identificacion' => $validated['tipo_identificacion'],
            'numero_identificacion' => $validated['numero_identificacion'],
            'elo_nacional' => $validated['elo_nacional'] ?? null,
            'elo_internacional' => $validated['elo_internacional'] ?? null,
            'fide_id' => $validated['fide_id'] ?? null,
            'titulo' => $validated['titulo'] ?? null,
            'estado' => $validated['estado'],
        ]);

        return response()->json([
            'message' => 'Deportista actualizado correctamente',
            'deportista' => $deportista
        ], 200);
    }


    public function destroy($id)
    {
        $deportista = Deportista::find($id);
        if (!$deportista) {
            return response()->json(['message' => 'Deportista no encontrado'], 404);
        }

        $deportista->delete();

        return response()->json(['message' => 'Deportista eliminado correctamente'], 200);
    }
}
