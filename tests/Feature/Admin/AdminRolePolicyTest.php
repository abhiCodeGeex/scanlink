<?php

namespace Tests\Feature\Admin;

use App\Enums\AdminRole;
use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminRolePolicyTest extends TestCase
{
  use RefreshDatabase;

  public function test_support_user_cannot_create_clients(): void
  {
    $support = User::factory()->create([
      'admin_role' => AdminRole::Support,
    ]);

    $this->actingAs($support);

    $this->assertFalse(\App\Filament\Resources\Clients\ClientResource::canCreate());
  }

  public function test_support_user_cannot_mutate_cms_or_orders(): void
  {
    $support = User::factory()->create([
      'admin_role' => AdminRole::Support,
    ]);

    $this->actingAs($support);

    $this->assertFalse(\App\Filament\Resources\Galleries\GalleryResource::canCreate());
    $this->assertFalse(\App\Filament\Resources\Galleries\GalleryResource::canDelete(new \App\Models\Gallery));
    $this->assertFalse(\App\Filament\Resources\Testimonials\TestimonialResource::canCreate());
    $this->assertFalse(\App\Filament\Resources\Testimonials\TestimonialResource::canEdit(new \App\Models\Testimonial));
  }

  public function test_admin_user_can_create_clients(): void
  {
    $admin = User::factory()->create([
      'admin_role' => AdminRole::Admin,
    ]);

    $this->actingAs($admin);

    Livewire::test(CreateClient::class)
      ->assertSuccessful();
  }

  public function test_support_user_can_view_profiles_list(): void
  {
    $support = User::factory()->create([
      'admin_role' => AdminRole::Support,
    ]);

    $this->actingAs($support)
      ->get('/admin/profiles')
      ->assertSuccessful();
  }
}
