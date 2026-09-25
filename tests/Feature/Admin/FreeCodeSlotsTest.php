<?php

namespace Tests\Feature\Admin;

use App\Filament\Portal\Auth\Register;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\CodePurchase;
use App\Models\CodePurchaseDetail;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use App\Services\ContactCaptchaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Free codes have to be codes, not just an order row.
 *
 * What a client spends when they click "Add a New Code" is an open slot: a `profiles` row with
 * update_or_not = 0. Legacy created one per code in all three places an order is made —
 * registration's welcome code, the admin's goodwill grant, and a paid purchase. The rebuild only
 * did it for the paid one, so a new sign-up and an admin grant both produced an order that
 * looked right on the orders screen and bought the client nothing at all.
 */
class FreeCodeSlotsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function registering_hands_the_new_client_one_usable_code(): void
    {
        Mail::fake();
        EquipmentType::query()->updateOrCreate(['slag' => 'code'], ['name' => 'Code']);
        Session::put(ContactCaptchaService::SESSION_KEY, sha1('TEST'));

        $email = 'free-code-signup@yopmail.com';

        Livewire::test(Register::class)
            ->set('data', $this->registrationData($email))
            ->call('register')
            ->call('register')
            ->assertHasNoErrors();

        $member = ClientUser::query()->where('email', $email)->firstOrFail();
        $order = CodePurchase::query()->where('client_id', $member->client_id)->firstOrFail();

        $slots = Profile::query()->where('code_purchase_id', $order->id)->get();

        $this->assertCount(1, $slots, 'the welcome code exists as a slot, not only as an order');
        $this->assertFalse((bool) $slots->first()->update_or_not, 'an open slot: nothing has claimed it yet');
        $this->assertSame((int) $member->id, (int) $slots->first()->user_id);
        $this->assertSame((int) $member->client_id, (int) $slots->first()->client_id);
        $this->assertTrue((bool) $slots->first()->free_code);
        $this->assertTrue($slots->first()->expired_at->isFuture(), 'a year from issue, like legacy');

        $this->assertDatabaseHas('code_purchase_detail', [
            'code_purchase_id' => $order->id,
            'profile_id' => $slots->first()->id,
        ]);
    }

    #[Test]
    public function an_admin_grant_of_three_free_codes_creates_three_slots(): void
    {
        EquipmentType::query()->updateOrCreate(['slag' => 'code'], ['name' => 'Code']);

        $client = Client::factory()->create(['email' => 'grant-co@yopmail.com']);
        $owner = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'grant-owner@yopmail.com',
        ]);
        // A sub-user must not end up owning the grant just by being created first.
        ClientUser::factory()->subUser()->create(['client_id' => $client->id]);

        $this->actingAs($this->admin());

        Livewire::test(EditClient::class, ['record' => $client->getKey()])
            ->callAction('addFreeCodes', ['no_of_codes' => 3])
            ->assertHasNoActionErrors();

        $order = CodePurchase::query()->where('client_id', $client->id)->firstOrFail();

        $this->assertSame(3, (int) $order->no_of_codes);
        $this->assertSame(3, Profile::query()->where('code_purchase_id', $order->id)->count());
        $this->assertSame(3, CodePurchaseDetail::query()->where('code_purchase_id', $order->id)->count());
        $this->assertSame(
            [(int) $owner->id],
            Profile::query()->where('code_purchase_id', $order->id)->pluck('user_id')->unique()->map(intval(...))->values()->all(),
            'the grant belongs to the account owner, not a sub-user',
        );
    }

    #[Test]
    public function a_client_with_no_users_is_told_rather_than_left_with_a_broken_order(): void
    {
        $client = Client::factory()->create(['email' => 'empty-co@yopmail.com']);
        $client->users()->delete();

        $this->actingAs($this->admin());

        Livewire::test(EditClient::class, ['record' => $client->getKey()])
            ->callAction('addFreeCodes', ['no_of_codes' => 2]);

        $this->assertSame(0, CodePurchase::query()->where('client_id', $client->id)->count());
        $this->assertSame(0, Profile::query()->where('client_id', $client->id)->count());
    }

    private function admin(): User
    {
        return User::factory()->create([
            'email' => 'free-code-admin@yopmail.com',
            'name' => 'ScanLink Admin',
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function registrationData(string $email): array
    {
        return [
            'first_name' => 'Jane',
            'last_name' => 'Client',
            'company_name' => 'Free Code Co',
            'billing_address' => '1 Test Street',
            'email' => $email,
            'town' => 'Melbourne',
            'phone' => '0400000000',
            'postal_code' => '3000',
            'password' => 'Pass12',
            'cpassword' => 'Pass12',
            'client_reseller_code' => '',
            'captcha' => 'TEST',
            'no_codes' => '',
        ];
    }
}
