<?php

namespace App\Http\Controllers;

use App\Services\ProfileQrService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Public "Express Code Generator" on the marketing home page — parity with the
 * legacy Kohana qrhome controller (createQRcode + downloadQR). Anonymous visitors
 * can generate and download a basic QR / Data Matrix code from a URL, with no
 * account and no persistence.
 */
class ExpressCodeController extends Controller
{
    public function __construct(private readonly ProfileQrService $qr) {}

    /** Live preview — parity with legacy qrhome/createQRcode. */
    public function preview(Request $request): JsonResponse
    {
        $url = trim((string) $request->input('url', ''));
        $codeType = ((int) $request->input('code_type', 0)) === 1 ? 1 : 0;

        if (! $this->isValidExpressUrl($url)) {
            return response()->json(['ok' => false]);
        }

        try {
            $dataUri = $this->qr->expressPreviewDataUri($url, $codeType);
        } catch (\Throwable) {
            return response()->json(['ok' => false]);
        }

        return response()->json(['ok' => (bool) $dataUri, 'image' => $dataUri]);
    }

    /** Download — parity with legacy qrhome/downloadQR. */
    public function download(Request $request): StreamedResponse
    {
        $url = trim((string) $request->input('url', ''));
        $codeType = ((int) $request->input('code_type', 0)) === 1 ? 1 : 0;
        $format = strtolower((string) $request->input('download_as', 'pdf'));

        abort_unless($this->isValidExpressUrl($url), 422, 'Please enter a valid URL.');

        if (! in_array($format, ['pdf', 'png', 'jpg', 'jpeg'], true)) {
            $format = 'pdf';
        }

        return $this->qr->expressDownload($url, $codeType, $format);
    }

    /** Legacy rule: non-empty and > 3 chars after stripping scheme + "www.". */
    private function isValidExpressUrl(string $url): bool
    {
        $lower = strtolower($url);

        if ($url === '' || $lower === 'http://' || $lower === 'https://') {
            return false;
        }

        $stripped = preg_replace('#^https?://#i', '', $url);
        $stripped = preg_replace('#^www\.#i', '', (string) $stripped);

        return strlen((string) $stripped) > 3;
    }
}
