<?php

namespace App\Filament\Auth;

use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Facades\Filament;
use SensitiveParameter;

/**
 * Filament double-wraps bacon SVG QR codes when Imagick is missing:
 * pragmarx already returns data:image/svg+xml;base64,… and Filament
 * base64-encodes that string again, which breaks <img src>.
 */
class ScanLinkAppAuthentication extends AppAuthentication
{
    public function generateQrCodeDataUri(#[SensitiveParameter] string $secret): string
    {
        /** @var HasAppAuthentication $user */
        $user = Filament::auth()->user();

        $inlineQrCode = $this->google2FA->getQRCodeInline(
            $this->getBrandName(),
            $this->getHolderName($user),
            $secret,
        );

        // Already a usable data URI (pragmarx + SvgImageBackEnd).
        if (str_starts_with($inlineQrCode, 'data:image/')) {
            return $inlineQrCode;
        }

        // Raw SVG markup — wrap once for <img src>.
        if (str_contains($inlineQrCode, '<svg')) {
            return 'data:image/svg+xml;base64,'.base64_encode($inlineQrCode);
        }

        return $inlineQrCode;
    }
}
