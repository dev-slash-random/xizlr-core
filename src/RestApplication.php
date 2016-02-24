<?php
/**
 *
 * The main Application class
 *
 * @package      Xizlr
 * @subpackage   Core     
 * @author       Ken Lalobo <ken@mooti.io>
 */

namespace Mooti\Xizlr\Core;

use Mooti\Xizlr\Core\Exception\ControllerNotFoundException;
use Mooti\Xizlr\Core\Exception\InvalidControllerException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Interop\Container\ContainerInterface;
use League\Route\RouteCollection;
use ICanBoogie\Inflector;

class RestApplication
{
    use Xizlr;

    /**
     * @var array
     */
    private $controllers;

    /**
     * @param array  $identifier  Key/value list of controllers.
     */
    public function __construct(array $controllers)
    {
        $this->controllers = $controllers;
    }

    /**
     * Create a Symfony Request object
     *
     * @return Symfony\Component\HttpFoundation\Request 
     */
    public function createRequest()
    {
        return Request::createFromGlobals();
    }

    /**
     * Create a League RoutCollection object and add our routes to it
     *
     * @return League\Route\RouteCollection
     */
    public function createRouteCollection()
    {
        $routeCollection  = $this->createNew(RouteCollection::class);

        $routeCollection->addRoute('GET', '/{resource}', [$this, 'callGetResources']);
        $routeCollection->addRoute('POST', '/{resource}', [$this, 'callCreateNewResource']);
        $routeCollection->addRoute('PUT', '/{resource}', [$this, 'callCreateNewResource']);
        $routeCollection->addRoute('HEAD', '/{resource}', [$this, 'callMethodNotAllowed']);
        $routeCollection->addRoute('DEL', '/{resource}', [$this, 'callMethodNotAllowed']);

        $routeCollection->addRoute('GET', '/{resource}/{id}', [$this, 'callGetResource']);
        $routeCollection->addRoute('POST', '/{resource}/{id}', [$this, 'callEditResource']);
        $routeCollection->addRoute('PUT', '/{resource}/{id}', [$this, 'callEditResource']);
        $routeCollection->addRoute('HEAD', '/{resource}/{id}', [$this, 'callResourceExists']);
        $routeCollection->addRoute('DEL', '/{resource}/{id}', [$this, 'calldDeleteResource']);

        $routeCollection->addRoute('GET', '/{resource}/{id}/{child}', [$this, 'callGetChildResources']);
        $routeCollection->addRoute('POST', '/{resource}/{id}/{child}', [$this, 'callCreateNewChildResource']);
        $routeCollection->addRoute('PUT', '/{resource}/{id}/{child}', [$this, 'callCreateNewChildResource']);
        $routeCollection->addRoute('HEAD', '/{resource}/{id}/{child}', [$this, 'callMethodNotAllowed']);
        $routeCollection->addRoute('DEL', '/{resource}/{id}/{child}', [$this, 'callMethodNotAllowed']);

        return $routeCollection;
    }

    /**
     * Add services that we need to a given container
     *
     * @param \ArrayAccess $container An object that implements ArrayAccess
     *
     * @return \ArrayAccess
     */
    public function registerServices(\ArrayAccess $container)
    {
        $services = Services::getDefinitions();
        foreach ($services as $id => $service) {
            $container[$id] = $service;
        }
        return $container;
    }

    /**
     * Run the application
     *
     * @param Interop\Container\ContainerInterface $container An object that implements ContainerInterface
     */
    public function run(ContainerInterface $container = null)
    {
        $compositeContainer = $this->createNew(CompositeContainer::class);
        if (isset($container) == true) {
            $compositeContainer->addContainer($container);
        }

        $xizlrContainer = $this->registerServices($this->createNew(Container::class));
        $compositeContainer->addContainer($xizlrContainer);

        $this->setContainer($compositeContainer);

        $routeCollection = $this->createRouteCollection();

        $dispatcher = $routeCollection->getDispatcher();

        $request = $this->createRequest();

        $response = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

        $response->send();
    }

    /**
     * Add services that we need to a given container
     *
     * @param string $resource The name of the resource being access (e.g 'users' if we are accessing http://account.mooti.io/users)
     *
     * @return \ArrayAccess
     */
    public function getController($resource)
    {
   
        if (isset($this->controllers[$resource]) == false) {
            throw new ControllerNotFoundException('the controller for "'.$resource.'" does not exist');
        }

        $controller = $this->createNew($this->controllers[$resource]);

        if (!$controller instanceof BaseController) {
            throw new InvalidControllerException('the controller '.$this->controllers[$resource].' is not an instance of AbstractController');
        }

        return $controller;
    }

    public function callMethod($resourceName, $methodName, $arguments = [])
    {
        $controller = $this->getController($resourceName);

        if (method_exists($controller, $methodName) == false) {
            throw new InvalidMethodException('The method ('.$methodName.') does not exist for the class '.get_class($controller));
        }
        return call_user_func_array([$controller, $methodName], $arguments);
    }

    public function callGetResources(Request $request, Response $response, array $args)
    {
        $resourceName = $args['resource'];
        $inflector    = $this->getService(Services::INFLECTOR);

        $methodName = 'get'.$inflector->Camelize($resourceName);

        return $this->callMethod($resourceName, $methodName, [$request, $response]);
    }

    public function callCreateNewResource(Request $request, Response $response, array $args)
    {
        $resourceName = $args['resource'];
        $inflector    = $this->getService(Services::INFLECTOR);
        $methodName = 'create'.$inflector->Camelize($this->singlelize($resourceName));

        return $this->callMethod($resourceName, $methodName, [$request, $response]);
    }

    public function callGetResource(Request $request, Response $response, array $args)
    {
        $resourceName = $args['resource'];
        $resourceId   = $args['id'];
        $inflector    = $this->getService(Services::INFLECTOR);
        $methodName = 'get'.$inflector->Camelize($this->singlelize($resourceName));

        return $this->callMethod($resourceName, $methodName, [resourceId, $request, $response]);
    }

    public function callEditResource(Request $request, Response $response, array $args)
    {
        $resourceName = $args['resource'];
        $resourceId   = $args['id'];
        $inflector    = $this->getService(Services::INFLECTOR);
        $methodName = 'edit'.$inflector->Camelize($this->singlelize($resourceName));

        return $this->callMethod($resourceName, $methodName, [resourceId, $request, $response]);
    }

    public function callResourceExists(Request $request, Response $response, array $args)
    {
        $resourceName = $args['resource'];
        $resourceId   = $args['id'];
        $inflector    = $this->getService(Services::INFLECTOR);
        $methodName = $inflector->Camelize($this->singlelize($resourceName), Inflector::DOWNCASE_FIRST_LETTER).'Exists';

        return $this->callMethod($resourceName, $methodName, [resourceId, $request, $response]);
    }

    public function calldDeleteResource(Request $request, Response $response, array $args)
    {
        $resourceName = $args['resource'];
        $resourceId   = $args['id'];
        $inflector    = $this->getService(Services::INFLECTOR);
        $methodName = 'delete'.$inflector->Camelize($this->singlelize($resourceName));

        return $this->callMethod($resourceName, $methodName, [resourceId, $request, $response]);
    }

    public function callGetChildResources(Request $request, Response $response, array $args)
    {
        $resourceName      = $args['resource'];
        $resourceId        = $args['id'];
        $childResourceName = $args['child'];
        $inflector         = $this->getService(Services::INFLECTOR);

        $methodName = 'get'.$inflector->Camelize($resourceName).$inflector->Camelize($this->singlelize($childResourceName));

        return $this->callMethod($resourceName, $methodName, [$request, $response]);
    }

    public function callCreateNewChildResource(Request $request, Response $response, array $args)
    {
        $resourceName      = $args['resource'];
        $resourceId        = $args['id'];
        $childResourceName = $args['child'];
        $inflector         = $this->getService(Services::INFLECTOR);

        $methodName = 'get'.$inflector->Camelize($resourceName).$inflector->Camelize($this->singlelize($childResourceName));

        return $this->callMethod($resourceName, $methodName, [$request, $response]);
    }

    public function callMethodNotAllowed(Request $request, Response $response, array $args)
    {
        throw new MethodNotAllowedException('The method ('.$methodName.') does not exist for the class '.get_class($controller));
    }
}
