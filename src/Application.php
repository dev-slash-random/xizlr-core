<?php

namespace Mooti\Xizlr\Core;

#use \Xizlr\Core\System\Request;
#use \Xizlr\Core\System\Framework;
#use \Xizlr\Core\Exception\UnauthorizedHttpRequestException;
#use \Xizlr\Core\Exception\HttpException;
#use \Xizlr\Core\Exception\BaseException;

class Application
{
    private $users = array(
        'ken.lalobo@xizlr.net' => array(
            'username'    => 'ken.lalobo@xizlr.net',
            'password'    => 'password'
        ),
        'anonymous@nowhere.invalid' => array(
            'username'    => 'anonymous@nowhere.invalid',
            'password'    => ''
        )
    );

    private $singlePluralMappings = array(
        'token' => 'tokens',
        'user'  => 'users'
    );

    public function run()
    {
        //$serverVars  = $_SERVER;
        //$requestVars = $_REQUEST;

        /*$method   = $_SERVER['REQUEST_METHOD'];
        $urlParts = parse_url($_SERVER['REQUEST_URI']);
        $uri      = $urlParts['path'];

        $uriParts = explode('/', $uri);
        $extraHeaders = array();

        if (empty($uriParts[1]) || empty($uriParts[2]) || empty($uriParts[3])) {
            $headerMessage = 'Not Found';
            $response = array(
                'message' => 'Resource Not Found'
            );
            $code = 404;
        } else {
            $version = $uriParts[1];
            $section = ucfirst($uriParts[2]);

            $pluralSingleMappings = array_flip($this->singlePluralMappings);
            $resourceName = ucfirst($pluralSingleMappings[$uriParts[3]]);

            if (empty($uriParts[4]) == false) {
                $requestVars[lcfirst($resourceName).'Id'] = $uriParts[4];
            }

            switch(strtolower($method)) {
                case 'post':
                    $action = 'create'.$resourceName;
                    $headerMessage = 'Created';
                    $code = 201;
                    break;
                case 'get':
                    $action = 'get'.$resourceName;
                    $headerMessage = 'OK';
                    $code = 200;
                    break;
            }

            try {
                $request = new Request(Request::REQUEST_TYPE_HTTP, $_SERVER['REQUEST_METHOD'], $_SERVER, $_REQUEST);
                if ($this->isValidRequest($request) == false) {
                    throw new UnauthorizedHttpRequestException('Access denied. The request was invalid');
                }
                $framework = new Framework();
                $framework->setRequest($request);

                $securityModule = $framework->makeModule('Security');

                if (empty($action) || !$securityModule->actionAllowed($request->username, $section, $resourceName, $action)) {
                    $headerMessage = 'Unauthorized';
                    $response = array(
                        'message' => 'Access Denied'
                    );
                    $code = 401;
                } else {
                    $resource = $this->makeResource($section, $resourceName, $framework);

                    $reflectionClass = new \ReflectionClass(get_class($resource));
                    $reflectionMethod = $reflectionClass->getMethod($action);
                    $parameters = $reflectionMethod->getParameters();
                    $parameterValues = array();

                    foreach ($parameters as $parameter) {
                        $parameterValues[] = $requestVars[$parameter->name];
                    }
                    $response = call_user_func_array(array($resource, $action), $parameterValues);

                    $extraHeaders['X-HMAC-Nonce'] = $securityModule->generateNewNonce($request->token, $request->username);
                }
            } catch (HttpException $e) {
                $code = $e->getCode();
                $headerMessage = $e->getHeaderMessage();
                $response = array(
                    'code'    => $code,
                    'message' => $e->getMessage()
                );
            }
        }

        header('HTTP/1.1 '.$code.' '.$headerMessage, true, $code);
        foreach ($extraHeaders as $headerName => $headerValue) {
            header($headerName.': '.$headerValue, true);
        }
        echo json_encode(array('response' => $response));*/

        return "PONG";
    }

    public function isValidRequest(Request $request)
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        $session = json_decode($redis->get('userToken:'.$request->username.':'.$request->token), true);

        $queryString = http_build_query($request->requestVars);

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

        return true;
    }
}
