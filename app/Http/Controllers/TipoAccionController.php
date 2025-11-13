<?php

namespace App\Http\Controllers;

use App\Models\TipoAccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TipoAccionController extends Controller
{
    public function index()
    {
        $tiposAccion = TipoAccion::get();
        $tiposAccion = $tiposAccion->map(function ($tipoAccion) {
            return [
                'id' => $tipoAccion->id,
                'nombre' => $tipoAccion->nombre,
                'descripcion' => $tipoAccion->descripcion,
            ];
        });

        return response()->json($tiposAccion);
    }

    public function show($id)
    {
        $tipoAccion = TipoAccion::find($id);
        if (!$tipoAccion) {
            return response()->json(['message' => 'Tipo Accion no encontrado'], 404);
        }
        return response()->json($tipoAccion, 200);
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

        $tipoAccion = TipoAccion::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
        ]);

        return response()->json([
            'message' => 'Tipo Accion creado exitosamente',
            'tipoAccion' => $tipoAccion
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $tipoAccion = TipoAccion::find($id);
        if (!$tipoAccion) {
            return response()->json(['message' => 'Tipo Accion no encontrado'], 404);
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

        $tipoAccion->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
        ]);

        return response()->json([
            'message' => 'Tipo Accion actualizado correctamente',
            'tipoAccion' => $tipoAccion
        ], 200);
    }


    public function destroy($id)
    {
        $tipoAccion = TipoAccion::find($id);
        if (!$tipoAccion) {
            return response()->json(['message' => 'Tipo Accion no encontrado'], 404);
        }

        $tipoAccion->delete();

        return response()->json(['message' => 'Tipo Accion eliminado correctamente'], 200);
    }
}
