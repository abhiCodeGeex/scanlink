<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MarketingController extends Controller
{
    public function home(): View|RedirectResponse
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user instanceof \App\Models\User
                && $user->canAccessPanel(\Filament\Facades\Filament::getPanel('portal'))) {
                return redirect()->to(\App\Filament\Portal\Pages\PortalHome::getUrl(panel: 'portal'));
            }
        }

        return view('marketing.home');
    }
}
