<?php
/*
 * Router class
 *
 */
namespace Mooti\Xizlr\Core\Http;

use ICanBoogie\Inflector;

class Router
{
    protected $httpMethod;
    protected $uri;
    protected $postVars;

    protected $resourceName;
    protected $resourceMethod;
    protected $resourceVersion;
    protected $resourceArguments;

    public function __construct($httpMethod, $uri, array $postVars = array())
    {
        $this->httpMethod = $httpMethod;
        $this->uri        = $uri;
        $this->postVars   = $postVars;

        $this->processRoute();
    }

    public function processRoute()
    {
        $inflector = Inflector::get('en');

        $parts = parse_url($this->uri);

        preg_match_all("/^\/([\d]+\.[\d]+)\/([\w \.-]*)/i", $parts['path'], $matches);
        
        if (count($matches) == 3) {
            $version        = $matches[1][0];
            $resourcePlural = $matches[2][0];

            $this->resourceName = $inflector->singularize($resourcePlural);
            if ($this->httpMethod == 'get') {
                $this->resourceMethod = 'get'.$inflector->camelize($resourcePlural);
            }

            $this->resourceVersion = $version;
        }
    }

    public function getResourceName()
    {
        return $this->resourceName;
    }
    public function getResourceMethod()
    {
        return $this->resourceMethod;
    }
    public function getResourceVersion()
    {
        return $this->resourceVersion;
    }
    public function getResourceArguments()
    {
        return $this->resourceArguments;
    }
}
