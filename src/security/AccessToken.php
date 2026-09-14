<?php
/**
 * Token to be used when certain URLs needs to be protected by an additional token
 *
 * @package controllerframework\security
 * @version 1.0
 * @copyright (c) 2025, Dirk Van Meirvenne
 * @author Dirk Van Meirvenne <van.meirvenne.dirk at gmail.com>
 */
namespace controllerframework\security;

/**
 * Token to be used when certain URLs needs to be protected by an additional token
 *
 * @author Dirk Van Meirvenne <dirk.van.meirvenne at samosconsulting.be>
 */
class AccessToken
{
    /**
     * Generate the token
     * 
     * @param string $purpose description of the purpose of the token
     * @param string $identifier 
     * @return string The token
     */
    public static function generate(
        string $purpose,
        string $identifier
    ): string {
        return hash_hmac(
            'sha256',
            $purpose . ':' . $identifier,
            _SALTRAND
        );
    }

    /**
     * Generate the token
     * 
     * @param string $purpose description of the purpose of the token
     * @param string $identifier 
     * @param string $token the given token
     * @return bool
     */
    public static function validate(
        string $purpose,
        string $identifier,
        string $token
    ): bool {
        $expectedToken = self::generate(
            $purpose,
            $identifier
        );

        return hash_equals($expectedToken, $token);
    }
}