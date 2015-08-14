<?php
/*
* Main xizlr framework class
*
* @author Ken Lalobo
*
*/

namespace Mooti\Xizlr\Core;

use \Mooti\Xizlr\Core\Interfaces\Config;
use \Mooti\Xizlr\Core\Interfaces\Framework as FrameworkInterface;
use Pimple\Container;
use Http\Request;

class Framework implements FrameworkInterface
{
    /**
     * @var \Pimple The dependancy injection container
     */
    private $container;

    /**
     * @var \Mooti\Xizlr\Core\Interfaces\Config The config object
     */
    public $config;

    /**
     * @param \Mooti\Xizlr\Core\Interfaces\Config $config The config object
     */
    public function __construct(Config $config)
    {
        
        $this->config = $config;

        $congfigModule = $config->get('module');

        $this->container = new Container(array());

        $resourcesConfig = $config->get('resources');
        $frameworkConfig = $config->get('framework');

        foreach ($resourcesConfig as $resourceName => $resourceClass) {
            $this->container['xizlr.resource.'.$resourceName] = function ($config) use ($resourceClass) {
                $resource = new $resourceClass();
                return new $resource;
            };
        }

        $requestClass = $frameworkConfig['request'];
        $this->container['xizlr.request'] = function ($config) use ($requestClass) {
            $resource = new $requestClass();
            return new $resource;
        };

        $loggerClass = $frameworkConfig['logger'];
        $this->container['xizlr.logger'] = function ($config) use ($loggerClass) {
            $resource = new $loggerClass();
            return new $resource;
        };

        /*$this->container['xizlr.service.logger'] = function ($config) {
            $configServices = $config->get('services');
            $logger = new $configServices['logger']['class']($config);//'\Mooti\Xizlr\Core\Logger');
            $logger->setModuleName($congfigModule['name']);
            return new $logger;
        };*/

        //$request       = $this->instantiate($configServices['request']['class'], $config, $serverVars, $postVars);//'\Mooti\Xizlr\Core\Request'
        //$response      = $this->instantiate($configServices['response']['class'], $config); //, '\Mooti\Xizlr\Core\Response');
        //$cache         = $this->instantiate($configServices['cache']['class'], $config); //, '\Mooti\Xizlr\Core\Response');
        //$session       = $this->instantiate($configServices['session']['class'], $config); //, '\Mooti\Xizlr\Core\Response');
        //$rdbms         = $this->instantiate($configServices['rdbms']['class'], $config); //, '\Mooti\Xizlr\Core\Response');
        //$documentStore = $this->instantiate($configServices['documentStore']['class'], $config); //, '\Mooti\Xizlr\Core\Response');
        //$search        = $this->instantiate($configServices['search']['class'], $config); //, '\Mooti\Xizlr\Core\Response');
        //$fileService   = $this->instantiate($configServices['fileService']['class'], $config); //, '\Mooti\Xizlr\Core\Response');
    }

    /**
     * @param string $configName The name of the config
     */
    public function getConfig($configName)
    {
        return $this->config->getConfig($configName);
    }

    /**
     * @param string $configName  The name of the config
     * @param array  $configValue  The value of the config
     */
    public function setConfig($configName, array $configValue)
    {
        $this->config->setConfig($configName, $configValue);
    }

    /**
     * @param string $resourceName The name of the resource
     */
    public function getResource($resourceName)
    {
        return $this->container['xizlr.resource.'.$resourceName];
    }

    /**
     * @return \Mooti\Xizlr\Core\Interfaces\Request
     */
    public function getRequest()
    {
        return $this->container['xizlr.request'];
    }

    /**
     * @return \Mooti\Xizlr\Core\Interfaces\Logger
     */
    public function getLogger()
    {
        return $this->container['xizlr.logger'];
    }
}
