<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::with('presidente')->get();
        $categorias = $categorias->map(function ($categoria) {
            return [
                'id' => $categoria->id,
                'nombre' => $categoria->nombre,
                'edad_minima' => $categoria->edad_minima,
                'edad_maxima' => $categoria->edad_maxima,
                'descripcion' => $categoria->descripcion,
                'activo' => $categoria->activo
            ];
        });

        return response()->json($categorias);
    }

    public function show($id)
    {
        $categoria = Categoria::with('deportistas')
            ->where('id', $id)
            ->first();
        if (!$categoria) {
            return response()->json(['message' => 'Categoria no encontrado'], 404);
        }
        return response()->json($categoria, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'edad_minima' => 'required|integer|min:0',
            'edad_maxima' => 'required|integer|min:0',
            'descripcion' => 'nullable|string',
            'estado' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $categoria = Categoria::create([
            'nombre' => $validated['nombre'],
            'edad_minima' => $validated['edad_minima'],
            'edad_maxima' => $validated['edad_maxima'],
            'descripcion' => $validated['descripcion'] ?? null,
            'activo' => $validated['estado'],
        ]);

        return response()->json([
            'message' => 'Categoria creada exitosamente',
            'categoria' => $categoria
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::find($id);
        if (!$categoria) {
            return response()->json(['message' => 'Categoria no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'edad_minima' => 'required|integer|min:0',
            'edad_maxima' => 'required|integer|min:0',
            'descripcion' => 'nullable|string',
            'estado' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $categoria->update([
            'nombre' => $validated['nombre'],
            'edad_minima' => $validated['edad_minima'],
            'edad_maxima' => $validated['edad_maxima'],
            'descripcion' => $validated['descripcion'] ?? null,
            'activo' => $validated['estado'],
        ]);

        return response()->json([
            'message' => 'Categoria actualizado correctamente',
            'categoria' => $categoria
        ], 200);
    }


    public function destroy($id)
    {
        $categoria = Categoria::find($id);
        if (!$categoria) {
            return response()->json(['message' => 'Categoria no encontrado'], 404);
        }

        $categoria->delete();

        return response()->json(['message' => 'Categoria eliminado correctamente'], 200);
    }
}
