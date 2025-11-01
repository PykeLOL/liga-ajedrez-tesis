<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClubController extends Controller
{
    public function index()
    {
        $clubes = Club::with('presidente')->get();
        $clubes = $clubes->map(function ($club) {
            return [
                'id' => $club->id,
                'nombre' => $club->nombre,
                'descripcion' => $club->descripcion,
                'nombre_presidente' => $club->presidente->nombre . ' ' . $club->presidente->apellido,
                'logo' => $club->logo
            ];
        });

        return response()->json($clubes);
    }

    public function show($id)
    {
        $club = Club::find($id);
        if (!$club) {
            return response()->json(['message' => 'Club no encontrado'], 404);
        }
        return response()->json($club, 200);
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
            'permiso' => $permiso
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
