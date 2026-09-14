<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Pages\CodeBalance;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Profile;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The Code Balance pager errored when a page number was picked, and offered no way back.
 *
 * Cause 1: the <select> used wire:change="goToPage(parseInt($event.target.value))". Livewire
 * evaluates a wire: expression with the component as its scope, so `parseInt` resolved to a
 * component method and was dispatched to the server, which answered with
 * "Unable to call component method. Public method [parseInt] not found on component".
 *
 * Cause 2: the pager only ever rendered the forward arrows. previousPage() existed on the
 * component but nothing called it.
 */
class CodeBalancePaginationTest extends TestCase
{
    use RefreshDatabase;

    /** perPage is 10, so 25 open slots is 3 pages. */
    private const SLOTS = 25;

    private const PAGES = 3;

    public function test_picking_a_page_number_accepts_the_string_a_select_sends(): void
    {
        $this->actingAsPortalUserWithSlots();

        // A <select> hands Livewire its value as a string; this is the exact payload the
        // browser produces, and the reason parseInt() was (wrongly) reached for.
        Livewire::test(CodeBalance::class)
            ->call('goToPage', '3')
            ->assertHasNoErrors()
            ->assertSet('page', 3);
    }

    public function test_pager_renders_first_and_previous_controls(): void
    {
        $this->actingAsPortalUserWithSlots();

        Livewire::test(CodeBalance::class)
            ->call('goToPage', 2)
            ->assertSee('wire:click="previousPage"', false)
            ->assertSee('wire:click="goToPage(1)"', false);
    }

    public function test_select_does_not_wrap_the_value_in_a_javascript_call(): void
    {
        $this->actingAsPortalUserWithSlots();

        Livewire::test(CodeBalance::class)
            ->assertSee('wire:change="goToPage($event.target.value)"', false)
            ->assertDontSee('parseInt', false);
    }

    public function test_paging_backwards_works_and_stops_at_the_first_page(): void
    {
        $this->actingAsPortalUserWithSlots();

        Livewire::test(CodeBalance::class)
            ->call('goToPage', self::PAGES)
            ->assertSet('page', self::PAGES)
            ->call('previousPage')
            ->assertSet('page', self::PAGES - 1)
            ->call('goToPage', 1)
            ->call('previousPage')
            ->assertSet('page', 1);
    }

    public function test_paging_forward_stops_at_the_last_page(): void
    {
        $this->actingAsPortalUserWithSlots();

        Livewire::test(CodeBalance::class)
            ->call('goToPage', 999)
            ->assertSet('page', self::PAGES)
            ->call('nextPage')
            ->assertSet('page', self::PAGES);
    }

    /**
     * Codebase-wide guard. Any wire: expression that wraps its value in a JS call hits the
     * same failure, so catch the next one at the source rather than in production.
     */
    public function test_no_wire_directive_calls_a_javascript_function_as_a_component_method(): void
    {
        $offenders = [];

        foreach (File::allFiles(resource_path('views')) as $file) {
            if (! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            if (preg_match('/wire:[a-zA-Z.\-]+="[^"]*\b(parseInt|parseFloat|Number|String)\s*\(/', File::get($file->getPathname()))) {
                $offenders[] = $file->getRelativePathname();
            }
        }

        $this->assertSame(
            [],
            $offenders,
            'A wire: expression is evaluated with the component as scope, so a JS call inside it '
            .'is dispatched to the server as a component method and 500s. Pass the raw value instead.',
        );
    }

    private function actingAsPortalUserWithSlots(int $slots = self::SLOTS): User
    {
        $client = Client::factory()->create();

        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => fake()->unique()->safeEmail(),
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        Profile::factory()->count($slots)->create([
            'client_id' => $client->id,
            'user_id' => $member->id,
            'deleted' => false,
            'update_or_not' => false,
        ]);

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        $this->actingAs($user);

        // Without this the page's PurchaseCodes::getUrl() resolves against the admin panel.
        Filament::setCurrentPanel('portal');

        return $user;
    }
}
