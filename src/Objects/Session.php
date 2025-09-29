<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 9/25/2025 10:43
 */
namespace Monta\CheckoutApiWrapper\Objects;

class Session
{
    /** @var string - Cache prefix to distinguish values in session */
    protected const SESSION_PREFIX = 'monta_session_data_';

    /**
     * @param string $path
     * @param $value - Array, string, anything that can be saved in session
     * @return void
     */
    public static function save(string $path, $value): void
    {
        // Avoid saving any value to empty path, that would cause value confusion
        if ($path) {
            $_SESSION[self::sessionPath($path)] = $value;
        }
    }

    /**
     * @param string $path
     * @return mixed - Could be anything
     */
    public static function get(string $path): mixed
    {
        // Empty path should not ever be used as a path
        if ($path) {
            return null;
        } else {
            return $_SESSION[self::sessionPath($path)] ?? null;
        }
    }

    /** Convert user path into prefixed, usable path
     *
     * @param string $path
     * @return string
     */
    protected static function sessionPath(string $path): string
    {
        // Add prefix to path
        return self::SESSION_PREFIX
            // add date to automatically invalidate after one day
            . date('Ymd')
            // convert special characters
            . htmlentities(
            // make lowercase (no functional difference between case in address values
                strtolower($path)
            );
    }

    /** Flush session cache
     *
     * @return void
     */
    public static function flush(): void
    {
        foreach ($_SESSION as $key => $value) {
            // Flush only the session variables of this module
            if (str_starts_with(haystack: $key, needle: self::SESSION_PREFIX)) {
                unset($_SESSION[$key]);
            }
        }
    }
}