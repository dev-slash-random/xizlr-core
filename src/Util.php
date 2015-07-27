<?php

namespace Mooti\Xizlr\Core;

class Util
{
    /**
     * Generate a valid V4 UUID
     * @return string
     */
    public static function uuidV4()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * @param string $requestMethod The method. i.e get, post, put
     * @param string $requestUri    The uri requested
     * @param string $queryString   The request variables sent converted to query string format
     * @param string $date          The date in RFC2616 format
     * @param string $nonce         The nonce
     * @param string $secret        The secret
     * @param $passwordHash         The password hash
     *
     * @return string An hmac hash using sha256
     */
    public static function generateAuthenticatinMessage($requestMethod, $requestUri, $queryString, $date, $nonce, $secret, $passwordHash)
    {
        /**
         * To do: put this is security module
        */
        return hash_hmac(
            'sha256',
            strtolower($requestMethod).$requestUri.$queryString.$date.$nonce,
            hash('sha256', $secret.$passwordHash)
        );
    }
}
