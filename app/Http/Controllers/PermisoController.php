<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PermisoController extends Controller
{
    public function index()
    {
        $permisos = Permiso::with('roles')->get();
        $permisos = $permisos->map(function ($usuario) {
            return [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'descripcion' => $usuario->descripcion,
            ];
        });
        log::info("Permisos: " . json_encode($permisos));

        return response()->json($permisos);
    }

    public function show($id)
    {
        $permiso = Permiso::find($id);
        if (!$permiso) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return response()->json($permiso, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
        ]);

        $permiso = Permiso::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
        ]);

        return response()->json([
            'message' => 'Permiso creado exitosamente',
            'permiso' => $permiso
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $permiso = Permiso::find($id);
        if (!$permiso) {
            return response()->json(['message' => 'Permiso no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
        ]);

        $usuario->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
        ]);

        return response()->json([
            'message' => 'Permiso actualizado correctamente',
            'permiso' => $permiso
        ], 200);
    }


    public function destroy($id)
    {
        $permiso = Permiso::find($id);
        if (!$permiso) {
            return response()->json(['message' => 'Permiso no encontrado'], 404);
        }

        $permiso->delete();

        return response()->json(['message' => 'Permiso eliminado correctamente'], 200);
    }
}
