<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modulo;

class ModuloController extends Controller
{
    public function index()
    {
        $modulos = Modulo::get();
        $modulos = $modulos->map(function ($modulo) {
            return [
                'id' => $modulo->id,
                'nombre' => $modulo->nombre,
                'descripcion' => $modulo->descripcion,
            ];
        });

        return response()->json($modulos);
    }

    public function show($id)
    {
        $modulo = Modulo::find($id)->first();
        if (!$modulo) {
            return response()->json(['message' => 'Modulo no encontrado'], 404);
        }
        return response()->json($modulo, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $modulo = Modulo::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
        ]);

        return response()->json([
            'message' => 'Modulo creado exitosamente',
            'modulo' => $modulo
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $modulo = Modulo::find($id);
        if (!$modulo) {
            return response()->json(['message' => 'Modulo no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $modulo->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
        ]);

        return response()->json([
            'message' => 'Modulo actualizado correctamente',
            'modulo' => $modulo
        ], 200);
    }


    public function destroy($id)
    {
        $modulo = Modulo::find($id);
        if (!$modulo) {
            return response()->json(['message' => 'Modulo no encontrado'], 404);
        }

        $modulo->delete();

        return response()->json(['message' => 'Modulo eliminado correctamente'], 200);
    }
}
