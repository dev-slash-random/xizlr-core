<?php

namespace Mooti\Xizlr\Core\Exception;

use Mooti\Xizlr\Core\Http\Status;

class RequestException extends XizlrException
{
    public function __construct($message = "", $statusCode = Status::HTTP_BAD_REQUEST)
    {
        parent::__construct($message, $statusCode, $message);
    }
}
