<?php

namespace App\Services;

/**
 * Dependency-free (GD) horizontal bar-chart renderer, used to embed real chart images
 * in server-generated PDFs (scanalytics country/platform/browser breakdowns).
 */
class BarChartRenderer
{
    /**
     * @param  array<int, array{label?: string, value?: int|float}>  $bars
     * @return string PNG bytes ('' if GD is unavailable or no bars)
     */
    public function renderPng(array $bars, int $width = 540, string $barColor = '#639922'): string
    {
        if (! function_exists('imagecreatetruecolor')) {
            return '';
        }

        $bars = array_values(array_filter($bars, fn ($b): bool => (float) ($b['value'] ?? 0) > 0));

        if ($bars === []) {
            $bars = [['label' => 'No data', 'value' => 0]];
        }

        $max = 1;
        foreach ($bars as $b) {
            $max = max($max, (int) ($b['value'] ?? 0));
        }

        $rowH = 26;
        $pad = 12;
        $labelW = 150;
        $valW = 44;
        $barAreaW = max(40, $width - $labelW - $valW - $pad * 2);
        $height = count($bars) * $rowH + $pad * 2;

        $img = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $white);

        $fill = $this->allocHex($img, $barColor);
        $border = imagecolorallocate($img, 120, 120, 120);
        $text = imagecolorallocate($img, 40, 40, 40);
        $track = imagecolorallocate($img, 238, 238, 238);

        $y = $pad;
        foreach ($bars as $b) {
            $label = $this->truncate((string) ($b['label'] ?? ''), 22);
            $val = (int) ($b['value'] ?? 0);
            $barW = (int) round($barAreaW * ($val / $max));

            imagestring($img, 2, $pad, $y + 5, $label, $text);

            $bx = $pad + $labelW;
            imagefilledrectangle($img, $bx, $y + 3, $bx + $barAreaW, $y + $rowH - 6, $track);
            if ($barW > 0) {
                imagefilledrectangle($img, $bx, $y + 3, $bx + $barW, $y + $rowH - 6, $fill);
                imagerectangle($img, $bx, $y + 3, $bx + $barW, $y + $rowH - 6, $border);
            }

            imagestring($img, 2, $bx + $barAreaW + 6, $y + 5, (string) $val, $text);

            $y += $rowH;
        }

        ob_start();
        imagepng($img);
        $png = (string) ob_get_clean();
        imagedestroy($img);

        return $png;
    }

    /**
     * @param  array<int, array{label?: string, value?: int|float}>  $bars
     */
    public function renderDataUri(array $bars, int $width = 540, string $barColor = '#639922'): string
    {
        $png = $this->renderPng($bars, $width, $barColor);

        return $png === '' ? '' : 'data:image/png;base64,'.base64_encode($png);
    }

    private function allocHex(\GdImage $img, string $hex): int
    {
        $hex = ltrim(trim($hex), '#');

        if (! preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            $hex = '000000';
        }

        return imagecolorallocate(
            $img,
            (int) hexdec(substr($hex, 0, 2)),
            (int) hexdec(substr($hex, 2, 2)),
            (int) hexdec(substr($hex, 4, 2)),
        );
    }

    private function truncate(string $value, int $length): string
    {
        $value = trim(preg_replace('/[^\x20-\x7E]/', '', $value) ?? '');

        return mb_strlen($value) > $length ? mb_substr($value, 0, $length - 3).'...' : $value;
    }
}
