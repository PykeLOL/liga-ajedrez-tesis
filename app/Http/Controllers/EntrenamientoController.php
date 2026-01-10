<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Entrenamiento;
use Google\Client as GoogleClient;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;

class EntrenamientoController extends Controller
{
    public function index()
    {
        $entrenamientos = Entrenamiento::with('club', 'categoria', 'genero', 'entrenador', 'deportistas', 'deportistas.usuario', 'deportistas.titulo')->get();
        $entrenamientos = $entrenamientos->map(function ($entrenamiento) {
            return [
                'id' => $entrenamiento->id,
                'club' => $entrenamiento->club->nombre,
                'categoria' => $entrenamiento->categoria->nombre,
                'genero' => $entrenamiento->genero->nombre,
                'entrenador' => $entrenamiento->entrenador->usuario->nombre . ' ' . $entrenamiento->entrenador->usuario->apellido,
                'fecha' => $entrenamiento->fecha,
                'hora_inicio' => $entrenamiento->hora_inicio,
                'hora_fin' => $entrenamiento->hora_fin,
                'ubicacion' => $entrenamiento->ubicacion,
                'url_mapa' => $entrenamiento->url_mapa,
                'tipo_entrenamiento' => $entrenamiento->tipo->nombre,
                'deportistas' => $entrenamiento->deportistas->map(function ($deportista) {
                    return [
                        'id' => $deportista->id,
                        'nombre' => $deportista->usuario->nombre,
                        'apellido' => $deportista->usuario->apellido,
                        'numero_identificacion' => $deportista->usuario->numero_identificacion,
                        'titulo' => $deportista->titulo->abreviacion,
                    ];
                }),
            ];
        });

        return response()->json($entrenamientos);
    }

    public function misEntrenamientos()
    {
        $user = auth()->user();
        dd($user->deportista->entrenamientos);
        $entrenamientos = Entrenamiento::with('club', 'categoria', 'genero', 'entrenador', 'deportistas', 'deportistas.usuario', 'deportistas.titulo')->get();
        $entrenamientos = $entrenamientos->map(function ($entrenamiento) {
            return [
                'id' => $entrenamiento->id,
                'club' => $entrenamiento->club->nombre,
                'categoria' => $entrenamiento->categoria->nombre,
                'genero' => $entrenamiento->genero->nombre,
                'entrenador' => $entrenamiento->entrenador->usuario->nombre . ' ' . $entrenamiento->entrenador->usuario->apellido,
                'fecha' => $entrenamiento->fecha,
                'hora_inicio' => $entrenamiento->hora_inicio,
                'hora_fin' => $entrenamiento->hora_fin,
                'ubicacion' => $entrenamiento->ubicacion,
                'url_mapa' => $entrenamiento->url_mapa,
                'tipo_entrenamiento' => $entrenamiento->tipo->nombre,
                'deportistas' => $entrenamiento->deportistas->map(function ($deportista) {
                    return [
                        'id' => $deportista->id,
                        'nombre' => $deportista->usuario->nombre,
                        'apellido' => $deportista->usuario->apellido,
                        'numero_identificacion' => $deportista->usuario->numero_identificacion,
                        'titulo' => $deportista->titulo->abreviacion,
                    ];
                }),
            ];
        });

        return response()->json($entrenamientos);
    }

    public function syncToGoogle($entrenamientoId)
    {
        $entrenamiento = Entrenamiento::findOrFail($entrenamientoId);
        $user = auth()->user();

        $client = new GoogleClient();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        if (!$user->google_token) {
            return response()->json([
                'error' => 'El usuario no tiene Google conectado'
            ], 401);
        }

        if (!$user->google_token_exp || now()->greaterThan($user->google_token_exp)) {
            $client->refreshToken($user->google_refresh);
            $newToken = $client->getAccessToken();

            $user->google_token = $newToken['access_token'];
            $user->google_token_exp = now()->addSeconds($newToken['expires_in']);
            $user->save();
        }

        $client->setAccessToken($user->google_token);

        $service = new Calendar($client);

        $start = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $entrenamiento->fecha.' '.$entrenamiento->hora_inicio,
            'America/Bogota'
        );

        $end = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $entrenamiento->fecha.' '.$entrenamiento->hora_fin,
            'America/Bogota'
        );

        $event = new Event([
            'summary' => 'Entrenamiento - '.$entrenamiento->tipo->nombre,
            'location' => $entrenamiento->coordenadas ?? $entrenamiento->ubicacion,
            'description' => $entrenamiento->descripcion,
            'start' => [
                'dateTime' => $start->toRfc3339String(),
                'timeZone' => 'America/Bogota',
            ],
            'end' => [
                'dateTime' => $end->toRfc3339String(),
                'timeZone' => 'America/Bogota',
            ],
        ]);

        if ($entrenamiento->google_event_id) {
            $service->events->update('primary', $entrenamiento->google_event_id, $event);
        } else {
            $created = $service->events->insert('primary', $event);
            $entrenamiento->google_event_id = $created->id;
            $entrenamiento->save();
        }

        return response()->json([
            'message' => 'Entrenamiento sincronizado con Google Calendar'
        ]);
    }
}
