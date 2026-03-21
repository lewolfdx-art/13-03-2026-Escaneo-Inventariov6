<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Http\RedirectResponse;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        // Siempre redirige a la ruta 'home' (tu welcome en /)
        return redirect()->route('home');
        // O directamente: return redirect('/');
    }
}