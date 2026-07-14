<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\YouTubeService;
use Illuminate\Console\Command;

class YouTubeAuthorize extends Command
{
  protected $signature = 'youtube:authorize {code? : Authorization code from Google redirect URL}';

  protected $description = 'Generate a YouTube OAuth refresh token for admin video uploads';

  public function handle(YouTubeService $youtube): int
  {
    $redirectUri = url('/oauth/youtube/callback');

    if (! $this->argument('code')) {
      $clientId = $youtube->credentials()['client_id'] ?? '';

      if (blank($clientId) || in_array($clientId, ['client-id', 'your-client-id', 'YOUR_CLIENT_ID'], true)) {
        $this->error('YouTube OAuth client id is missing or still the seeded placeholder "client-id".');
        $this->line('Fix: Admin → Global Settings → paste your real Google OAuth Web client id & secret.');
        $this->line('Then run: php artisan youtube:status');

        return self::FAILURE;
      }

      if (! str_contains($clientId, '.apps.googleusercontent.com')) {
        $this->warn('Warning: client id does not look like a Google Web OAuth id (.apps.googleusercontent.com).');
      }

      try {
        $url = $youtube->authorizationUrl($redirectUri);
      } catch (\Throwable $exception) {
        $this->error($exception->getMessage());

        return self::FAILURE;
      }

      $this->line('1. Add this redirect URI in Google Cloud Console → OAuth client:');
      $this->line('   '.$redirectUri);
      $this->newLine();
      $this->line('2. Open this URL and approve access:');
      $this->newLine();
      $this->line($url);
      $this->newLine();
      $this->line('3. After redirect, the refresh token is saved automatically.');
      $this->line('   Or run: php artisan youtube:authorize PASTE_CODE_HERE');

      return self::SUCCESS;
    }

    try {
      $tokens = $youtube->exchangeAuthorizationCode($this->argument('code'), $redirectUri);
    } catch (\Throwable $exception) {
      $this->error($exception->getMessage());

      return self::FAILURE;
    }

    if (empty($tokens['refresh_token'])) {
      $this->error('No refresh_token returned. Revoke app access in Google Account settings and run again with prompt=consent.');

      return self::FAILURE;
    }

    Setting::setValue('youtube_refresh_token', $tokens['refresh_token']);

    $this->info('YouTube refresh token saved to settings (youtube_refresh_token).');
    $this->line('Upload to YouTube is now available on profile edit → Videos tab.');

    return self::SUCCESS;
  }
}
