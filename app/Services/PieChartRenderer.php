<?php

namespace App\Services;

/**
 * Dependency-free (GD) pie-chart image renderer, used to embed real pie images in
 * server-generated PDFs/exports (legacy rendered pie images too). No external services.
 */
class PieChartRenderer
{
    /** @var list<string> */
    private array $palette = [
        '#3B6D11', '#639922', '#97C459', '#185FA5', '#378ADD',
        '#BA7517', '#EF9F27', '#A32D2D', '#E24B4A', '#534AB7',
    ];

    /**
     * @param  array<int, array{label?: string, value?: int|float, color?: string}>  $slices
     * @return string PNG bytes ('' if GD is unavailable)
     */
    public function renderPng(array $slices, int $diameter = 200): string
    {
        if (! function_exists('imagecreatetruecolor')) {
            return '';
        }

        $slices = array_values($slices);
        $total = 0.0;
        foreach ($slices as $s) {
            $total += (float) ($s['value'] ?? 0);
        }

        $pad = 12;
        $legendWidth = 250;
        $legendHeight = max(1, count($slices)) * 22;
        $width = $diameter + $pad * 3 + $legendWidth;
        $height = (int) max($diameter + $pad * 2, $legendHeight + $pad * 2);

        $img = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $white);

        $colors = [];
        foreach ($slices as $i => $s) {
            $colors[$i] = $this->allocHex($img, (string) ($s['color'] ?? $this->palette[$i % count($this->palette)]));
        }

        $cx = $pad + (int) ($diameter / 2);
        $cy = (int) ($height / 2);

        if ($total <= 0) {
            $grey = imagecolorallocate($img, 224, 224, 224);
            imagefilledellipse($img, $cx, $cy, $diameter, $diameter, $grey);
        } else {
            $start = 0.0;
            foreach ($slices as $i => $s) {
                $val = (float) ($s['value'] ?? 0);
                if ($val <= 0) {
                    continue;
                }
                $end = $start + 360 * ($val / $total);
                imagefilledarc($img, $cx, $cy, $diameter, $diameter, (int) round($start), (int) round($end), $colors[$i], IMG_ARC_PIE);
                $start = $end;
            }
        }

        $black = imagecolorallocate($img, 40, 40, 40);
        $lx = $pad * 2 + $diameter;
        $ly = $pad;

        foreach ($slices as $i => $s) {
            imagefilledrectangle($img, $lx, $ly, $lx + 14, $ly + 14, $colors[$i]);
            imagerectangle($img, $lx, $ly, $lx + 14, $ly + 14, $black);

            $val = (int) ($s['value'] ?? 0);
            $pct = $total > 0 ? (string) round($val * 100 / $total).'%' : '0%';
            $label = $this->truncate((string) ($s['label'] ?? ''), 28).' ('.$val.', '.$pct.')';

            imagestring($img, 2, $lx + 20, $ly + 1, $label, $black);
            $ly += 22;
        }

        ob_start();
        imagepng($img);
        $png = (string) ob_get_clean();
        imagedestroy($img);

        return $png;
    }

    /**
     * @param  array<int, array{label?: string, value?: int|float, color?: string}>  $slices
     */
    public function renderDataUri(array $slices, int $diameter = 200): string
    {
        $png = $this->renderPng($slices, $diameter);

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
        // imagestring only draws ASCII — strip anything else and cap the length.
        $value = trim(preg_replace('/[^\x20-\x7E]/', '', $value) ?? '');

        return mb_strlen($value) > $length ? mb_substr($value, 0, $length - 3).'...' : $value;
    }
}
