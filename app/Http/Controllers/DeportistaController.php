<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Titulo;
use App\Models\Categoria;
use App\Models\Deportista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DeportistaController extends Controller
{
    public function index()
    {
        $deportistas = Deportista::with(['genero', 'usuario', 'club', 'categoria'])->get();
        $deportistas = $deportistas->map(function ($deportista) {
            return [
                'id' => $deportista->id,
                'fecha_nacimiento' => $deportista->fecha_nacimiento,
                'genero' => $deportista->genero->nombre,
                'nacionalidad' => $deportista->nacionalidad->codigo,
                'tipo_identificacion' => $deportista->tipoIdentificacion->abreviacion,
                'numero_identificacion' => $deportista->numero_identificacion,
                'elo_nacional' => $deportista->elo_nacional,
                'elo_internacional' => $deportista->elo_internacional,
                'fide_id' => $deportista->fide_id,
                'titulo' => $deportista->titulo->abreviacion,
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
        $deportista = Deportista::with(['genero', 'usuario', 'club', 'categoria', 'nacionalidad', 'tipoIdentificacion', 'titulo'])
            ->where('id', $id)
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
            'fecha_nacimiento' => 'required|date',
            'genero_id' => 'required|numeric|exists:generos,id',
            'nacionalidad_id' => 'required|numeric|exists:nacionalidades,id',
            'tipo_identificacion_id' => 'required|numeric|exists:tipos_identificacion,id',
            'numero_identificacion' => 'required|string|max:100|unique:deportistas,numero_identificacion',
            'elo_nacional' => 'nullable|integer|min:0',
            'elo_internacional' => 'nullable|integer|min:0',
            'fide_id' => 'nullable|string|max:50|unique:deportistas,fide_id',
            'titulo_id' => 'nullable|numeric|exists:titulo,id',
            'estado' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $edad = Carbon::parse($validated['fecha_nacimiento'])->age;

        $categoria = Categoria::where('edad_minima', '<=', $edad)
                            ->where('edad_maxima', '>=', $edad)
                            ->first();

        $titulo = $validated['titulo_id'] ?? null;
        if(!$validated['titulo_id']) {
            $titulo = Titulo::where('abreviacion', 'ST')->first()->id;
        }

        $deportista = Deportista::create([
            'usuario_id' => $validated['usuario_id'],
            'club_id' => $validated['club_id'] ?? null,
            'categoria_id' => $categoria->id,
            'fecha_nacimiento' => $validated['fecha_nacimiento'],
            'genero_id' => $validated['genero_id'],
            'nacionalidad_id' => $validated['nacionalidad_id'],
            'tipo_identificacion_id' => $validated['tipo_identificacion_id'],
            'numero_identificacion' => $validated['numero_identificacion'],
            'elo_nacional' => $validated['elo_nacional'] ?? null,
            'elo_internacional' => $validated['elo_internacional'] ?? null,
            'fide_id' => $validated['fide_id'] ?? null,
            'titulo_id' => $titulo,
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
            'fecha_nacimiento' => 'required|date',
            'genero_id' => 'required|numeric|exists:generos,id',
            'nacionalidad_id' => 'required|numeric|exists:nacionalidades,id',
            'tipo_identificacion_id' => 'required|numeric|exists:tipos_identificacion,id',
            'numero_identificacion' => 'required|string|max:100|unique:deportistas,numero_identificacion,' . $id,
            'elo_nacional' => 'nullable|integer|min:0',
            'elo_internacional' => 'nullable|integer|min:0',
            'fide_id' => 'nullable|string|max:50|unique:deportistas,fide_id,' . $id,
            'titulo_id' => 'nullable|numeric|exists:titulo,id',
            'estado' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $deportista->update([
            'usuario_id' => $validated['usuario_id'],
            'club_id' => $validated['club_id'] ?? null,
            'fecha_nacimiento' => $validated['fecha_nacimiento'],
            'genero_id' => $validated['genero_id'],
            'nacionalidad_id' => $validated['nacionalidad_id'],
            'tipo_identificacion_id' => $validated['tipo_identificacion_id'],
            'numero_identificacion' => $validated['numero_identificacion'],
            'elo_nacional' => $validated['elo_nacional'] ?? null,
            'elo_internacional' => $validated['elo_internacional'] ?? null,
            'fide_id' => $validated['fide_id'] ?? null,
            'titulo_id' => $validated['titulo_id'] ?? null,
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
