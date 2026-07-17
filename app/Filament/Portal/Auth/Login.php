<?php

namespace App\Filament\Portal\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;

class Login extends BaseLogin
{
    public function mount(): void
    {
        if (Filament::auth()->check()) {
            $this->redirectIntended(default: Filament::getUrl());

            return;
        }

        $this->redirect(route('marketing.home'));
    }
}
