<?php

declare(strict_types=1);

namespace Scrawler;

/**
 * Bootstrap helper for Scrawler
 *
 * Handles PHP 8.4 deprecation warnings from vendor libraries (DiDom, Guzzle)
 * until they are updated with explicit nullable type declarations.
 */
final class Bootstrap
{
    private static bool $initialized = false;

    /**
     * Initialize error handling for PHP 8.4 compatibility
     *
     * Suppresses only E_DEPRECATED warnings from vendor libraries
     * while keeping all other error reporting intact.
     */
    public static function init(): void
    {
        if (self::$initialized) {
            return;
        }

        // Only suppress deprecation warnings in PHP 8.4+
        if (PHP_VERSION_ID >= 80400) {
            set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
                // Only suppress deprecation warnings from vendor libraries
                if ($errno === E_DEPRECATED || $errno === E_USER_DEPRECATED) {
                    if (str_contains($errfile, '/vendor/') || str_contains($errfile, '\\vendor\\')) {
                        return true; // Suppress
                    }
                }

                // Let PHP handle all other errors normally
                return false;
            });
        }

        self::$initialized = true;
    }
}
