<?php

namespace Tests\Unit\Services;

use App\Models\Client;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\QrImage;
use App\Services\ProfileQrService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileQrServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generates_qr_image_record_and_file(): void
    {
        Storage::fake('public');

        $client = Client::factory()->create(['url' => 'acme-test']);
        $type = EquipmentType::factory()->create(['slag' => 'asset', 'name' => 'Asset']);

        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'type_id' => $type->id,
            'code_type' => '0',
        ]);

        $qrImage = app(ProfileQrService::class)->generateFor($profile);

        $this->assertInstanceOf(QrImage::class, $qrImage);
        $this->assertDatabaseHas('qrimage', [
            'profile_id' => $profile->id,
        ]);
        $this->assertFileExists(storage_path('app/public/'.$qrImage->diskPath()));
    }

    public function test_applies_color_and_regenerates(): void
    {
        Storage::fake('public');

        $client = Client::factory()->create(['url' => 'color-client']);
        $type = EquipmentType::factory()->create(['slag' => 'code', 'name' => 'Code']);

        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'type_id' => $type->id,
            'code_type' => '0',
            'color_code' => null,
        ]);

        $service = app(ProfileQrService::class);
        $service->applyColorAndRegenerate($profile, '#FF0000');

        $profile->refresh();

        $this->assertSame('#FF0000', $profile->color_code);
        $this->assertNotNull($profile->qrImage);
        $this->assertFileExists(storage_path('app/public/'.$profile->qrImage->diskPath()));
    }

    public function test_preview_data_uri_returns_png_data_uri(): void
    {
        $uri = app(ProfileQrService::class)->previewDataUri('https://example.com/custom');

        $this->assertIsString($uri);
        $this->assertStringStartsWith('data:image/png;base64,', $uri);
    }

    public function test_download_pdf_streams_pdf(): void
    {
        Storage::fake('public');

        $client = Client::factory()->create(['url' => 'pdf-client']);
        $type = EquipmentType::factory()->create(['slag' => 'code', 'name' => 'Code']);

        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'type_id' => $type->id,
            'name' => 'PDF Profile',
            'url' => 'https://example.com/dest',
            'code_type' => '0',
        ]);

        $response = app(ProfileQrService::class)->downloadPdf($profile);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('Content-Type'));
    }
}
