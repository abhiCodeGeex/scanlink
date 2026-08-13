<?php

namespace App\Console\Commands;

use App\Services\YouTubeService;
use Illuminate\Console\Command;

class YouTubeStatus extends Command
{
  protected $signature = 'youtube:status';

  protected $description = 'Show whether YouTube OAuth credentials are configured correctly';

  public function handle(YouTubeService $youtube): int
  {
    $credentials = $youtube->credentials();
    $clientId = $credentials['client_id'] ?? '';
    $clientSecret = $credentials['client_secret'] ?? '';
    $refreshToken = $credentials['refresh_token'] ?? '';

    $this->line('YouTube credential check');
    $this->line('Redirect URI: '.url('/oauth/youtube/callback'));
    $this->newLine();

    $this->line('youtube_client_id: '.$this->mask($clientId));

    if ($this->isPlaceholderClientId($clientId)) {
      $this->error('  ✗ Invalid — still using the seeded placeholder "client-id".');
      $this->line('    Create a Web OAuth client in Google Cloud Console and paste the real Client ID in Global Settings.');
    } elseif (! str_contains($clientId, '.apps.googleusercontent.com')) {
      $this->warn('  ! Unusual format — Web OAuth client IDs usually end with .apps.googleusercontent.com');
    } else {
      $this->info('  ✓ Format looks valid');
    }

    $this->line('youtube_client_secret: '.($clientSecret !== '' ? $this->mask($clientSecret) : '(empty)'));

    if ($clientSecret === '' || $clientSecret === 'client-secret') {
      $this->error('  ✗ Missing or still using placeholder secret.');
    } else {
      $this->info('  ✓ Set');
    }

    $this->line('youtube_refresh_token: '.($refreshToken !== '' ? $this->mask($refreshToken) : '(empty)'));

    if ($refreshToken === '') {
      $this->warn('  ! Not set yet — run `php artisan youtube:authorize` after fixing client id/secret.');
    } else {
      $this->info('  ✓ Set');
    }

    $this->newLine();

    $ready = $youtube->hasUploadCredentials()
      && ! $this->isPlaceholderClientId($clientId)
      && $clientSecret !== ''
      && $clientSecret !== 'client-secret'
      && ! str_starts_with($refreshToken, 'GOCSPX');

    if ($ready) {
      $this->info('Upload to YouTube: READY');

      return self::SUCCESS;
    }

    if (str_starts_with($refreshToken, 'GOCSPX')) {
      $this->error('youtube_refresh_token looks like a client secret (GOCSPX...). Run `php artisan youtube:authorize` after fixing client id/secret.');
    }

    $this->warn('Upload to YouTube: NOT READY');
    $this->line('You can still add videos by pasting a YouTube URL on profile edit → Videos tab.');

    return self::FAILURE;
  }

  protected function isPlaceholderClientId(?string $clientId): bool
  {
    if (blank($clientId)) {
      return true;
    }

    return in_array($clientId, ['client-id', 'your-client-id', 'YOUR_CLIENT_ID'], true);
  }

  protected function mask(string $value): string
  {
    if (strlen($value) <= 8) {
      return str_repeat('*', strlen($value));
    }

    return substr($value, 0, 6).'...'.substr($value, -4);
  }
}
