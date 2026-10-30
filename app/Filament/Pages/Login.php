<?php

namespace App\Filament\Pages;

use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    //  protected static string $view = 'filament.pages.auth.login';
    //
    protected static string $layout = 'filament.layouts.auth';

    /** Disables the native browser password exchange when central SSO is enabled; otherwise preserves Filament login. */
    public function authenticate(): ?\Filament\Http\Responses\Auth\Contracts\LoginResponse
    {
        abort_if(env('MONITOR_SSO_ENABLED') === '1', 403, 'Use central sign-in.');

        return parent::authenticate();
    }
}
