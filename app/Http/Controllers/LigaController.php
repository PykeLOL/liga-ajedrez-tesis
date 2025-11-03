<?php

namespace App\Http\Controllers;

use App\Models\Liga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LigaController extends Controller
{
    public function index()
    {
        $ligas = Liga::with('clubes', 'presidente')->get();
        $ligas = $ligas->map(function ($liga) {
            return [
                'id' => $liga->id,
                'nombre' => $liga->nombre,
                'descripcion' => $liga->descripcion,
                'nombre_presidente' => $liga->presidente->nombre . ' ' . $liga->presidente->apellido,
                'logo' => $liga->logo,
                'clubes' => $liga->clubes->map(function ($club) {
                    return [
                        'id' => $club->id,
                        'nombre' => $club->nombre,
                    ];
                }),
            ];
        });

        return response()->json($ligas);
    }

    public function show($id)
    {
        $liga = Liga::find($id)
            ->with('clubes', 'presidente')
            ->first();
        if (!$liga) {
            return response()->json(['message' => 'Liga no encontrado'], 404);
        }
        return response()->json($liga, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'presidente_id' => 'nullable|numeric|exists:usuarios,id',
            'imagen' => 'nullable|image|max:2048',
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
            $path = $request->file('imagen')->store('ligas', 'public');
        }

        $liga = Liga::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'presidente_id' => $validated['presidente_id'],
            'logo' => $path,
        ]);

        return response()->json([
            'message' => 'Liga creada exitosamente',
            'liga' => $liga
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $liga = Liga::find($id);
        if (!$liga) {
            return response()->json(['message' => 'Liga no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'presidente_id' => 'nullable|numeric|exists:usuarios,id',
            'imagen' => 'nullable|image|max:2048',
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
            $path = $request->file('imagen')->store('ligas', 'public');
        }

        $liga->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'presidente_id' => $validated['presidente_id'],
            'logo' => $path,
        ]);

        return response()->json([
            'message' => 'Liga actualizado correctamente',
            'liga' => $liga
        ], 200);
    }


    public function destroy($id)
    {
        $liga = Liga::find($id);
        if (!$liga) {
            return response()->json(['message' => 'Liga no encontrado'], 404);
        }

        $liga->delete();

        return response()->json(['message' => 'Liga eliminado correctamente'], 200);
    }
}
