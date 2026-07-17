<?php

namespace Tests\Feature\Portal;

use App\Models\ChecklistItem;
use App\Models\Client;
use App\Models\FormBuilderQuestion;
use App\Models\FormBuilderQuestionOption;
use App\Models\Profile;
use App\Models\Weblink;
use Database\Seeders\Phase2Seeder;
use Database\Seeders\Phase3Seeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
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

        $this->withoutMiddleware([
            VerifyCsrfToken::class,
            PreventRequestForgery::class,
        ]);

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

    public function test_scan_page_shows_weblink_when_present(): void
    {
        Weblink::query()->create([
            'profile_id' => $this->profile->id,
            'link_button_text' => 'Safety datasheet',
            'link_button_url' => 'https://example.com/sds',
        ]);

        $this->get("/{$this->client->url}/{$this->profile->id}")
            ->assertOk()
            ->assertSee('Safety datasheet');
    }

    public function test_scan_page_shows_document_tile_when_present(): void
    {
        $this->profile->documents()->create([
            'client_id' => $this->client->id,
            'name' => 'Inspection PDF',
            'doc_name' => 'storage/documents/sample.pdf',
        ]);

        $this->get("/{$this->client->url}/{$this->profile->id}")
            ->assertOk()
            ->assertSee('Inspection PDF');
    }

    public function test_scan_page_shows_checklist_items(): void
    {
        ChecklistItem::query()->create([
            'profile_id' => $this->profile->id,
            'checklist_item' => 'Check tyre pressure',
        ]);

        $this->get("/{$this->client->url}/{$this->profile->id}")
            ->assertOk()
            ->assertSee('Checklist')
            ->assertSee('Check tyre pressure');
    }

    public function test_checklist_item_can_be_checked_and_unchecked(): void
    {
        $item = ChecklistItem::query()->create([
            'profile_id' => $this->profile->id,
            'checklist_item' => 'Inspect brakes',
        ]);

        $this->post("/{$this->client->url}/{$this->profile->id}/checklist/{$item->id}/check")
            ->assertRedirect("/{$this->client->url}/{$this->profile->id}");

        $this->assertNotNull($item->fresh()->datetime);

        $this->post("/{$this->client->url}/{$this->profile->id}/checklist/{$item->id}/uncheck")
            ->assertRedirect("/{$this->client->url}/{$this->profile->id}");

        $this->assertNull($item->fresh()->datetime);
    }

    public function test_scan_page_renders_radio_options_for_form_questions(): void
    {
        FormBuilderQuestion::query()->create([
            'question_id' => 10,
            'profile_id' => $this->profile->id,
            'form_id' => 1,
            'question_type_id' => 3,
            'question_text' => 'Condition?',
            'question_order' => 1,
        ]);

        FormBuilderQuestionOption::query()->create([
            'question_id' => 10,
            'option_name' => 'Good',
            'question_option_type_id' => 3,
        ]);

        $this->profile->update(['form_active' => true]);

        $this->get("/{$this->client->url}/{$this->profile->id}")
            ->assertOk()
            ->assertSee('Condition?')
            ->assertSee('Good');
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

    public function test_code_profile_with_destination_url_redirects_externally(): void
    {
        $codeType = \App\Models\EquipmentType::query()->where('slag', 'code')->firstOrFail();

        $this->profile->update([
            'type_id' => $codeType->id,
            'url' => 'https://example.com/destination',
        ]);

        $this->get("/{$this->client->url}/{$this->profile->id}")
            ->assertRedirect('https://example.com/destination');
    }

    public function test_marketing_routes_are_available(): void
    {
        $this->get('/pricing')->assertOk();
        $this->get('/faq')->assertOk();
        $this->get('/privacy')->assertOk();
        $this->get('/terms')->assertOk();
    }
}
