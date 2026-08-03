<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('how_to_tutorials')) {
            Schema::create('how_to_tutorials', function (Blueprint $table): void {
                $table->id();
                $table->string('title');
                $table->string('url', 500);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->unique('title');
            });
        }

        if (Schema::hasTable('how_to_tutorials') && DB::table('how_to_tutorials')->count() === 0) {
            $now = now();
            $rows = [];

            foreach ($this->defaults() as $index => $item) {
                $rows[] = [
                    'title' => $item['title'],
                    'url' => $item['url'],
                    'sort_order' => $index,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('how_to_tutorials')->insert($rows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('how_to_tutorials');
    }

    /**
     * @return list<array{title: string, url: string}>
     */
    private function defaults(): array
    {
        return [
            ['title' => 'Create a ScanLink account', 'url' => 'https://www.youtube.com/embed/9aTjweHyAWw?rel=0'],
            ['title' => 'Getting Started', 'url' => 'https://www.youtube.com/embed/o6NxTt0CmYI?rel=0'],
            ['title' => 'Register a new code', 'url' => 'https://www.youtube.com/embed/GZ12nXTO7_w?rel=0'],
            ['title' => 'Upload a logo', 'url' => 'https://www.youtube.com/embed/hGrBYsys2Oo?rel=0'],
            ['title' => 'Upload a video', 'url' => 'https://www.youtube.com/embed/H33caspIlcc?rel=0'],
            ['title' => 'Add text and phone numbers', 'url' => 'https://www.youtube.com/embed/CZx8xplEfoU?rel=0'],
            ['title' => 'Upload pictures', 'url' => 'https://www.youtube.com/embed/GshHCp9F0wU?rel=0'],
            ['title' => 'Upload documents', 'url' => 'https://www.youtube.com/embed/ujiEr65yg30?rel=0'],
            ['title' => 'Add web link buttons', 'url' => 'https://www.youtube.com/embed/id0I8j8RTuY?rel=0'],
            ['title' => 'Add social media and email share buttons', 'url' => 'https://www.youtube.com/embed/qOi6tSBsII4?rel=0'],
            ['title' => 'Create pop up messages to collect data', 'url' => 'https://www.youtube.com/embed/C_vH14MFtXA?rel=0'],
            ['title' => 'Select a code type - QR or Data matrix', 'url' => 'https://www.youtube.com/embed/jCeyQOfm7uc?rel=0'],
            ['title' => 'Feature code profile number on mobile display', 'url' => 'https://www.youtube.com/embed/eJtzHbZoCPw?rel=0'],
            ['title' => 'Password protect a code profile', 'url' => 'https://www.youtube.com/embed/KcXJnxuMVyc?rel=0'],
            ['title' => 'Link a code to a URL', 'url' => 'https://www.youtube.com/embed/uEDTnBPUk28?rel=0'],
            ['title' => 'Delete a code profile', 'url' => 'https://www.youtube.com/embed/Gu12cnKn16s?rel=0'],
            ['title' => 'View and download scan activity', 'url' => 'https://www.youtube.com/embed/Y0bVkzDA5Rc?rel=0'],
            ['title' => 'Create a form', 'url' => 'https://www.youtube.com/embed/cYQnzxkp528?rel=0'],
        ];
    }
};
