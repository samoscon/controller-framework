<?php
/**
 * Csrf.php
 *
 * @package controllerframework\security
 * @version 1.0
 * @copyright (c) 2026, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne
 */

namespace controllerframework\security;

/**
 * Generic CSRF mechanism
 *
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
class Csrf
{
    private const SESSION_KEY = 'controllerframework_csrf_token';
    public const TOKEN_PARAMETER = 'csrf_token';

    /**
     * Return the current CSRF token.
     *
     * A token is generated lazily and stored in the session.
     *
     * @return string
     */
    public static function getToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new \RuntimeException(
                'A PHP session must be active before using CSRF protection.'
            );
        }

        if (
            !isset($_SESSION[self::SESSION_KEY]) ||
            !is_string($_SESSION[self::SESSION_KEY]) ||
            $_SESSION[self::SESSION_KEY] === ''
        ) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Validate a CSRF token.
     *
     * @param string|null $token
     * @return bool
     */
    public static function validate(?string $token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return false;
        }

        if (
            $token === null ||
            $token === '' ||
            !isset($_SESSION[self::SESSION_KEY]) ||
            !is_string($_SESSION[self::SESSION_KEY])
        ) {
            return false;
        }

        return hash_equals($_SESSION[self::SESSION_KEY], $token);
    }

    /**
     * Generate a new CSRF token.
     *
     * This can be used after a security-sensitive operation.
     *
     * @return string
     */
    public static function regenerateToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new \RuntimeException(
                'A PHP session must be active before using CSRF protection.'
            );
        }

        $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));

        return $_SESSION[self::SESSION_KEY];
    }
}