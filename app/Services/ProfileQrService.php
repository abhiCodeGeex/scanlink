<?php

namespace App\Services;

use App\Enums\ProfileCodeType;
use App\Models\Profile;
use App\Models\QrImage;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use TCPDF;

class ProfileQrService
{
    public function profileUrl(Profile $profile): string
    {
        $profile->loadMissing('client');

        $base = rtrim(config('scanlink.portal_url'), '/');
        $clientUrl = $profile->client?->url;

        // Orphan profile (client missing/deleted): no valid public scan path. Return a clean
        // base/id URL instead of a double-slashed one — and avoid a "property on null" warning.
        if (blank($clientUrl)) {
            return $base.'/'.$profile->id;
        }

        return $base.'/'.$clientUrl.'/'.$profile->id;
    }

    public function resolveQrData(Profile $profile): string
    {
        if (filled($profile->shorturl)) {
            return $profile->shorturl;
        }

        $profile->loadMissing(['equipmentType', 'client']);

        // CustomQR encodes the destination URL (profiles.name), not the portal path.
        $isCustomQr = $profile->typeSlug() === 'customqr' && filled($profile->name);
        $targetUrl = $isCustomQr ? $profile->name : $this->profileUrl($profile);

        // Don't shorten an orphan profile (no linked client) — it has no valid scan
        // destination yet, so a short link would just point at a dead URL. Fall back to raw.
        $canShorten = $isCustomQr || filled($profile->client?->url);

        $token = config('scanlink.short_url_api_token');

        if ($canShorten && $token) {
            $short = $this->shortenUrl($targetUrl, $token);

            if ($short) {
                $profile->update(['shorturl' => $short]);

                return $short;
            }
        }

        return $targetUrl;
    }

    public function generateFor(Profile $profile): QrImage
    {
        $data = $this->resolveQrData($profile);
        $codeType = $profile->code_type ?? ProfileCodeType::QrCode;

        if ($codeType === ProfileCodeType::DataMatrix) {
            return $this->generateDataMatrix($profile, $data);
        }

        return $this->generateQrCode($profile, $data);
    }

    public function generateQrCode(Profile $profile, string $data): QrImage
    {
        $relativePath = config('scanlink.qr_path').'/CSQRIMG'.$profile->id.'.png';
        $fullPath = storage_path('app/public/'.$relativePath);

        $this->ensureWritableFile($fullPath);

        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'scale' => 8,
            'imageBase64' => false,
            'moduleValues' => $this->moduleValuesForColor($profile->color_code),
        ]);

        (new QRCode($options))->render($data, $fullPath);

        return $this->saveRecord($profile, 'storage/'.$relativePath);
    }

    public function generateDataMatrix(Profile $profile, string $data): QrImage
    {
        $relativePath = config('scanlink.dm_path').'/DMIMG'.$profile->id.'.png';
        $fullPath = storage_path('app/public/'.$relativePath);

        $this->ensureWritableFile($fullPath);

        // Real ECC200 Data Matrix (legacy dm_code/index.php parity) via TCPDF's 2D encoder.
        $barcode = new \TCPDF2DBarcode($data, 'DATAMATRIX');
        $png = $barcode->getBarcodePngData(8, 8, $this->colorToRgb($profile->color_code));

        if (! is_string($png) || $png === '') {
            throw new \RuntimeException('Failed to generate Data Matrix image.');
        }

        // Add a white quiet zone (2 modules) so scanners reliably read the code.
        $src = @imagecreatefromstring($png);
        if ($src !== false) {
            $quiet = 16;
            $w = imagesx($src);
            $h = imagesy($src);
            $dst = imagecreatetruecolor($w + 2 * $quiet, $h + 2 * $quiet);
            $white = imagecolorallocate($dst, 255, 255, 255);
            imagefill($dst, 0, 0, $white);
            imagecopy($dst, $src, $quiet, $quiet, 0, 0, $w, $h);
            ob_start();
            imagepng($dst);
            $bordered = ob_get_clean();
            imagedestroy($src);
            imagedestroy($dst);

            if (is_string($bordered) && $bordered !== '') {
                $png = $bordered;
            }
        }

        file_put_contents($fullPath, $png);

        return $this->saveRecord($profile, 'storage/'.$relativePath);
    }

    /**
     * @return array{0: int, 1: int, 2: int} foreground RGB (defaults to black)
     */
    private function colorToRgb(?string $colorCode): array
    {
        $hex = ltrim(trim((string) $colorCode), '#');

        if (! preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            return [0, 0, 0];
        }

        return [
            (int) hexdec(substr($hex, 0, 2)),
            (int) hexdec(substr($hex, 2, 2)),
            (int) hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Live preview (CustomQR create/edit) — data URI, no filesystem write.
     */
    public function previewDataUri(string $data, ?string $colorCode = null): ?string
    {
        $data = trim($data);

        if ($data === '') {
            return null;
        }

        $cacheKey = 'qr.preview.'.hash('xxh3', $data.'|'.($colorCode ?? ''));

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($data, $colorCode): ?string {
            $options = new QROptions([
                'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                'scale' => 5,
                'imageBase64' => true,
                'moduleValues' => $this->moduleValuesForColor($colorCode),
            ]);

            $rendered = (new QRCode($options))->render($data);

            return is_string($rendered) ? $rendered : null;
        });
    }

    /**
     * Express Code Generator (public home page) — parity with legacy qrhome.
     * Builds a QR/DM PNG from a raw URL with NO Profile and NO persistence.
     *
     * @param  int  $codeType  0 = QR, 1 = Data Matrix
     */
    public function expressPngBinary(string $data, int $codeType, ?string $colorCode = null): string
    {
        $data = trim($data);

        if ($data === '') {
            throw new \InvalidArgumentException('No data to encode.');
        }

        if ($codeType === 1) {
            $barcode = new \TCPDF2DBarcode($data, 'DATAMATRIX');
            $png = $barcode->getBarcodePngData(8, 8, $this->colorToRgb($colorCode));

            if (! is_string($png) || $png === '') {
                throw new \RuntimeException('Failed to generate Data Matrix image.');
            }

            // White quiet zone (2 modules) so scanners read the code reliably.
            $src = @imagecreatefromstring($png);
            if ($src !== false) {
                $quiet = 16;
                $w = imagesx($src);
                $h = imagesy($src);
                $dst = imagecreatetruecolor($w + 2 * $quiet, $h + 2 * $quiet);
                $white = imagecolorallocate($dst, 255, 255, 255);
                imagefill($dst, 0, 0, $white);
                imagecopy($dst, $src, $quiet, $quiet, 0, 0, $w, $h);
                ob_start();
                imagepng($dst);
                $bordered = ob_get_clean();
                imagedestroy($src);
                imagedestroy($dst);

                if (is_string($bordered) && $bordered !== '') {
                    $png = $bordered;
                }
            }

            return $png;
        }

        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'scale' => 8,
            'imageBase64' => false,
            'moduleValues' => $this->moduleValuesForColor($colorCode),
        ]);

        $rendered = (new QRCode($options))->render($data);

        if (! is_string($rendered) || $rendered === '') {
            throw new \RuntimeException('Failed to generate QR image.');
        }

        return $rendered;
    }

    /** Data URI for the express live preview (base64 PNG). */
    public function expressPreviewDataUri(string $data, int $codeType, ?string $colorCode = null): ?string
    {
        if (trim($data) === '') {
            return null;
        }

        return 'data:image/png;base64,'.base64_encode($this->expressPngBinary($data, $codeType, $colorCode));
    }

    /**
     * Stream an express code as a download. Supports pdf|png|jpg (TIFF/EPS need
     * ImageMagick, which is not installed — same limitation as the portal).
     */
    public function expressDownload(string $data, int $codeType, string $format): StreamedResponse
    {
        $format = strtolower(trim($format));
        $png = $this->expressPngBinary($data, $codeType, null);

        [$binary, $filename, $mime] = match ($format) {
            'png' => [$png, 'scanlink-code.png', 'image/png'],
            'jpg', 'jpeg' => [$this->pngBinaryToJpg($png), 'scanlink-code.jpg', 'image/jpeg'],
            'pdf' => [$this->pngBinaryToPdf($png, $data), 'scanlink-code.pdf', 'application/pdf'],
            default => throw new \InvalidArgumentException('Unsupported express format: '.$format),
        };

        return response()->streamDownload(
            function () use ($binary): void {
                echo $binary;
            },
            $filename,
            ['Content-Type' => $mime],
        );
    }

    protected function pngBinaryToJpg(string $png): string
    {
        $src = @imagecreatefromstring($png);

        if ($src === false) {
            throw new \RuntimeException('Could not read PNG for JPG conversion.');
        }

        $canvas = imagecreatetruecolor(imagesx($src), imagesy($src));
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefilledrectangle($canvas, 0, 0, imagesx($src), imagesy($src), $white);
        imagecopy($canvas, $src, 0, 0, 0, 0, imagesx($src), imagesy($src));
        ob_start();
        imagejpeg($canvas, null, 92);
        $binary = (string) ob_get_clean();
        imagedestroy($src);
        imagedestroy($canvas);

        return $binary;
    }

    protected function pngBinaryToPdf(string $png, string $url): string
    {
        $pdf = new TCPDF;
        $pdf->SetCreator('ScanLink');
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'ScanLink Express Code', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 11);
        $pdf->MultiCell(0, 8, 'URL: '.$url, 0, 'C');
        $pdf->Ln(6);
        $pdf->Image('@'.$png, 75, 60, 60, 60, 'PNG');

        return (string) $pdf->Output('scanlink-code.pdf', 'S');
    }

    public function applyColorAndRegenerate(Profile $profile, string $colorCode): QrImage
    {
        $normalized = $this->normalizeColor($colorCode) ?? '#000000';

        $profile->update(['color_code' => $normalized]);

        return $this->generateFor($profile->fresh());
    }

    public function downloadPdf(Profile $profile): StreamedResponse
    {
        $profile->loadMissing(['client', 'qrImage']);
        $this->ensureQrFile($profile);

        $qrAbsolute = Storage::disk('public')->path($profile->qrImage->diskPath());

        $pdf = new TCPDF;
        $pdf->SetCreator('ScanLink');
        $pdf->SetAuthor('ScanLink');
        $pdf->SetTitle('Code '.$profile->id);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();

        $logoCandidates = [
            public_path('images/logo.png'),
            public_path('images/scanlink-logo.png'),
            resource_path('images/logo.png'),
        ];

        foreach ($logoCandidates as $logoPath) {
            if (is_file($logoPath)) {
                $pdf->Image($logoPath, 85, 12, 40, 0, '', '', '', false, 300);

                break;
            }
        }

        $pdf->SetFont('helvetica', '', 14);
        $pdf->SetY(45);
        $pdf->Cell(0, 10, 'Profile No. : '.$profile->id, 0, 1);
        // Prefer the code's profile name (many code types leave `name` blank and store the
        // display name in code_profile_name), falling back to name / application.
        $qrName = trim((string) ($profile->code_profile_name ?: $profile->name ?: $profile->application));
        $pdf->Cell(0, 10, 'Name : '.($qrName !== '' ? $qrName : '-'), 0, 1);

        $url = $profile->url ?: $this->profileUrl($profile);
        $pdf->MultiCell(0, 10, 'URL :'.$url, 0, 'L');

        if (is_file($qrAbsolute)) {
            $pdf->Image($qrAbsolute, 85, 90, 40, 0, 'PNG');
        }

        $binary = $pdf->Output('code-'.$profile->id.'.pdf', 'S');

        return response()->streamDownload(
            function () use ($binary): void {
                echo $binary;
            },
            'code-'.$profile->id.'.pdf',
            ['Content-Type' => 'application/pdf'],
        );
    }

    public function downloadQrImage(Profile $profile): StreamedResponse
    {
        $profile->loadMissing('qrImage');
        $this->ensureQrFile($profile);

        $path = $profile->qrImage->diskPath();

        return Storage::disk('public')->download($path, basename($path));
    }

    /**
     * Legacy dashboard/getQR download_as formats: pdf|png|jpg|tiff|eps.
     */
    public function downloadAs(Profile $profile, string $format): StreamedResponse
    {
        $format = strtolower(trim($format));

        return match ($format) {
            'pdf' => $this->downloadPdf($profile),
            'png' => $this->downloadQrImage($profile),
            'jpg', 'jpeg' => $this->downloadConvertedImage($profile, 'jpg', 'image/jpeg'),
            'tiff', 'tif' => $this->downloadConvertedImage($profile, 'tiff', 'image/tiff'),
            'eps' => $this->downloadConvertedImage($profile, 'eps', 'application/postscript'),
            default => throw new \InvalidArgumentException('Unsupported download format: '.$format),
        };
    }

    protected function downloadConvertedImage(Profile $profile, string $extension, string $mime): StreamedResponse
    {
        $profile->loadMissing('qrImage');
        $this->ensureQrFile($profile);

        $sourcePath = Storage::disk('public')->path($profile->qrImage->diskPath());

        if (! is_file($sourcePath)) {
            throw new \RuntimeException('QR image file is missing.');
        }

        $binary = $this->convertQrImage($sourcePath, $extension);
        $filename = 'code_'.$profile->id.'.'.$extension;

        return response()->streamDownload(
            function () use ($binary): void {
                echo $binary;
            },
            $filename,
            ['Content-Type' => $mime],
        );
    }

    /**
     * Public entry point: guarantee a QR image whose PNG actually exists on disk,
     * regenerating stale legacy rows (row present, file never imported). Returns the
     * QrImage, or null if generation was not possible (caller shows the URL fallback).
     */
    public function ensureFor(Profile $profile): ?QrImage
    {
        try {
            $this->ensureQrFile($profile);
        } catch (\Throwable) {
            return null;
        }

        return $profile->qrImage;
    }

    /**
     * Live DB often has qrimage rows whose PNG files were never imported.
     * Regenerate on demand so Download / PDF / format exports keep working.
     */
    protected function ensureQrFile(Profile $profile): void
    {
        $profile->loadMissing('qrImage');

        $needsGenerate = $profile->qrImage === null;

        if (! $needsGenerate) {
            $relative = $profile->qrImage->diskPath();
            $needsGenerate = $relative === '' || ! Storage::disk('public')->exists($relative);
        }

        // A code generated before URL shortening was configured still carries a long-URL QR
        // PNG (blank shorturl). If a TinyURL token is now configured and this profile can be
        // shortened, regenerate so the stored image encodes the short link. A short cache lock
        // keeps a flaky/unreachable API from triggering a fresh 10s HTTP call — and a rewrite —
        // on every display/download. (The public scan path never calls this, only the editor
        // and the download/PDF exports.)
        $shortenLock = 'qr.shorten.attempt.'.$profile->id;

        if (
            ! $needsGenerate
            && blank($profile->shorturl)
            && filled(config('scanlink.short_url_api_token'))
            && $this->canShorten($profile)
            && ! Cache::has($shortenLock)
        ) {
            $needsGenerate = true;
        }

        if ($needsGenerate) {
            $this->generateFor($profile);
            $profile->unsetRelation('qrImage');
            $profile->load('qrImage');

            // Still no short link => the API is down/blocked. Back off for 10 minutes so we
            // don't re-attempt (and rewrite the PNG) on every subsequent view/download.
            if (blank($profile->shorturl)) {
                Cache::put($shortenLock, true, now()->addMinutes(10));
            }
        }

        if ($profile->qrImage === null) {
            throw new \RuntimeException('Unable to generate QR image for profile '.$profile->id);
        }
    }

    /**
     * Whether this profile has a real scan destination that can be shortened — mirrors the
     * gate in resolveQrData(): a CustomQR with a target, or a linked client that has a URL.
     */
    protected function canShorten(Profile $profile): bool
    {
        $profile->loadMissing('client');

        if ($profile->typeSlug() === 'customqr' && filled($profile->name)) {
            return true;
        }

        return filled($profile->client?->url);
    }

    protected function convertQrImage(string $sourcePath, string $extension): string
    {
        if (extension_loaded('imagick') && class_exists(\Imagick::class)) {
            $image = new \Imagick($sourcePath);
            $image->setImageFormat($extension === 'jpg' ? 'jpeg' : $extension);
            $binary = $image->getImageBlob();
            $image->clear();
            $image->destroy();

            return $binary;
        }

        if ($extension === 'jpg') {
            $png = @imagecreatefrompng($sourcePath);

            if ($png === false) {
                throw new \RuntimeException('Could not read QR PNG for JPG conversion.');
            }

            $canvas = imagecreatetruecolor(imagesx($png), imagesy($png));
            $white = imagecolorallocate($canvas, 255, 255, 255);
            imagefilledrectangle($canvas, 0, 0, imagesx($png), imagesy($png), $white);
            imagecopy($canvas, $png, 0, 0, 0, 0, imagesx($png), imagesy($png));

            ob_start();
            imagejpeg($canvas, null, 92);
            $binary = (string) ob_get_clean();

            imagedestroy($png);
            imagedestroy($canvas);

            return $binary;
        }

        // TIFF/EPS need ImageMagick. Rather than hand out a PNG under a .tiff/.eps name
        // (a corrupt file), fail clearly so the UI can ask for a supported format.
        throw new \App\Exceptions\UnsupportedDownloadFormatException(
            'The '.strtoupper($extension).' format requires ImageMagick, which is not available on this server. '
            .'Please choose PDF, PNG or JPG instead.'
        );
    }

    /**
     * @return array<int, array{0: int, 1: int, 2: int}>
     */
    protected function moduleValuesForColor(?string $colorCode): array
    {
        $rgb = $this->hexToRgb($colorCode) ?? [0, 0, 0];
        $light = [255, 255, 255];

        $darkTypes = [
            QRMatrix::M_DATA_DARK,
            QRMatrix::M_FINDER_DARK,
            QRMatrix::M_ALIGNMENT_DARK,
            QRMatrix::M_TIMING_DARK,
            QRMatrix::M_FORMAT_DARK,
            QRMatrix::M_VERSION_DARK,
            QRMatrix::M_DARKMODULE,
            QRMatrix::M_FINDER_DOT,
            QRMatrix::M_SEPARATOR_DARK,
            QRMatrix::M_QUIETZONE_DARK,
            QRMatrix::M_LOGO_DARK,
        ];

        $lightTypes = [
            QRMatrix::M_DATA,
            QRMatrix::M_FINDER,
            QRMatrix::M_ALIGNMENT,
            QRMatrix::M_TIMING,
            QRMatrix::M_FORMAT,
            QRMatrix::M_VERSION,
            QRMatrix::M_QUIETZONE,
            QRMatrix::M_SEPARATOR,
            QRMatrix::M_DARKMODULE_LIGHT,
            QRMatrix::M_FINDER_DOT_LIGHT,
            QRMatrix::M_LOGO,
        ];

        $values = [];

        foreach ($darkTypes as $type) {
            $values[$type] = $rgb;
        }

        foreach ($lightTypes as $type) {
            $values[$type] = $light;
        }

        return $values;
    }

    /**
     * @return array{0: int, 1: int, 2: int}|null
     */
    protected function hexToRgb(?string $colorCode): ?array
    {
        $normalized = $this->normalizeColor($colorCode);

        if ($normalized === null) {
            return null;
        }

        $hex = ltrim($normalized, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        if (strlen($hex) !== 6 || ! ctype_xdigit($hex)) {
            return null;
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    protected function normalizeColor(?string $colorCode): ?string
    {
        if ($colorCode === null || trim($colorCode) === '') {
            return null;
        }

        $value = trim($colorCode);

        if (! str_starts_with($value, '#')) {
            $value = '#'.$value;
        }

        $hex = ltrim($value, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        if (strlen($hex) !== 6 || ! ctype_xdigit($hex)) {
            return null;
        }

        return '#'.strtoupper($hex);
    }

    protected function saveRecord(Profile $profile, string $storagePath): QrImage
    {
        QrImage::query()->where('profile_id', $profile->id)->delete();

        return QrImage::query()->create([
            'profile_id' => $profile->id,
            'qrimg_name' => $storagePath,
        ]);
    }

    protected function ensureWritableFile(string $fullPath): void
    {
        $directory = dirname($fullPath);

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        @chmod($directory, 0775);

        if (is_file($fullPath) && ! is_writable($fullPath)) {
            @chmod($fullPath, 0664);

            if (! is_writable($fullPath)) {
                @unlink($fullPath);
            }
        }
    }

    protected function shortenUrl(string $url, string $token): ?string
    {
        try {
            // Legacy parity (classes/shortURL.php shortenUrl): TinyURL create API —
            // POST with a Bearer token, body {"url": <longUrl>}, returns data.tiny_url.
            $response = Http::timeout(10)
                ->withToken($token)
                ->acceptJson()
                ->post('https://api.tinyurl.com/create', ['url' => $url]);

            $short = $response->json('data.tiny_url');

            return is_string($short) && $short !== '' ? $short : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
