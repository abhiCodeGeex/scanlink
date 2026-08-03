<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a requested QR/DM download format cannot be produced on this server
 * (e.g. TIFF/EPS require ImageMagick). The message is safe to show to the user.
 */
class UnsupportedDownloadFormatException extends RuntimeException
{
}
