<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\HowToTutorials;
use App\Models\HowToTutorial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HowToTutorialsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_how_to_items_and_reject_duplicate_titles(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(HowToTutorials::class)
            ->fillForm([
                'tutorials' => [
                    ['title' => 'Upload a logo', 'url' => 'https://www.youtube.com/watch?v=hGrBYsys2Oo'],
                    ['title' => 'Create a form', 'url' => 'https://www.youtube.com/embed/cYQnzxkp528'],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseCount('how_to_tutorials', 2);
        $this->assertDatabaseHas('how_to_tutorials', [
            'title' => 'Upload a logo',
            'url' => 'https://www.youtube.com/embed/hGrBYsys2Oo?rel=0',
        ]);

        Livewire::test(HowToTutorials::class)
            ->fillForm([
                'tutorials' => [
                    ['title' => 'Upload a logo', 'url' => 'https://www.youtube.com/embed/aaa'],
                    ['title' => 'upload a logo', 'url' => 'https://www.youtube.com/embed/bbb'],
                ],
            ])
            ->call('save')
            ->assertHasFormErrors(['tutorials.1.title']);

        $this->assertSame(2, HowToTutorial::query()->count());
        $this->assertCount(2, HowToTutorial::catalog());
    }
}
