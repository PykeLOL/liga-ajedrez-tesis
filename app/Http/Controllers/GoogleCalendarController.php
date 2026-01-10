<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Oauth2;

class GoogleCalendarController extends Controller
{
    public function redirectToGoogle(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            abort(401, 'No autenticado');
        }

        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));

        $client->addScope([
            'https://www.googleapis.com/auth/calendar',
            'https://www.googleapis.com/auth/userinfo.profile',
            'https://www.googleapis.com/auth/userinfo.email',
        ]);

        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setState($user->id);

        return redirect($client->createAuthUrl());
    }

    public function handleGoogleCallback(Request $request)
    {
        $userId = $request->state;
        $user = Usuario::find($userId);

        if (!$user) {
            abort(404, 'Usuario no encontrado');
        }

        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));

        $token = $client->fetchAccessTokenWithAuthCode($request->code);

        if (isset($token['error'])) {
            return redirect('http://127.0.0.1:8001/entrenamientos?google=error');
        }

        $client->setAccessToken($token);

        $oauth2 = new Oauth2($client);
        $googleUser = $oauth2->userinfo->get();

        // $user->google_id = $googleUser->id;
        $user->google_id = $googleUser->email;
        $user->google_token = $token['access_token'];
        $user->google_refresh = $token['refresh_token'] ?? $user->google_refresh;
        $user->google_token_exp = now()->addSeconds($token['expires_in']);
        $user->save();

        return redirect('http://127.0.0.1:8001/entrenamientos?google=ok');
    }
}
