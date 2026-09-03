<?php

namespace App\Http\Middleware;

use App\Filament\Portal\Resources\Profiles\ProfileResource;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Legacy never shows a sub-user a 403 page. Every guarded action in Kohana sets an
 * errormessage in the session and redirects back to the code list:
 *
 *   location.php     "Oops! You have no access to add profile."         (access_addcode)
 *                    "Oops! You have no access to edit profile."        (access_edit)
 *                    "Oops! You have no access to delete profile."      (access_delete)
 *   dashboard.php    "Oops! You have no access to download code."       (access_download)
 *                    "Oops! You have no access to order label."         (access_label)
 *                    "Oops! You have no access to view visitor log."    (access_log)
 *                    "Oops! You have no access to view analytics."      (access_analytics)
 *   formbuilder.php  "Oops! You have no access to view form submissions" (access_form_submission)
 *
 * Filament raises a bare 403 instead. Translate it back into the legacy message +
 * redirect for exactly the routes Kohana guards; every other 403 is left alone.
 *
 * This inspects the response rather than catching: Illuminate\Routing\Pipeline turns an
 * authorization exception into a 403 response at the stage that threw it, so by the time
 * control returns here the exception is already gone.
 */
class RedirectDeniedPortalActions
{
    /**
     * Route name => legacy error message.
     *
     * @var array<string, string>
     */
    private const MESSAGES = [
        'filament.portal.resources.profiles.create' => 'Oops! You have no access to add profile.',
        'filament.portal.resources.profiles.edit' => 'Oops! You have no access to edit profile.',
        'filament.portal.resources.profiles.view' => 'Oops! You have no access to edit profile.',
        'filament.portal.pages.scan-analytics' => 'Oops! You have no access to view analytics.',
        'filament.portal.pages.cumulative-analytics' => 'Oops! You have no access to view analytics.',
        'filament.portal.pages.order-labels' => 'Oops! You have no access to order label.',
        'filament.portal.pages.visitor-log' => 'Oops! You have no access to view visitor log.',
        'filament.portal.pages.form-submissions' => 'Oops! You have no access to view form submissions',
        'filament.portal.pages.form-submission-view' => 'Oops! You have no access to view form submissions',
        'filament.portal.pages.participants' => 'Oops! You have no access to view form submissions',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->getStatusCode() !== Response::HTTP_FORBIDDEN) {
            return $response;
        }

        $message = self::MESSAGES[$request->route()?->getName() ?? ''] ?? null;

        if ($message === null) {
            return $response;
        }

        Notification::make()
            ->title($message)
            ->danger()
            ->send();

        // Not redirect(): inside a Livewire lifecycle the container's "redirect" binding is
        // Livewire's Redirector, whose to() returns itself rather than a Response.
        return new RedirectResponse(ProfileResource::getUrl('index', panel: 'portal'));
    }
}
