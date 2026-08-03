<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class HowToTutorial extends Model
{
    protected $fillable = [
        'title',
        'url',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /**
     * Hardcoded legacy defaults (menu.php / marketing How to).
     *
     * @return list<array{title: string, url: string}>
     */
    public static function defaults(): array
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

    /**
     * Ordered catalog for nav / marketing. Falls back to defaults if table empty/missing.
     *
     * @return list<array{title: string, url: string}>
     */
    public static function catalog(): array
    {
        if (! Schema::hasTable('how_to_tutorials')) {
            return static::defaults();
        }

        /** @var Collection<int, self> $rows */
        $rows = static::query()->orderBy('sort_order')->orderBy('id')->get(['title', 'url']);

        if ($rows->isEmpty()) {
            return static::defaults();
        }

        return $rows
            ->map(fn (self $row): array => [
                'title' => (string) $row->title,
                'url' => (string) $row->url,
            ])
            ->values()
            ->all();
    }

    /**
     * Normalize common YouTube watch/share URLs to embed form used by the How to popup.
     */
    public static function normalizeUrl(string $url): string
    {
        $url = trim($url);

        if ($url === '') {
            return $url;
        }

        if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/)([A-Za-z0-9_-]{6,})~', $url, $matches)) {
            return 'https://www.youtube.com/embed/'.$matches[1].'?rel=0';
        }

        if (preg_match('~youtube\.com/embed/([A-Za-z0-9_-]{6,})~', $url, $matches)) {
            return 'https://www.youtube.com/embed/'.$matches[1].'?rel=0';
        }

        return $url;
    }
}
