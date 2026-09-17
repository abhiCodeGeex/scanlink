<?php

namespace App\Filament\Auth;

use App\Support\LoginEntrance;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login;
use Throwable;

/**
 * The admin panel's sign-in: records that this session came through the admin door.
 *
 * Filament authenticates and then asks canAccessPanel() inside the same call, and that check
 * reads the door — so it has to be on record first. A failed attempt restores whatever door was
 * there before, so a mistyped admin password cannot re-label a session opened at another door.
 */
class AdminLogin extends Login
{
    public function authenticate(): ?LoginResponse
    {
        $previous = session(LoginEntrance::SESSION_KEY);

        LoginEntrance::record(LoginEntrance::ADMIN);

        try {
            return parent::authenticate();
        } catch (Throwable $exception) {
            if (in_array($previous, [LoginEntrance::CLIENT, LoginEntrance::VOC], true)) {
                LoginEntrance::record($previous);
            } else {
                session()->forget(LoginEntrance::SESSION_KEY);
            }

            throw $exception;
        }
    }
}
