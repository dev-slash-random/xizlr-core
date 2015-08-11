<?php

namespace Mooti\Xizlr\Core;

use \Mooti\Xizlr\Core\Config;
use \Mooti\Xizlr\Core\Interfaces\Framework as FrameworkInterface;

class Application
{
    use \Mooti\Xizlr\Testable\Testable;

    /**
     * @var \Mooti\Xizlr\Core\Interfaces\Framework
     */
    private static $framework;
    
    public function newFramework(Config $config)
    {
        $framework = new \Mooti\Xizlr\Core\Framework($config);
        self::setFramework($framework);
        return $framework;
    }

    public function run(Config $config, $serverVars = array(), $postVars = array())
    {
        $framework = $this->newFramework($config);

        try {
            $request = $framework->getRequest();

            $request->setServerVariables($serverVars);
            $request->setPostVariables($postVars);

            if ($request->isValidateRequest($framework->getSession())) {
                throw new UnauthorizedHttpRequestException('Access denied. The request was invalid');
            }

            $router = $this->instantiate('\Mooti\Xizlr\Core\Http\Router', $request->getRequestUri(), $request->getPostVariables());

            $resourceName      = $router->getResourceName();
            $resourceMethod    = $router->getResourceMethod();
            $resourceArguments = $router->getResourceArguments();

            $resource = $this->framework->getResource($resourceName);
            
            if ($request->getUsername() == Request::USERNAME_ANONYMOUS && !$resource->anonymousAccessAllowed()) {
                throw new UnauthorizedHttpRequestException('Access denied. You are not allowed to call this method');
            }

            $resource->$resourceMethod( ...$parameterValues);

            $reponse = $framework->getResponse();

            $reponse->setRequest($request);
            $reponse->setResource($resource);
            $reponse->setSession($framework->getSession());
            $reponse->generateNewNonce();
           
        } catch (HttpException $e) {
            $reponse = $this->instantiate('\Mooti\Xizlr\Core\Http\Response');
            $reponse->setResponseData(array('error'));

        } catch (\Exception $e) {
            $reponse = $this->instantiate('\Mooti\Xizlr\Core\Http\Response');
            $reponse->setResponseData(array('error'));

        }
    
        return $response;
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
