<?php

namespace Mooti\Xizlr\Core\Exception;

class XizlrException extends HttpCompatibleException
{
    public function __construct($message = "", $statusCode = null, $friendlyMessage = null)
    {
        parent::__construct($message, $statusCode, null, $statusCode, $friendlyMessage);
    }
}
