<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    /**
     * Decode a JWT token.
     *
     * @param string $token
     * @param string $secretKey
     * @return object
     * @throws \Exception
     */
    public static function decode(string $token, string $secretKey): object
    {
        try {
            return JWT::decode($token, new Key($secretKey, 'HS256'));
        } catch (\Exception $e) {
            throw new \Exception('Invalid token: ' . $e->getMessage());
        }
    }
}
