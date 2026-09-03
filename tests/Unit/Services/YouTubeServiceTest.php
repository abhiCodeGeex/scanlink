<?php

namespace Tests\Unit\Services;

use App\Models\Client;
use App\Models\Profile;
use App\Models\Video;
use App\Services\YouTubeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class YouTubeServiceTest extends TestCase
{
  use RefreshDatabase;

  public function test_parse_video_id_from_watch_url(): void
  {
    $service = app(YouTubeService::class);

    $this->assertSame(
      'dQw4w9WgXcQ',
      $service->parseVideoId('https://www.youtube.com/watch?v=dQw4w9WgXcQ'),
    );
  }

  public function test_parse_video_id_from_short_url(): void
  {
    $service = app(YouTubeService::class);

    $this->assertSame('dQw4w9WgXcQ', $service->parseVideoId('https://youtu.be/dQw4w9WgXcQ'));
  }

  public function test_delete_video_removes_database_row_when_not_shared(): void
  {
    $profile = Profile::factory()->create();
    $video = Video::query()->create([
      'client_id' => $profile->client_id,
      'user_id' => $profile->user_id,
      'profile_id' => $profile->id,
      'title' => 'Demo',
      'video_name' => 'abc12345678',
      'is_extra' => false,
    ]);

    app(YouTubeService::class)->deleteVideo($video);

    $this->assertDatabaseMissing('video', ['id' => $video->id]);
  }

  public function test_client_library_returns_unique_video_ids(): void
  {
    $client = Client::factory()->create();
    $profileA = Profile::factory()->create(['client_id' => $client->id]);
    $profileB = Profile::factory()->create(['client_id' => $client->id]);

    Video::query()->create([
      'client_id' => $client->id,
      'profile_id' => $profileA->id,
      'title' => 'One',
      'video_name' => 'sharedvideo1',
      'is_extra' => false,
    ]);

    Video::query()->create([
      'client_id' => $client->id,
      'profile_id' => $profileB->id,
      'title' => 'One duplicate id',
      'video_name' => 'sharedvideo1',
      'is_extra' => false,
    ]);

    $library = app(YouTubeService::class)->clientLibrary($client->id);

    $this->assertCount(1, $library);
    $this->assertSame('sharedvideo1', $library[0]['video_name']);
  }
}
