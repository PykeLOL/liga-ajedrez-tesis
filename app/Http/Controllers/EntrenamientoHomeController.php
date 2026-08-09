<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Google\Service\Calendar;
use App\Models\Entrenamiento;
use Google\Client as GoogleClient;
use Google\Service\Calendar\Event;
use App\Models\EntrenamientoGoogleEvent;

class EntrenamientoHomeController extends Controller
{
    public function index(int $clubId)
    {
        $entrenamientos = Entrenamiento::with([
            'club',
            'categoria',
            'genero',
            'entrenador',
            'deportistas',
            'deportistas.usuario',
            'deportistas.titulo',
        ])
        ->where('club_id', $clubId)
        ->get();

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
        if(!$user->deportista) {
            return response()->json(['message' => 'Mis entrenamientos solo lo pueden ver los deportistas.'], 400);
        }
        $entrenamientos = Entrenamiento::with([
            'club',
            'categoria',
            'genero',
            'tipo',
            'entrenador.usuario',
            'deportistas.usuario',
            'deportistas.titulo',
            'googleEvents' => function ($query) use ($user) {
                $query->where('usuario_id', $user->id);
            },
        ])
        ->whereHas('deportistas', function ($query) use ($user) {
            $query->where('deportistas.id', $user->deportista->id);
        })
        ->get();

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
                'google_sync' => $entrenamiento->googleEvents->isNotEmpty(),
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

    public function syncToGoogle(Request $request)
    {
        $request->validate([
            'training_ids' => ['required', 'array', 'min:1'],
            'training_ids.*' => ['integer', 'exists:entrenamientos,id'],
        ]);

        $user = auth()->user();

        if (!$user->google_token) {
            return response()->json([
                'error' => 'El usuario no tiene Google conectado'
            ], 401);
        }

        $client = new GoogleClient();

        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        if (!$user->google_token_exp || now()->greaterThan($user->google_token_exp)) {

            $client->refreshToken($user->google_refresh);

            $newToken = $client->getAccessToken();

            $user->google_token = $newToken['access_token'];
            $user->google_token_exp = now()->addSeconds($newToken['expires_in']);
            $user->save();
        }

        $client->setAccessToken($user->google_token);

        $service = new Calendar($client);

        $entrenamientos = Entrenamiento::with('tipo')
            ->whereIn('id', $request->training_ids)
            ->get();

        $synced = 0;
        $updated = 0;
        $failed = [];

        foreach ($entrenamientos as $entrenamiento) {
            try {
                if (!$entrenamiento->hora_inicio || !$entrenamiento->hora_fin) {
                    $failed[] = [
                        'id' => $entrenamiento->id,
                        'error' => 'Horario incompleto'
                    ];
                    continue;
                }

                $start = Carbon::parse(
                    $entrenamiento->fecha . ' ' . $entrenamiento->hora_inicio,
                    'America/Bogota'
                );

                $end = Carbon::parse(
                    $entrenamiento->fecha . ' ' . $entrenamiento->hora_fin,
                    'America/Bogota'
                );

                if ($end->lessThanOrEqualTo($start)) {
                    $failed[] = [
                        'id' => $entrenamiento->id,
                        'error' => 'Horario inválido'
                    ];
                    continue;
                }

                $event = new Event([
                    'summary' => 'Entrenamiento - ' . $entrenamiento->tipo->nombre,
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

                $sync = EntrenamientoGoogleEvent::where('usuario_id', $user->id)
                    ->where('entrenamiento_id', $entrenamiento->id)
                    ->first();

                if ($sync) {
                    $service->events->update(
                        'primary',
                        $sync->google_event_id,
                        $event
                    );

                    $updated++;
                } else {
                    $created = $service->events->insert(
                        'primary',
                        $event
                    );

                    EntrenamientoGoogleEvent::create([
                        'usuario_id' => $user->id,
                        'entrenamiento_id' => $entrenamiento->id,
                        'google_event_id' => $created->id,
                    ]);

                    $synced++;
                }
            } catch (\Throwable $e) {
                $failed[] = [
                    'id' => $entrenamiento->id,
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'message' => "Sincronización finalizada.",
            'synced' => $synced,
            'updated' => $updated,
            'failed' => count($failed),
            'errors' => $failed,
        ]);
    }
}
