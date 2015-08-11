<?php 
/*
* Request class
*
*/
namespace Mooti\Xizlr\Core\Http;

use \Mooti\Xizlr\Core\Exception\RequestException;
use \Mooti\Xizlr\Core\Exception\XizlrException;
use \Mooti\Xizlr\Core\Http\Status;

class Request
{
    const AUTHORIZATION_HMAC = 'hmac';

    const REQUEST_METHOD_DELETE = 'delete';
    const REQUEST_METHOD_HEAD   = 'head';
    const REQUEST_METHOD_GET    = 'get';
    const REQUEST_METHOD_PATCH  = 'patch';
    const REQUEST_METHOD_POST   = 'post';
    const REQUEST_METHOD_PUT    = 'put';

    protected $requestMethod;
    protected $headers = array();
    protected $username;
    protected $token;
    protected $date;
    protected $postVars;

    protected $authenticationMessage;
    protected $requestUri;

    protected $allowedRequestMethods = array(
        self::REQUEST_METHOD_DELETE,
        self::REQUEST_METHOD_HEAD,
        self::REQUEST_METHOD_GET,
        self::REQUEST_METHOD_PATCH,
        self::REQUEST_METHOD_POST,
        self::REQUEST_METHOD_PUT
    );

    public function setServerVariables($serverVars)
    {
        $this->requestUri     = $serverVars['REQUEST_URI'];

        if (in_array(strtolower($serverVars['REQUEST_METHOD']), $this->allowedRequestMethods, true) == false) {
            throw new RequestException('The request method "'.$requestMethod.'" is invalid', Status::HTTP_METHOD_NOT_ALLOWED);
        }

        $this->requestMethod  = $serverVars['REQUEST_METHOD'];

        if (empty($serverVars['HTTP_DATE'])) {
            throw new RequestException('The request date is empty');
        }

        try {
            $this->date = new \DateTime($serverVars['HTTP_DATE']);
        } catch (\Exception $e) {
            throw new XizlrException($e->getMessage(), Status::HTTP_BAD_REQUEST, 'The request date is in an invalid format');
        }

        $datePast = new \DateTime();
        $datePast->modify('-1 minute');
        if ($datePast > $this->date) {
            throw new RequestException('The request date is more than 1 minute in the past');
        }

        foreach ($serverVars as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $this->headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        $authParts = explode(' ', $this->headers['Authorization']);

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
            $this->username = self::USERNAME_ANONYMOUS;
        }
    }

    public function setPostVariables($postVars)
    {
        $this->postVars = $postVars;
    }

    public function isValidRequest(Session $session)
    {
        /*$redis = new \Redis();
        $redis->connect('127.0.0.1');
        $session = json_decode($redis->get('userToken:'.$request->username.':'.$request->token), true);

        $queryString = http_build_query($request->postVars);

        //confirm that the auth message we have is valid given the request parameters
        if ($request->authenticationMessage != \Xizlr\Core\Util::generateAuthenticatinMessage(
            $request->requestMethod,
            $request->requestUri,
            $queryString,
            $request->rawRequestHeaders['Date'],
            $session['nonce'],
            $session['secret'],
            hash('sha256', $this->users[$request->username]['password'])
        )) {
            return false;
        }

        return true;*/
    }
}
