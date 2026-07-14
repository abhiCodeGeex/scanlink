<?php

namespace Tests\Feature\Portal;

use App\Models\Client;
use App\Models\FormBuilderQuestion;
use App\Models\Profile;
use Database\Seeders\Phase2Seeder;
use Database\Seeders\Phase3Seeder;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScanPageTest extends TestCase
{
    use RefreshDatabase;

    protected Client $client;

    protected Profile $profile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(VerifyCsrfToken::class);

        $this->seed(Phase2Seeder::class);
        $this->seed(Phase3Seeder::class);

        $this->client = Client::query()->where('url', 'acme-inspections')->firstOrFail();
        $this->profile = Profile::query()
            ->where('client_id', $this->client->id)
            ->where('code_profile_name', 'acme-forklift-a12')
            ->firstOrFail();
    }

    public function test_scan_page_renders_for_valid_profile(): void
    {
        $this->get("/{$this->client->url}/{$this->profile->id}")
            ->assertOk()
            ->assertSee($this->profile->name);
    }

    public function test_expired_profile_shows_expired_view(): void
    {
        $this->profile->update(['expired_at' => now()->subDay()]);

        $this->get("/{$this->client->url}/{$this->profile->id}")
            ->assertOk()
            ->assertSee('Code expired');
    }

    public function test_password_protected_profile_requires_unlock(): void
    {
        $this->profile->update([
            'protect' => true,
            'password' => 'secret-pass',
        ]);

        $this->get("/{$this->client->url}/{$this->profile->id}")
            ->assertOk()
            ->assertSee('password protected');
    }

    public function test_visitor_contact_can_be_stored(): void
    {
        $this->withoutMiddleware();

        $this->profile->update([
            'enable_data_collection' => true,
            'data_collection_name' => true,
            'data_collection_email' => true,
        ]);

        $this->from("/{$this->client->url}/{$this->profile->id}")
            ->post("/{$this->client->url}/{$this->profile->id}/visitor", [
                'user_name' => 'Jane Visitor',
                'user_email' => 'jane@example.com',
            ])->assertRedirect();

        $this->assertDatabaseHas('user_info', [
            'profile_id' => $this->profile->id,
            'user_name' => 'Jane Visitor',
            'user_email' => 'jane@example.com',
        ]);
    }

    public function test_form_answers_can_be_submitted(): void
    {
        $this->withoutMiddleware();

        FormBuilderQuestion::query()->create([
            'question_id' => 1,
            'profile_id' => $this->profile->id,
            'form_id' => 1,
            'question_type_id' => 1,
            'question_text' => 'Comments?',
            'question_order' => 1,
        ]);

        $this->from("/{$this->client->url}/{$this->profile->id}")
            ->post("/{$this->client->url}/{$this->profile->id}/form", [
                'answers' => [1 => 'All good'],
            ])->assertRedirect();

        $this->assertDatabaseHas('form_builder_answers', [
            'profile_id' => $this->profile->id,
            'question_id' => 1,
            'question_answer' => 'All good',
        ]);
    }

    public function test_marketing_routes_are_available(): void
    {
        $this->get('/pricing')->assertOk();
        $this->get('/faq')->assertOk();
        $this->get('/contact')->assertOk();
        $this->get('/privacy')->assertOk();
        $this->get('/terms')->assertOk();
    }
}
