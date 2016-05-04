<?php

namespace Mooti\Framework;

class Security
{
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
