<?php
/*
* application class
*
* @author Ken Lalobo
*
*/

namespace Mooti\Xizlr\Core;

use League\Container\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use League\Route\RouteCollection;

class WebApplication
{
    use Xizlr;

    public function run(array $controllers)
    {
        $container = $this->createNew(Container::class);
        $this->setContainer($container);

        $router = $this->createNew(RouteCollection::class);

        $router->addRoute('GET', '/{resource}', function (Request $request, Response $response, array $args) use ($controllers) {
            $resource = $args['resource'];
            
            if (isset($controllers[$resource])) {
                throw new ControllerNotFoundException('the controller '.$controllers[$resource].' does not exist');
            }

            $controller = $this->createNew($controllers[$resource]);

            if (!$controller instanceof AbstractController) {
                throw new InvalidControllerException('the controller '.$controllers[$resource].' is not an instance of AbstractController');
            }

            return $controller->getAll($request, $response);
        });

        $dispatcher = $router->getDispatcher();

        $request = $this->createRequest();

        $response = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

        $response->send();
    }

    public function createRequest()
    {
        return Request::createFromGlobals();
    }
}

