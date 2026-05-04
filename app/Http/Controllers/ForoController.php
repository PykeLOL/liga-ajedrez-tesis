<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\PublicacionMedia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ForoController extends Controller
{
    public function index()
    {
        $foros = Publicacion::with(
            'media',
                'comentarios',
                'comentarios.usuario:id,nombre',
                'comentarios.reacciones:id,publicacion_comentario_id,usuario_id,reaccion_id',
                'comentarios.reacciones.reaccion:id,nombre,icono'
            )->get();
        return response()->json($foros);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'liga_id' => 'required|integer|exists:ligas,id',
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',

            'media' => 'nullable|array|min:1',
            'media.*.tipo' => 'required|in:imagen,video,url',
            'media.*.archivo' => 'required_if:media.*.tipo,imagen,video|file|mimes:jpg,jpeg,png,webp,mp4,webm|max:10240',
            'media.*.url' => 'required_if:media.*.tipo,url|nullable|youtube_url',
            'media.*.orden' => 'required|integer|min:1',
            'media.*.descripcion' => 'nullable|string|max:255',
        ],[
            'media.*.url.youtube_url' => 'La URL del contenido multimedia debe ser de Youtube.',
            'media.*.url.required_if' => 'Debe ingresar una URL válida para el contenido multimedia.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $data['usuario_id'] = auth()->id();
        $data['fecha'] = now();
        $foro = Publicacion::create($data);

        if ($request->filled('media')) {
            foreach ($request->media as $index => $m) {
                $path = null;

                if (!empty($m['archivo'])) {
                    $path = $m['archivo']->store(
                        "foros/media/foro_{$foro->id}",
                        'public'
                    );
                }

                if (!empty($m['url'])) {
                    $path = $m['url'];
                }

                if (!$path) continue;

                PublicacionMedia::create([
                    'publicacion_id' => $foro->id,
                    'tipo' => $m['tipo'],
                    'path' => $path,
                    'orden' => $m['orden'] ?? ($index + 1),
                    'descripcion' => $m['descripcion'] ?? null,
                ]);
            }
        }

        return response()->json([
            'message' => 'Foro creado exitosamente',
            'foro' => $foro->load(
                'media',
            )
        ], 201);
    }

    public function show($id)
    {
        $foro = Publicacion::with(
            'media',
            'comentarios',
            'comentarios.usuario:id,nombre',
            'comentarios.reacciones:id,publicacion_comentario_id,usuario_id,reaccion_id',
            'comentarios.reacciones.reaccion:id,nombre,icono'
        )->find($id);

        if (!$foro) {
            return response()->json(['message' => 'Foro no encontrado'], 404);
        }

        return response()->json($foro, 200);
    }

    public function update(Request $request, $id)
    {
        $foro = Publicacion::find($id);

        if (!$foro) {
            return response()->json(['message' => 'Foro no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'titulo' => 'sometimes|required|string|max:255',
            'contenido' => 'sometimes|required|string',

            'media' => 'sometimes|nullable|array|min:1',
            'media.*.id' => [
                'nullable',
                Rule::exists('publicacion_media', 'id')->where(fn ($q) =>
                    $q->where('publicacion_id', $id)
                ),
            ],
            'media.*.tipo' => 'required_with:media|in:imagen,video,url',
            'media.*.archivo' => 'required_if:media.*.tipo,imagen,video|file|mimes:jpg,jpeg,png,webp,mp4,webm|max:10240',
            'media.*.url' => 'required_if:media.*.tipo,url|nullable|youtube_url',
            'media.*.orden' => 'required_with:media|integer|min:1',
            'media.*.descripcion' => 'nullable|string|max:255',

            'media_eliminados' => 'nullable|array',
            'media_eliminados.*' => [
                'integer',
                Rule::exists('publicacion_media', 'id')->where(fn ($q) =>
                    $q->where('publicacion_id', $id)
                ),
            ],
        ],[
            'media.*.url.youtube_url' => 'La URL del contenido multimedia debe ser de Youtube.',
            'media.*.url.required_if' => 'Debe ingresar una URL válida para el contenido multimedia.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $foro = Publicacion::find($id);
        $foro->update($data);

        if ($request->filled('media_eliminados')) {
            foreach ($request->media_eliminados as $mid) {
                $m = PublicacionMedia::find($mid);
                if ($m && $m->tipo !== 'url' && str_starts_with($m->path, 'foros/')) {
                    Storage::disk('public')->delete($m->path);
                }
                optional($m)->delete();
            }
        }

        if ($request->filled('media')) {
            foreach ($request->media as $m) {
                $media = isset($m['id'])
                    ? PublicacionMedia::find($m['id'])
                    : new PublicacionMedia(['publicacion_id' => $foro->id]);

                if (in_array($m['tipo'], ['imagen', 'video'])) {
                    if (empty($m['archivo']) && empty($m['id'])) {
                        return response()->json([
                            'message' => 'Debe subir un archivo para imágenes o videos nuevos'
                        ], 422);
                    }

                    if (!empty($m['archivo'])) {
                        $file = $m['archivo'];
                        $media->tipo = $m['tipo'];
                        $media->path = $file->store("foros/media/foro_{$foro->id}", 'public');
                    }

                    $media->orden = $m['orden'];
                    $media->descripcion = $m['descripcion'];
                    $media->save();
                }

                if ($m['tipo'] === 'url') {
                    if(empty($m['url'])) {
                        return response()->json([
                            'message' => 'Debe indicar la URL del video'
                        ], 422);
                    }

                    $media->tipo = $m['tipo'];
                    $media->path = $m['url'];
                    $media->orden = $m['orden'];
                    $media->descripcion = $m['descripcion'];
                    $media->save();
                }
            }
        }

        return response()->json([
            'message' => 'Foro actualizado exitosamente',
            'foro' => $foro->load(
                'media',
                'comentarios',
                'comentarios.usuario:id,nombre',
                'comentarios.reacciones:id,publicacion_comentario_id,usuario_id,reaccion_id',
                'comentarios.reacciones.reaccion:id,nombre,icono'
            )
        ], 200);
    }

    public function destroy($id)
    {
        $foro = Publicacion::find($id);
        if (!$foro) {
            return response()->json(['message' => 'Foro no encontrado'], 404);
        }

        $foro->delete();

        return response()->json(['message' => 'Foro eliminado exitosamente'], 200);
    }

    public function destroyHome($id)
    {
        $foro = Publicacion::find($id);
        if (!$foro) {
            return response()->json(['message' => 'Foro no encontrado'], 404);
        }

        if ($foro->usuario_id !== auth()->id()) {
            return response()->json(['message' => 'No tienes permiso para eliminar este foro'], 403);
        }

        $foro->delete();

        return response()->json(['message' => 'Foro eliminado exitosamente'], 200);
    }

    public function comentarPublicacion(Request $request, $id)
    {
        $foro = Publicacion::find($id);
        if (!$foro) {
            return response()->json(['message' => 'Foro no encontrado'], 404);
        }


        $validator = Validator::make($request->all(), [
            'comentario' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        $comentario = $foro->comentarios()->create([
            'usuario_id' => auth()->id(),
            'comentario' => $request->comentario,
            'fecha' => now(),
        ]);

        return response()->json([
            'message' => 'Comentario agregado exitosamente',
            'comentario' => $comentario->load('usuario:id,nombre')
        ], 201);
    }

    public function eliminarComentario($foroId, $comentarioId)
    {
        $foro = Publicacion::find($foroId);
        if (!$foro) {
            return response()->json(['message' => 'Foro no encontrado'], 404);
        }

        $comentario = $foro->comentarios()->find($comentarioId);
        if (!$comentario) {
            return response()->json(['message' => 'Comentario no encontrado'], 404);
        }

        if ($comentario->usuario_id !== auth()->id()) {
            return response()->json(['message' => 'No tienes permiso para eliminar este comentario'], 403);
        }

        $comentario->delete();

        return response()->json(['message' => 'Comentario eliminado exitosamente'], 200);
    }

    public function reaccionarComentario(Request $request, $foroId, $comentarioId)
    {
        $foro = Publicacion::find($foroId);
        if (!$foro) {
            return response()->json(['message' => 'Foro no encontrado'], 404);
        }

        $comentario = $foro->comentarios()->find($comentarioId);
        if (!$comentario) {
            return response()->json(['message' => 'Comentario no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'reaccion_id' => 'required|integer|exists:reacciones,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        $reaccion = $comentario->reacciones()->updateOrCreate(
            ['usuario_id' => auth()->id()],
            ['reaccion_id' => $request->reaccion_id]
        );

        return response()->json([
            'message' => 'Reacción aplicada exitosamente',
            'reaccion' => $reaccion->load('reaccion:id,nombre,icono')
        ], 200);
    }

    public function eliminarReaccionComentario($foroId, $comentarioId)
    {
        $foro = Publicacion::find($foroId);
        if (!$foro) {
            return response()->json(['message' => 'Foro no encontrado'], 404);
        }

        $comentario = $foro->comentarios()->find($comentarioId);
        if (!$comentario) {
            return response()->json(['message' => 'Comentario no encontrado'], 404);
        }

        $reaccion = $comentario->reacciones()->where('usuario_id', auth()->id())->first();
        if (!$reaccion) {
            return response()->json(['message' => 'Reacción no encontrada'], 404);
        }

        $reaccion->delete();

        return response()->json(['message' => 'Reacción eliminada exitosamente'], 200);
    }
}
