<?php

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

class GoogleCalendarController extends Controller
{
    public function redirectToGoogle()
    {
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->addScope(Google_Service_Calendar::CALENDAR);

        return redirect($client->createAuthUrl());
    }

    public function handleGoogleCallback(Request $request)
    {
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));

        $token = $client->fetchAccessTokenWithAuthCode($request->code);

        // Guardar $token en DB asociado al usuario
        $user = auth()->user();
        $user->google_token = $token['access_token'];
        $user->google_refresh = $token['refresh_token'] ?? $user->google_refresh;
        $user->google_token_exp = now()->addSeconds($token['expires_in']);
        $user->save();

        return redirect('/dashboard')->with('success', 'Cuenta de Google conectada');
    }

    public function syncToGoogle($trainingId)
    {
        $training = Training::findOrFail($trainingId);
        $user = auth()->user();

        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));

        if (!$user->google_token_exp || $user->google_token_exp < now()) {
            try {
                $client->refreshToken($user->google_refresh);
                $newToken = $client->getAccessToken();

                $user->google_token = $newToken['access_token'];
                $user->google_token_exp = now()->addSeconds($newToken['expires_in']);
                $user->save();
            } catch (\Exception $e) {
                return response()->json(['error' => 'No se pudo refrescar el token de Google. Conecta tu cuenta de nuevo.'], 401);
            }
        }

        $client->setAccessToken($user->google_token);
        $service = new Google_Service_Calendar($client);

        $event = new Google_Service_Calendar_Event([
            'summary' => $training->name,
            'description' => $training->description,
            'start' => ['dateTime' => $training->start_time->toRfc3339String()],
            'end' => ['dateTime' => $training->end_time->toRfc3339String()],
        ]);

        $calendarId = 'primary';

        try {
            $service->events->insert($calendarId, $event);
            return response()->json(['message' => 'Entrenamiento sincronizado con Google Calendar']);
        } catch (\Google_Service_Exception $e) {
            return response()->json(['error' => 'Error al sincronizar con Google Calendar', 'details' => $e->getMessage()], 500);
        }
    }
}

