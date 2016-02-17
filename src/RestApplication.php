<?php
/**
 * The main Application class
 *
 * @author Ken Lalobo
 *
 */

namespace Mooti\Xizlr\Core;

use Interop\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use League\Route\RouteCollection;
use ICanBoogie\Inflector;

class RestApplication
{
    use Xizlr;

    private $controllers;

    public function createRequest()
    {
        return Request::createFromGlobals();
    }

    public function setControllers(array $controllers)
    {
        $this->controllers = $controllers;
    }

    public function registerServices(\ArrayAccess $container){
        $services = Services::getDefinitions();
        foreach ($services as $id => $service) {
            $container[$id] = $service;
        }
        return $container;
    }

    public function run(array $controllers, ContainerInterface $container = null)
    {
        $this->controllers = $controllers;

        if (isset($container) == false) {
            $container = $this->registerServices($this->createNew(Container::class));            
        }
        
        $this->setContainer($container);

        $router = $this->createNew(RouteCollection::class);

        $router->addRoute('GET', '/{resource}', [$this, 'callGetResources']);
        $router->addRoute('POST', '/{resource}', [$this, 'callCreateNewResource']);
        $router->addRoute('PUT',  '/{resource}', [$this, 'callCreateNewResource']);
        $router->addRoute('HEAD', '/{resource}', [$this, 'callMethodNotAllowed']);
        $router->addRoute('DEL', '/{resource}', [$this, 'callMethodNotAllowed']);

        $router->addRoute('GET', '/{resource}/{id}', [$this, 'callGetResource']);
        $router->addRoute('POST', '/{resource}/{id}', [$this, 'callEditResource']);
        $router->addRoute('PUT',  '/{resource}/{id}', [$this, 'callEditResource']);
        $router->addRoute('HEAD', '/{resource}/{id}', [$this, 'callResourceExists']);
        $router->addRoute('DEL', '/{resource}/{id}', [$this, 'calldDeleteResource']);

        $router->addRoute('GET', '/{resource}/{id}/{child}', [$this, 'callGetChildResources']);
        $router->addRoute('POST', '/{resource}/{id}/{child}', [$this, 'callCreateNewChildResource']);
        $router->addRoute('PUT',  '/{resource}/{id}/{child}', [$this, 'callCreateNewChildResource']);
        $router->addRoute('HEAD', '/{resource}/{id}/{child}', [$this, 'callMethodNotAllowed']);
        $router->addRoute('DEL', '/{resource}/{id}/{child}', [$this, 'callMethodNotAllowed']);

        $dispatcher = $router->getDispatcher();

        $request = $this->createRequest();

        $response = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

        $response->send();
    }

    public function getController($resource)
    {        
        if (isset($this->controllers[$resource]) == false) {
            throw new ControllerNotFoundException('the controller '.$this->controllers[$resource].' does not exist');
        }

        $controller = $this->createNew($this->controllers[$resource]);

        if (!$controller instanceof AbstractController) {
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

