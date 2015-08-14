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

        $request  = $framework->getRequest();
        $logger   = $framework->getLogger();
        $username = $request->getUsername();

        $moduleConfig = $config->get('module');

        $logger->setModuleName($moduleConfig['name']);
        $logger->setRequestId($request->getRequestId());

        $logger->notice('Start Request');

        try {
            $request->setServerVariables($serverVars);
            $request->setPostVariables($postVars);

            if ($request->isValidateRequest($framework->getSession())) {
                throw new UnauthorizedHttpRequestException('Access denied. The request was invalid');
            }

            $router = $this->instantiate('\Mooti\Xizlr\Core\Http\Router', $request->getRequestUri(), $request->getPostVariables());

            $resourceName      = $router->getResourceName();
            $resourceMethod    = $router->getResourceMethod();
            $resourceVersion   = $router->getResourceVersion();
            $resourceArguments = $router->getResourceArguments();

            if ($resourceVersion != $moduleConfig['version']) {
                throw new UnauthorizedHttpRequestException('Access denied. There is a version mismatch requested "'.$resourceVersion.'" but got "'.$moduleConfig['version'].'"');
            }

            $logger->notice('Run '.$resourceName.'.'.$resourceMethod . ' as "'.$username.'" with arguments: '.json_encode($resourceArguments));

            $resource = $this->framework->getResource($resourceName);
            
            if ($username == Request::USERNAME_ANONYMOUS && !$resource->anonymousAccessAllowed()) {
                throw new UnauthorizedHttpRequestException('Access denied. You are not allowed to call this method');
            }

            $resource->$resourceMethod( ...$parameterValues);

            $reponse = $framework->getResponse();

            $reponse->setRequest($request);
            $reponse->setResource($resource);
            $reponse->setSession($framework->getSession());
            $reponse->generateNewNonce();

            $logger->notice('Request Complete');
        } catch (HttpException $e) {
            $reponse = $this->instantiate('\Mooti\Xizlr\Core\Http\Response');
            $reponse->setResponseData(array('error'));

            $logger->error('Request Failed');
        } catch (\Exception $e) {
            throw $e;
            //$reponse = $this->instantiate('\Mooti\Xizlr\Core\Http\Response');
            //$reponse->setResponseData(array('error'));

            //$logger->critical('Request Failed');

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
