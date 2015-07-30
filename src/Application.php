<?php

namespace Mooti\Xizlr\Core;

use \Mooti\Xizlr\Core\Config;

#use \Xizlr\Core\System\Request;
#use \Xizlr\Core\System\Framework;
#use \Xizlr\Core\Exception\UnauthorizedHttpRequestException;
#use \Xizlr\Core\Exception\HttpException;
#use \Xizlr\Core\Exception\BaseException;

class Application
{
    use \Mooti\Xizlr\Testable\Testable;

    /**
     * @var \Mooti\Xizlr\Core\Interfaces\Framework
     */
    private static $framework;

    /*private $users = array(
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
    );*/
    
    public function newFramework(Config $config, $serverVars = array(), $requestVars = array())
    {
        $framework = new \Mooti\Xizlr\Core\Framework($config, $serverVars, $requestVars);
        self::setFramework($framework);
        return $framework;
    }

    public function run(Config $config, $serverVars = array(), $requestVars = array())
    {
        $framework = $this->newFramework($config, $serverVars, $requestVars);

        try {

            //$resource = $this->makeResource($resourceName, $framework);

            /*
            if ($this->isValidRequest($request) == false) {
                throw new UnauthorizedHttpRequestException('Access denied. The request was invalid');
            }
            
            

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
            */
        } catch (HttpException $e) {
            /*$logger->error('Start Request');
            $code = $e->getCode();
            $headerMessage = $e->getHeaderMessage();
            $response = array(
                'code'    => $code,
                'message' => $e->getMessage()
            );*/

            throw $e;
        } catch (\Exception $e) {
            /*$logger->error('Error!');
            error_log('Error!');
            $code = $e->getCode();
            $headerMessage = $e->getHeaderMessage();
            $response = array(
                'code'    => $code,
                'message' => $e->getMessage()
            );*/
            throw $e;
        }

    
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

            

        header('HTTP/1.1 '.$code.' '.$headerMessage, true, $code);
        foreach ($extraHeaders as $headerName => $headerValue) {
            header($headerName.': '.$headerValue, true);
        }
        echo json_encode(array('response' => $response));*/

        //return $response;
    }

    public function makeResource($resourceName, Framework $framework)
    {
        /*$applicationConfig = $framework->getConfig('application');

        $className = $applicationConfig['namespace'].'\\Resource\\'.$resourceName;
        $resource = new $className();

        if ($resource instanceof \Mooti\Xizlr\Core\Resource) {
            $resource->setFramework($framework);
            return $resource;
        } else {
            throw InvalidResourceException();
        }*/
    }

    public function isValidRequest(Request $request)
    {
        /*$redis = new \Redis();
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

        return true;*/
    }

    /**
     * @param \Mooti\Xizlr\Core\Interfaces\Framework $framework
     */
    public static function setFramework(FrameworkInterface $framework)
    {
        self::$framework = $framework;
    }

    /**
     * @return \Mooti\Xizlr\Core\Interfaces\Framework
     */
    public static function getFramework()
    {
        return self::$framework;
    }
}
