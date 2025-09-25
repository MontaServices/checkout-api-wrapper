<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 9/25/2025 10:43
 */
namespace Monta\CheckoutApiWrapper\Objects;

class Session
{
    /** @var string  */
    protected const string SESSION_PREFIX = 'monta_session_data_';

    /**
     * @param string $path
     * @param $value
     * @return void
     */
    public static function save(string $path, $value)
    {
        $_SESSION[self::SESSION_PREFIX . $path] = $value;
    }

    /**
     * @param string $path
     * @return mixed
     */
    public static function get(string $path)
    {
        return $_SESSION[self::SESSION_PREFIX . $path] ?? null;
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