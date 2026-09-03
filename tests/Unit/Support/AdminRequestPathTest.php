<?php

namespace Tests\Unit\Support;

use App\Support\AdminRequestPath;
use Illuminate\Http\Request;
use Tests\TestCase;

class AdminRequestPathTest extends TestCase
{
    public function test_uses_request_path_on_normal_admin_get(): void
    {
        $this->app->instance('request', Request::create('/admin/form-builder-orders/42', 'GET'));

        $this->assertSame('admin/form-builder-orders/42', AdminRequestPath::current());

        $state = AdminRequestPath::backButtonState();
        $this->assertTrue($state['show']);
        $this->assertSame('form-builder-orders', $state['resource']);
    }

    public function test_resolves_admin_path_from_referer_on_livewire_update(): void
    {
        $request = Request::create('/livewire/update', 'POST', [], [], [], [
            'HTTP_REFERER' => 'http://localhost:8000/admin/form-builder-orders/42',
        ]);
        $this->app->instance('request', $request);

        $this->assertSame('admin/form-builder-orders/42', AdminRequestPath::current());

        $state = AdminRequestPath::backButtonState();
        $this->assertTrue($state['show']);
        $this->assertSame('form-builder-orders', $state['resource']);
    }

    public function test_resolves_admin_path_from_livewire_snapshot_memo(): void
    {
        $snapshot = json_encode([
            'memo' => [
                'path' => '/admin/orders/1180/edit',
            ],
        ], JSON_THROW_ON_ERROR);

        $request = Request::create('/livewire/update', 'POST', [
            'components' => [
                ['snapshot' => $snapshot],
            ],
        ]);
        $this->app->instance('request', $request);

        $this->assertSame('admin/orders/1180/edit', AdminRequestPath::current());

        $state = AdminRequestPath::backButtonState();
        $this->assertTrue($state['show']);
        $this->assertSame('orders', $state['resource']);
    }

    public function test_hides_back_on_list_and_dashboard(): void
    {
        $this->app->instance('request', Request::create('/admin/clients', 'GET'));
        $this->assertFalse(AdminRequestPath::backButtonState()['show']);

        $this->app->instance('request', Request::create('/admin', 'GET'));
        $this->assertFalse(AdminRequestPath::backButtonState()['show']);
    }

    public function test_hides_back_on_add_client_but_shows_on_other_create_pages(): void
    {
        $this->app->instance('request', Request::create('/admin/clients/create', 'GET'));
        $this->assertFalse(AdminRequestPath::backButtonState()['show']);

        $this->app->instance('request', Request::create('/admin/orders/create', 'GET'));
        $this->assertTrue(AdminRequestPath::backButtonState()['show']);
    }
}
