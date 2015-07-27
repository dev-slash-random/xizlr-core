<?php 
/*
* Request class
*
*/
namespace Mooti\Xizlr\Core;

/*use \Xizlr\Core\Model\Account\User;

use \Xizlr\Core\Exception\InvalidRequestException;
use \Xizlr\Core\Exception\InvalidArgumentException;
use \Xizlr\Core\Exception\AuthorizationTypeException;
use \Xizlr\Core\Exception\InvalidAuthorizationException;*/

class Request
{
    const AUTHORIZATION_HMAC = 'hmac';

    const REQUEST_TYPE_HTTP = 'http';
    const REQUEST_TYPE_CLI  = 'cli';

    const REQUEST_METHOD_GET    = 'cli';
    const REQUEST_METHOD_POST   = 'post';
    const REQUEST_METHOD_DELETE = 'delete';

    public $requestType;
    public $requestMethod;
    public $rawRequestHeaders = array();
    public $username;
    public $token;
    public $date;
    public $requestVars;

    public $authenticationMessage;
    public $requestUri;
    public $nonce;

    private $allowedRequestTypes   = array('http', 'cli');
    private $allowedRequestMethods = array('get', 'post', 'delete');

    /**
     * @param string $requestType   The request type. Either http or cli
     * @param string $requestMethod The request method. i.e get, post, put
     *
     */
    public function __construct($requestType, $requestMethod, $serverVars = array(), $requestVars = array())
    {
        /*if (in_array(strtolower($requestType), $this->allowedRequestTypes, true) == false) {
            throw new InvalidArgumentException('Request type '.$requestType.' is invalid');
        }

        if (in_array(strtolower($requestMethod), $this->allowedRequestMethods, true) == false) {
            throw new InvalidArgumentException('request method '.$requestMethod.'is invalid');
        }

        $this->requestType    = strtolower($requestType);
        $this->requestMethod  = strtolower($requestMethod);
        $this->requestUri     = $serverVars['REQUEST_URI'];
        $this->requestVars    = $requestVars;

        if (empty($serverVars['HTTP_DATE'])) {
            throw new InvalidArgumentException('The request date is empty');
        }

        try {
            $this->date = new \DateTime($serverVars['HTTP_DATE']);
        } catch (\Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }

        $datePast   = new \DateTime();
        $datePast->modify('-1 minute');
        if ($datePast > $this->date) {
            throw new InvalidArgumentException('The request date is more than 1 minute in the past');
        }

        foreach ($serverVars as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $this->rawRequestHeaders[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        $authParts = explode(' ', $this->rawRequestHeaders['Authorization']);

        if (trim($authParts[0]) == self::AUTHORIZATION_HMAC) {
            $authDetails = explode(':', $authParts[1]);
        } else {
            throw new AuthorizationTypeException("Authorization type not allowed");
        }

        if (count($authDetails) != 3) {
            throw new InvalidAuthorizationException('The Authorization header is in the wrong format');
        }

        $this->username              = $authDetails[0];
        $this->token                 = $authDetails[1];
        $this->authenticationMessage = $authDetails[2];

        if (empty($this->token) == true) {
            $this->username = User::USERNAME_ANONYMOUS;
        }*/
    }
}
