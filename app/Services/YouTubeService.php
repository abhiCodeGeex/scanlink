<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Video;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class YouTubeService
{
  /**
   * @return array{client_id: ?string, client_secret: ?string, refresh_token: ?string, developer_key: ?string}
   */
  public function credentials(): array
  {
    return once(function (): array {
      return [
        'client_id' => Setting::valueFor('youtube_client_id') ?: config('youtube.client_id'),
        'client_secret' => Setting::valueFor('youtube_client_secret') ?: config('youtube.client_secret'),
        'refresh_token' => Setting::valueFor('youtube_refresh_token') ?: config('youtube.refresh_token'),
        'developer_key' => Setting::valueFor('youtube_developer_key') ?: config('youtube.developer_key'),
      ];
    });
  }

  public function hasUploadCredentials(): bool
  {
    $credentials = $this->credentials();

    return filled($credentials['client_id'])
      && filled($credentials['client_secret'])
      && filled($credentials['refresh_token']);
  }

  public function parseVideoId(string $input): ?string
  {
    $input = trim($input);

    if ($input === '') {
      return null;
    }

    if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input)) {
      return $input;
    }

    // Path-style YouTube URLs: /embed/ID, /shorts/ID, /v/ID, or youtu.be/ID (with optional
    // trailing query like ?si=... or ?t=...).
    if (preg_match('~(?:youtube\.com/(?:embed|shorts|v)/|youtu\.be/)([a-zA-Z0-9_-]{11})~', $input, $matches)) {
      return $matches[1];
    }

    // watch URLs where v= may be anywhere in the query string (e.g. watch?app=desktop&v=ID).
    if (preg_match('~[?&]v=([a-zA-Z0-9_-]{11})~', $input, $matches)) {
      return $matches[1];
    }

    return null;
  }

  public function watchUrl(string $videoId): string
  {
    return 'https://www.youtube.com/watch?v='.$videoId;
  }

  /**
   * @return list<array{id: int, title: ?string, video_name: string}>
   */
  public function clientLibrary(int $clientId, ?int $excludeProfileId = null): array
  {
    return Video::query()
      ->where('client_id', $clientId)
      ->when($excludeProfileId, fn ($query) => $query->where('profile_id', '!=', $excludeProfileId))
      ->orderByDesc('created_at')
      ->get(['id', 'title', 'video_name', 'profile_id'])
      ->unique('video_name')
      ->values()
      ->map(fn (Video $video): array => [
        'id' => $video->id,
        'title' => $video->title,
        'video_name' => $video->video_name,
      ])
      ->all();
  }

  public function deleteVideo(Video $video): void
  {
    $videoId = $video->video_name;

    $stillUsed = Video::query()
      ->where('video_name', $videoId)
      ->whereKeyNot($video->getKey())
      ->exists();

    if (! $stillUsed && $this->hasUploadCredentials()) {
      $this->deleteFromYouTube($videoId);
    }

    $video->delete();
  }

  public function deleteFromYouTube(string $videoId): bool
  {
    $token = $this->accessToken();

    if ($token === null) {
      return false;
    }

    $response = $this->authorizedClient($token)
      ->delete('https://www.googleapis.com/youtube/v3/videos', [
        'id' => $videoId,
      ]);

    return $response->successful();
  }

  /**
   * @throws \RuntimeException
   */
  public function uploadVideo(string $diskPath, string $title, string $description = ''): string
  {
    if (! $this->hasUploadCredentials()) {
      throw new \RuntimeException('YouTube OAuth credentials are not configured.');
    }

    $token = $this->accessToken();

    if ($token === null) {
      throw new \RuntimeException('Unable to obtain a YouTube access token.');
    }

    $absolutePath = Storage::disk('local')->path($diskPath);

    if (! is_file($absolutePath)) {
      throw new \RuntimeException('Upload file was not found.');
    }

    $init = $this->authorizedClient($token)
      ->withHeaders(['X-Upload-Content-Type' => 'video/*'])
      ->post('https://www.googleapis.com/upload/youtube/v3/videos?uploadType=resumable&part=snippet,status', [
        'snippet' => [
          'title' => Str::limit($title, 100, ''),
          'description' => Str::limit($description, 5000, ''),
          'categoryId' => '22',
        ],
        'status' => [
          'privacyStatus' => 'unlisted',
        ],
      ]);

    if (! $init->successful()) {
      throw new \RuntimeException('YouTube upload session failed: '.$init->body());
    }

    $uploadUrl = $init->header('Location');

    if (blank($uploadUrl)) {
      throw new \RuntimeException('YouTube did not return an upload URL.');
    }

    $upload = Http::withToken($token)
      ->withHeaders(['Content-Type' => 'video/*'])
      ->withBody(file_get_contents($absolutePath) ?: '', 'video/*')
      ->put($uploadUrl);

    if (! $upload->successful()) {
      throw new \RuntimeException('YouTube video upload failed: '.$upload->body());
    }

    $videoId = $upload->json('id');

    if (! is_string($videoId) || $videoId === '') {
      throw new \RuntimeException('YouTube did not return a video id.');
    }

    return $videoId;
  }

  public function authorizationUrl(string $redirectUri): string
  {
    $clientId = $this->credentials()['client_id'];

    if (blank($clientId)) {
      throw new \RuntimeException('youtube_client_id is not configured.');
    }

    $query = http_build_query([
      'client_id' => $clientId,
      'redirect_uri' => $redirectUri,
      'response_type' => 'code',
      'scope' => 'https://www.googleapis.com/auth/youtube.upload https://www.googleapis.com/auth/youtube',
      'access_type' => 'offline',
      'prompt' => 'consent',
    ]);

    return 'https://accounts.google.com/o/oauth2/v2/auth?'.$query;
  }

  /**
   * @return array{access_token: string, refresh_token?: string}
   */
  public function exchangeAuthorizationCode(string $code, string $redirectUri): array
  {
    $credentials = $this->credentials();

    $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
      'code' => $code,
      'client_id' => $credentials['client_id'],
      'client_secret' => $credentials['client_secret'],
      'redirect_uri' => $redirectUri,
      'grant_type' => 'authorization_code',
    ]);

    if (! $response->successful()) {
      throw new \RuntimeException('OAuth token exchange failed: '.$response->body());
    }

    return $response->json();
  }

  protected function accessToken(): ?string
  {
    $credentials = $this->credentials();

    if (! filled($credentials['refresh_token'])) {
      return null;
    }

    $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
      'client_id' => $credentials['client_id'],
      'client_secret' => $credentials['client_secret'],
      'refresh_token' => $credentials['refresh_token'],
      'grant_type' => 'refresh_token',
    ]);

    if (! $response->successful()) {
      return null;
    }

    return $response->json('access_token');
  }

  protected function authorizedClient(string $token): PendingRequest
  {
    $request = Http::withToken($token)->acceptJson();

    $developerKey = $this->credentials()['developer_key'];

    if (filled($developerKey)) {
      $request = $request->withQueryParameters(['key' => $developerKey]);
    }

    return $request;
  }
}
