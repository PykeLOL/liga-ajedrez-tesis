<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('youtube_url', function ($attribute, $value) {
            try {
                $parsed = parse_url($value);
                if (!isset($parsed['host'])) {
                    return false;
                }

                $host = strtolower($parsed['host']);
                if ($host === 'youtu.be') {
                    return isset($parsed['path']) && strlen(trim($parsed['path'], '/')) > 0;
                }

                if ($host === 'youtube.com' || str_ends_with($host, '.youtube.com')) {
                    if (!empty($parsed['query'])) {
                        parse_str($parsed['query'], $query);
                        if (!empty($query['v'])) {
                            return true;
                        }
                    }
                    if (isset($parsed['path']) && str_starts_with($parsed['path'], '/shorts/')) {
                        return strlen(str_replace('/shorts/', '', $parsed['path'])) > 0;
                    }
                }
                return false;
            } catch (\Throwable $e) {
                return false;
            }
        });
    }
}
