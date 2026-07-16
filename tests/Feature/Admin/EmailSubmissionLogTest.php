<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailSubmissionLogTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'email' => 'admin@scanlink.com',
            'name' => 'ScanLink Admin',
        ]);
    }

    public function test_guests_are_redirected_from_email_submission_log(): void
    {
        $this->get('/admin/email-submission-log')
            ->assertRedirect('/admin/login');
    }

    public function test_admin_can_open_email_submission_log_page(): void
    {
        $this->actingAs($this->admin());

        $this->get('/admin/email-submission-log')
            ->assertOk()
            ->assertSee('Export email submissions')
            ->assertSee('Download CSV');
    }
}
