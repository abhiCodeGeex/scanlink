<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The form builder editor showed "Current document" as a dead link.
 *
 * It hardcoded /images/formbuilder_upload/<name>, which only ever worked because
 * FormBuilderUploadController::storeDoc() also @copy()'d each upload there. Once that
 * directory became a symlink into the read-only EFS legacy archive the copy started failing
 * silently (it is error-suppressed), so documents uploaded since resolved only at their real
 * home on the Laravel disk — which is why the profile output rendered them and the editor
 * did not.
 *
 * The editor now asks the server to resolve the filename, so both eras work: the frozen
 * Kohana archive for pre-migration files, the public disk for everything since.
 */
class FormBuilderDocumentLinkTest extends TestCase
{
    use RefreshDatabase;

    private const ROUTE = '/portal/legacy-form-builder/document/';

    public function test_resolves_a_document_stored_on_the_laravel_disk(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('form-builder/docs/fb_doc_5957_jWwcUtmt.pdf', 'pdf-bytes');

        $this->actingAs($this->portalUser())
            ->get(self::ROUTE.'fb_doc_5957_jWwcUtmt.pdf')
            ->assertRedirect(asset('storage/form-builder/docs/fb_doc_5957_jWwcUtmt.pdf'));
    }

    /**
     * Asserted structurally: this app has no named `login` route (sign-in is the marketing
     * form), so an unauthenticated hit throws out of the middleware rather than redirecting.
     */
    public function test_is_behind_the_auth_middleware(): void
    {
        $route = collect(Route::getRoutes())
            ->first(fn ($r): bool => $r->uri() === 'portal/legacy-form-builder/document/{name}');

        $this->assertNotNull($route, 'document resolver route is not registered');
        $this->assertContains('auth', $route->gatherMiddleware());
    }

    public function test_rejects_path_traversal(): void
    {
        $this->actingAs($this->portalUser())
            ->get(self::ROUTE.urlencode('../../../.env'))
            ->assertNotFound();
    }

    public function test_rejects_an_extension_the_form_builder_never_accepts(): void
    {
        $this->actingAs($this->portalUser())
            ->get(self::ROUTE.'payload.php')
            ->assertNotFound();

        $this->actingAs($this->portalUser())
            ->get(self::ROUTE.'.env')
            ->assertNotFound();
    }

    /**
     * Legacy filenames are not tidy — the 2014 archive has names with spaces — so the guard
     * must not be a character class that would 404 documents which work today.
     */
    public function test_accepts_a_legacy_filename_containing_a_space(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('form-builder/docs/20140320_113407 LR.jpg', 'jpg-bytes');

        $this->actingAs($this->portalUser())
            ->get(self::ROUTE.rawurlencode('20140320_113407 LR.jpg'))
            ->assertRedirect(asset('storage/form-builder/docs/20140320_113407 LR.jpg'));
    }

    /**
     * Guard the cause, not just the symptom: the live editor views must not rebuild the
     * legacy URL by hand, or the next upload is a dead link again.
     */
    public function test_live_editor_views_do_not_hardcode_the_legacy_upload_path(): void
    {
        $offenders = [];

        // shell.blade.php includes index.php; control-wrap.blade.php includes control.php.
        // The .blade.php twins of both are stale duplicates and are not rendered.
        foreach (['index.php', 'control.php'] as $file) {
            $path = resource_path('views/legacy/formbuilder/'.$file);

            if (str_contains((string) file_get_contents($path), 'images/formbuilder_upload')) {
                $offenders[] = $file;
            }
        }

        $this->assertSame(
            [],
            $offenders,
            'Resolve uploads through FormBuilderMedia (or the document route) instead of '
            .'rebuilding /images/formbuilder_upload/, which only holds pre-migration files.',
        );
    }

    private function portalUser(): User
    {
        $client = Client::factory()->create();

        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => fake()->unique()->safeEmail(),
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        return $user;
    }
}
