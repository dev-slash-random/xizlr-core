<?php

namespace Mooti\Xizlr\Core\Exception;

use \Mooti\Xizlr\Core\Http\Status;

class HttpCompatibleException extends \Exception
{
    protected $friendlyMessage;
    protected $statusCode;

    private $allowSatausCodes = array(
        Status::HTTP_BAD_REQUEST,
        Status::HTTP_UNAUTHORIZED ,
        Status::HTTP_PAYMENT_REQUIRED,
        Status::HTTP_FORBIDDEN,
        Status::HTTP_NOT_FOUND,
        Status::HTTP_METHOD_NOT_ALLOWED,
        Status::HTTP_NOT_ACCEPTABLE,
        Status::HTTP_PROXY_AUTHENTICATION_REQUIRED,
        Status::HTTP_REQUEST_TIMEOUT,
        Status::HTTP_CONFLICT,
        Status::HTTP_GONE,
        Status::HTTP_LENGTH_REQUIRED,
        Status::HTTP_PRECONDITION_FAILED,
        Status::HTTP_REQUEST_ENTITY_TOO_LARGE,
        Status::HTTP_REQUEST_URI_TOO_LONG,
        Status::HTTP_UNSUPPORTED_MEDIA_TYPE,
        Status::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE,
        Status::HTTP_EXPECTATION_FAILED,
        Status::HTTP_INTERNAL_SERVER_ERROR,
        Status::HTTP_NOT_IMPLEMENTED,
        Status::HTTP_BAD_GATEWAY,
        Status::HTTP_SERVICE_UNAVAILABLE,
        Status::HTTP_GATEWAY_TIMEOUT,
        Status::HTTP_VERSION_NOT_SUPPORTED
    );

    public function __construct($message = "", $code = 0, Exception $previous = null, $statusCode = null, $friendlyMessage = null)
    {
        if (!empty($statusCode) && !in_array($statusCode, $this->allowSatausCodes, true) == false) {
            $statusCode = null;
        }

        $this->statusCode      = (empty($statusCode) == false ? $statusCode : Status::HTTP_INTERNAL_SERVER_ERROR);
        $this->friendlyMessage = (empty($friendlyMessage) == false ? $friendlyMessage : 'The server has encountered an error');
        parent::__construct($message, $code, $previous);
    }

    public function getFriendlyMessage()
    {
        return $this->friendlyMessage;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
