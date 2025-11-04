<?php

namespace App\Http\Controllers;

use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
        $club = Club::find($id)
            ->with('liga', 'presidente')
            ->first();
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
            'liga_id' => 'nullable|numeric|exists:ligas,id',
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
            $path = $request->file('imagen')->store('clubes', 'public');
        }

        $club = Club::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'presidente_id' => $validated['presidente_id'],
            'liga_id' => $validated['liga_id'],
            'logo' => $path,
        ]);

        return response()->json([
            'message' => 'Club creada exitosamente',
            'club' => $club
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $club = Club::find($id);
        if (!$club) {
            return response()->json(['message' => 'Club no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'presidente_id' => 'nullable|numeric|exists:usuarios,id',
            'liga_id' => 'nullable|numeric|exists:ligas,id',
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
            $path = $request->file('imagen')->store('clubes', 'public');
        }

        $club->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'presidente_id' => $validated['presidente_id'],
            'liga_id' => $validated['liga_id'],
            'logo' => $path,
        ]);

        return response()->json([
            'message' => 'Club actualizado correctamente',
            'club' => $club
        ], 200);
    }


    public function destroy($id)
    {
        $club = Club::find($id);
        if (!$club) {
            return response()->json(['message' => 'Club no encontrado'], 404);
        }

        $club->delete();

        return response()->json(['message' => 'Club eliminado correctamente'], 200);
    }
}
