<?php
namespace Mooti\Test\Xizlr\Core;

require dirname(__FILE__).'/../vendor/autoload.php';

use Mooti\Xizlr\Core\Exception\MethodNotAllowedException;
use Mooti\Test\Xizlr\Core\Fixture\TestClassWithMethod;
use Mooti\Xizlr\Core\CompositeContainer;
use Mooti\Xizlr\Core\ModuleInterface;
use Mooti\Xizlr\Core\RestApplication;
use Mooti\Xizlr\Core\ServiceProvider;
use Mooti\Xizlr\Core\BaseController;
use Mooti\Xizlr\Core\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Interop\Container\ContainerInterface;
use League\Route\RouteCollection;
use League\Route\Dispatcher;
use ICanBoogie\Inflector;

class RestApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function createRequestSucceeds()
    {
        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        self::assertInstanceOf(Request::class, $restApplication->createRequest());
    }

    /**
     * @test
     */
    public function createRouteCollectionSucceeds()
    {

        $routeCollection = $this->getMockBuilder(RouteCollection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['createNew'])
            ->getMock();

        $routeCollection->expects(self::exactly(15))
            ->method('addRoute')
            ->withConsecutive(
                ['GET', '/{resourceNamePlural}', [$restApplication, 'callGetResources']],
                ['POST', '/{resourceNamePlural}', [$restApplication, 'callCreateNewResource']],
                ['PUT', '/{resourceNamePlural}', [$restApplication, 'callCreateNewResource']],
                ['HEAD', '/{resourceNamePlural}', [$restApplication, 'callMethodNotAllowed']],
                ['DEL', '/{resourceNamePlural}', [$restApplication, 'callMethodNotAllowed']],
                ['GET', '/{resourceNamePlural}/{id}', [$restApplication, 'callGetResource']],
                ['POST', '/{resourceNamePlural}/{id}', [$restApplication, 'callEditResource']],
                ['PUT', '/{resourceNamePlural}/{id}', [$restApplication, 'callEditResource']],
                ['HEAD', '/{resourceNamePlural}/{id}', [$restApplication, 'callResourceExists']],
                ['DEL', '/{resourceNamePlural}/{id}', [$restApplication, 'calldDeleteResource']],
                ['GET', '/{resourceNamePlural}/{id}/{childNamePlural}', [$restApplication, 'callGetChildResources']],
                ['POST', '/{resourceNamePlural}/{id}/{childNamePlural}', [$restApplication, 'callCreateNewChildResource']],
                ['PUT', '/{resourceNamePlural}/{id}/{childNamePlural}', [$restApplication, 'callCreateNewChildResource']],
                ['HEAD', '/{resourceNamePlural}/{id}/{childNamePlural}', [$restApplication, 'callMethodNotAllowed']],
                ['DEL', '/{resourceNamePlural}/{id}/{childNamePlural}', [$restApplication, 'callMethodNotAllowed']]
            );

        $restApplication->expects(self::once())
            ->method('createNew')
            ->with(self::equalTo(RouteCollection::class))
            ->will(self::returnValue($routeCollection));

        self::assertSame($routeCollection, $restApplication->createRouteCollection());
    }

    /**
     * @test
     */
    public function bootstrapWithNoCustomServicePorviderSucceeds()
    {
        $xizlrServiceProvider = $this->getMockBuilder(ServiceProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container->expects(self::once())
            ->method('registerServices')
            ->with(self::equalTo($xizlrServiceProvider));

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['createNew', 'setContainer'])
            ->getMock();

        $restApplication->expects(self::exactly(2))
            ->method('createNew')
            ->withConsecutive(
                [self::equalTo(Container::class)],
                [self::equalTo(ServiceProvider::class)]
            )
            ->will(self::onConsecutiveCalls($container, $xizlrServiceProvider));

        $restApplication->expects(self::once())
            ->method('setContainer')
            ->with(self::equalTo($container));

        $restApplication->bootstrap();
    }

    /**
     * @test
     */
    public function bootstrapWithCustomServicePorviderSucceeds()
    {
        $serviceProvider = $this->getMockBuilder(ServiceProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $xizlrServiceProvider = $this->getMockBuilder(ServiceProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container->expects(self::exactly(2))
            ->method('registerServices')
            ->withConsecutive(
                [self::equalTo($serviceProvider)],
                [self::equalTo($xizlrServiceProvider)]
            );

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['createNew', 'setContainer'])
            ->getMock();

        $restApplication->expects(self::exactly(2))
            ->method('createNew')
            ->withConsecutive(
                [self::equalTo(Container::class)],
                [self::equalTo(ServiceProvider::class)]
            )
            ->will(self::onConsecutiveCalls($container, $xizlrServiceProvider));

        $restApplication->expects(self::once())
            ->method('setContainer')
            ->with(self::equalTo($container));

        $restApplication->bootstrap($serviceProvider);
    }

    /**
     * @test
     * @expectedException Mooti\Xizlr\Core\Exception\InvalidModuleException
     */
    public function registerModulesThrowsInvalidModuleException()
    {
        $moduleName = '\\TestModule';

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['createNew'])
            ->getMock();

        $restApplication->expects(self::once())
            ->method('createNew')
            ->with(self::equalTo($moduleName))
            ->will(self::returnValue(new \stdClass));

        $restApplication->registerModules([$moduleName]);
    }

    /**
     * @test     
     */
    public function registerModulesSucceeds()
    {
        $moduleName = '\\TestModule';

        $serviceProvider = $this->getMockBuilder(ServiceProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $module = $this->getMockBuilder(ModuleInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $module->expects(self::once())
            ->method('getServiceProvider')
            ->will(self::returnValue($serviceProvider));

        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container->expects(self::once())
            ->method('registerServices')
            ->with(self::equalTo($serviceProvider));

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['getContainer', 'createNew'])
            ->getMock();

        $restApplication->expects(self::once())
            ->method('createNew')
            ->with(self::equalTo($moduleName))
            ->will(self::returnValue($module));

        $restApplication->expects(self::once())
            ->method('getContainer')
            ->will(self::returnValue($container));

        $restApplication->registerModules([$moduleName]);
    }


    /**
     * @test
     * @expectedException Mooti\Xizlr\Core\Exception\ContainerNotFoundException
     */
    public function runThrowsConatinerNotFoundException()
    {
        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['getContainer'])
            ->getMock();

        $restApplication->expects(self::once())
            ->method('getContainer')
            ->will(self::returnValue(null));

        $restApplication->run();
    }

    /**
     * @test
     */
    public function runSucceeds()
    {
        $requestMethod = 'GET';
        $requestPath   = '/test';

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request->expects(self::once())
            ->method('getMethod')
            ->will(self::returnValue($requestMethod));

        $request->expects(self::once())
            ->method('getPathInfo')
            ->will(self::returnValue($requestPath));

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response->expects(self::once())
            ->method('send');

        $dispatcher = $this->getMockBuilder(Dispatcher::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dispatcher->expects(self::once())
            ->method('dispatch')
            ->with(
                self::equalTo($requestMethod),
                self::equalTo($requestPath)
            )
            ->will(self::returnValue($response));

        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $routeCollection = $this->getMockBuilder(RouteCollection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $routeCollection->expects(self::once())
            ->method('getDispatcher')
            ->will(self::returnValue($dispatcher));

        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['createRouteCollection', 'createRequest', 'getContainer'])
            ->getMock();

        $restApplication->expects(self::once())
            ->method('getContainer')
            ->will(self::returnValue($container));

        $restApplication->expects(self::once())
            ->method('createRouteCollection')
            ->will(self::returnValue($routeCollection));

        $restApplication->expects(self::once())
            ->method('createRouteCollection')
            ->will(self::returnValue($routeCollection));

        $restApplication->expects(self::once())
            ->method('createRequest')
            ->will(self::returnValue($request));

        self::assertNull($restApplication->run());
    }

    /**
     * @test
     */
    public function createControllerSucceeds()
    {
        $controllers = [
            'test' => BaseController::class
        ];

        $controller =  $this->getMockBuilder(BaseController::class)
            ->disableOriginalConstructor()
            ->getMock();

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->setConstructorArgs([$controllers])
            ->setMethods(['createNew'])
            ->getMock();

        $restApplication->expects(self::once())
            ->method('createNew')
            ->with(self::equalTo(BaseController::class))
            ->will(self::returnValue($controller));

        self::assertSame($controller, $restApplication->createController('test'));
    }

    /**
     * @test
     * @expectedException Mooti\Xizlr\Core\Exception\ControllerNotFoundException
     */
    public function createControllerThrowsControllerNotFoundException()
    {
        $controllers = [];

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->setConstructorArgs([$controllers])
            ->setMethods(null)
            ->getMock();

        $restApplication->createController('test');
    }

    /**
     * @test
     * @expectedException Mooti\Xizlr\Core\Exception\InvalidControllerException
     */
    public function createControllerThrowsInvalidControllerException()
    {
        $controllers = [
            'test' => '\\stdClass'
        ];

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->setConstructorArgs([$controllers])
            ->setMethods(null)
            ->getMock();

        $restApplication->createController('test');
    }

    /**
     * @test
     */
    public function callMethodSucceeds()
    {
        $resourceName = 'test';
        $methodName   = 'foo';
        $argument     = 'bar';
        $return       = 'bar';

        $controller =  new TestClassWithMethod;

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['createController'])
            ->getMock();

        $restApplication->expects(self::once())
            ->method('createController')
            ->with(self::equalTo($resourceName))
            ->will(self::returnValue($controller));

        self::assertSame($return, $restApplication->callMethod($resourceName, $methodName, [$argument]));
    }

    /**
     * @test
     * @expectedException Mooti\Xizlr\Core\Exception\InvalidMethodException
     */
    public function callMethodThrowsInvalidMethodException()
    {
        $resourceName = 'test';
        $methodName   = 'foo';

        $controller =  $controller =  $this->getMockBuilder('\\stdClass')
            ->disableOriginalConstructor()
            ->getMock();

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['createController'])
            ->getMock();

        $restApplication->expects(self::once())
            ->method('createController')
            ->with(self::equalTo($resourceName))
            ->will(self::returnValue($controller));

        $restApplication->callMethod($resourceName, $methodName, []);
    }

    /**
     * @test
     */
    public function callGetResourcesSucceeds()
    {
        $resourceNamePlural = 'tests';
        $resourceNameCamel  = 'Tests';
        $methodName         = 'getTests';

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $inflector = $this->getMockBuilder(Inflector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $inflector->expects(self::once())
            ->method('camelize')
            ->with(self::equalTo($resourceNamePlural))
            ->will(self::returnValue($resourceNameCamel));

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['callMethod', 'get'])
            ->getMock();

        $restApplication->expects(self::once())
            ->method('get')
            ->with(self::equalTo(ServiceProvider::INFLECTOR))
            ->will(self::returnValue($inflector));

        $restApplication->expects(self::once())
            ->method('callMethod')
            ->with(
                self::equalTo($resourceNamePlural),
                self::equalTo($methodName),
                self::equalTo([$request, $response])
            )
            ->will(self::returnValue('foobar'));

        self::assertSame('foobar', $restApplication->callGetResources($request, $response, ['resourceNamePlural' => $resourceNamePlural]));
    }

    /**
     * @test
     */
    public function callCreateNewResourceSucceeds()
    {
        $resourceNamePlural       = 'tests';
        $resourceNameSingle       = 'test';
        $resourceNameSingleCamel  = 'Test';
        $methodName               = 'createTest';

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $inflector = $this->getMockBuilder(Inflector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $inflector->expects(self::once())
            ->method('singularize')
            ->with(self::equalTo($resourceNamePlural))
            ->will(self::returnValue($resourceNameSingle));

        $inflector->expects(self::once())
            ->method('camelize')
            ->with(self::equalTo($resourceNameSingle))
            ->will(self::returnValue($resourceNameSingleCamel));

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['callMethod', 'get'])
            ->getMock();

        $restApplication->expects(self::once())
            ->method('get')
            ->with(self::equalTo(ServiceProvider::INFLECTOR))
            ->will(self::returnValue($inflector));

        $restApplication->expects(self::once())
            ->method('callMethod')
            ->with(
                self::equalTo($resourceNamePlural),
                self::equalTo($methodName),
                self::equalTo([$request, $response])
            )
            ->will(self::returnValue('foobar'));

        self::assertSame('foobar', $restApplication->callCreateNewResource($request, $response, ['resourceNamePlural' => $resourceNamePlural]));
    }

    /**
     * @test
     */
    public function callGetResourceSucceeds()
    {
        $resourceNamePlural       = 'tests';
        $resourceNameSingle       = 'test';
        $resourceNameSingleCamel  = 'Test';
        $methodName               = 'getTest';
        $resourceId               = 123;

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $inflector = $this->getMockBuilder(Inflector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $inflector->expects(self::once())
            ->method('singularize')
            ->with(self::equalTo($resourceNamePlural))
            ->will(self::returnValue($resourceNameSingle));

        $inflector->expects(self::once())
            ->method('camelize')
            ->with(self::equalTo($resourceNameSingle))
            ->will(self::returnValue($resourceNameSingleCamel));

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['callMethod', 'get'])
            ->getMock();

        $restApplication->expects(self::once())
            ->method('get')
            ->with(self::equalTo(ServiceProvider::INFLECTOR))
            ->will(self::returnValue($inflector));

        $restApplication->expects(self::once())
            ->method('callMethod')
            ->with(
                self::equalTo($resourceNamePlural),
                self::equalTo($methodName),
                self::equalTo([$resourceId, $request, $response])
            )
            ->will(self::returnValue('foobar'));

        self::assertSame('foobar', $restApplication->callGetResource($request, $response, ['resourceNamePlural' => $resourceNamePlural, 'id' => $resourceId]));
    }

    /**
     * @test
     */
    public function callEditResourceSucceeds()
    {
        $resourceNamePlural       = 'tests';
        $resourceNameSingle       = 'test';
        $resourceNameSingleCamel  = 'Test';
        $methodName               = 'editTest';
        $resourceId               = 123;

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $inflector = $this->getMockBuilder(Inflector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $inflector->expects(self::once())
            ->method('singularize')
            ->with(self::equalTo($resourceNamePlural))
            ->will(self::returnValue($resourceNameSingle));

        $inflector->expects(self::once())
            ->method('camelize')
            ->with(self::equalTo($resourceNameSingle))
            ->will(self::returnValue($resourceNameSingleCamel));

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['callMethod', 'get'])
            ->getMock();

        $restApplication->expects(self::once())
            ->method('get')
            ->with(self::equalTo(ServiceProvider::INFLECTOR))
            ->will(self::returnValue($inflector));

        $restApplication->expects(self::once())
            ->method('callMethod')
            ->with(
                self::equalTo($resourceNamePlural),
                self::equalTo($methodName),
                self::equalTo([$resourceId, $request, $response])
            )
            ->will(self::returnValue('foobar'));

        self::assertSame('foobar', $restApplication->callEditResource($request, $response, ['resourceNamePlural' => $resourceNamePlural, 'id' => $resourceId]));
    }

    /**
     * @test
     */
    public function callResourceExistsSucceeds()
    {
        $resourceNamePlural       = 'testFoos';
        $resourceNameSingle       = 'testFoo';
        $resourceNameSingleCamel  = 'testFoo';
        $methodName               = 'testFooExists';
        $resourceId               = 123;

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $inflector = $this->getMockBuilder(Inflector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $inflector->expects(self::once())
            ->method('singularize')
            ->with(self::equalTo($resourceNamePlural))
            ->will(self::returnValue($resourceNameSingle));

        $inflector->expects(self::once())
            ->method('camelize')
            ->with(self::equalTo($resourceNameSingle))
            ->will(self::returnValue($resourceNameSingleCamel));

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['callMethod', 'get'])
            ->getMock();

        $restApplication->expects(self::once())
            ->method('get')
            ->with(self::equalTo(ServiceProvider::INFLECTOR))
            ->will(self::returnValue($inflector));

        $restApplication->expects(self::once())
            ->method('callMethod')
            ->with(
                self::equalTo($resourceNamePlural),
                self::equalTo($methodName),
                self::equalTo([$resourceId, $request, $response])
            )
            ->will(self::returnValue('foobar'));

        self::assertSame('foobar', $restApplication->callResourceExists($request, $response, ['resourceNamePlural' => $resourceNamePlural, 'id' => $resourceId]));
    }

    /**
     * @test
     */
    public function calldDeleteResourceSucceeds()
    {
        $resourceNamePlural       = 'tests';
        $resourceNameSingle       = 'test';
        $resourceNameSingleCamel  = 'Test';
        $methodName               = 'deleteTest';
        $resourceId               = 123;

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $inflector = $this->getMockBuilder(Inflector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $inflector->expects(self::once())
            ->method('singularize')
            ->with(self::equalTo($resourceNamePlural))
            ->will(self::returnValue($resourceNameSingle));

        $inflector->expects(self::once())
            ->method('camelize')
            ->with(self::equalTo($resourceNameSingle))
            ->will(self::returnValue($resourceNameSingleCamel));

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['callMethod', 'get'])
            ->getMock();

        $restApplication->expects(self::once())
            ->method('get')
            ->with(self::equalTo(ServiceProvider::INFLECTOR))
            ->will(self::returnValue($inflector));

        $restApplication->expects(self::once())
            ->method('callMethod')
            ->with(
                self::equalTo($resourceNamePlural),
                self::equalTo($methodName),
                self::equalTo([$resourceId, $request, $response])
            )
            ->will(self::returnValue('foobar'));

        self::assertSame('foobar', $restApplication->calldDeleteResource($request, $response, ['resourceNamePlural' => $resourceNamePlural, 'id' => $resourceId]));
    }

    /**
     * @test
     */
    public function callGetChildResourcesSucceeds()
    {
        $resourceNamePlural      = 'tests';
        $resourceId              = 123;
        $childNamePlural         = 'foos';
        $resourceNameSingle      = 'test';
        $resourceNameSingleCamel = 'Test';
        $childNameCamel          = 'Foos';
        $methodName              = 'getTestFoos';

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $inflector = $this->getMockBuilder(Inflector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $inflector->expects(self::once())
            ->method('singularize')
            ->with(self::equalTo($resourceNamePlural))
            ->will(self::returnValue($resourceNameSingle));

        $inflector->expects(self::exactly(2))
            ->method('camelize')
            ->withConsecutive(
                [self::equalTo($resourceNameSingle)],
                [self::equalTo($childNamePlural)]
            )
            ->will(
                self::onConsecutiveCalls($resourceNameSingleCamel, $childNameCamel)
            );

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['callMethod', 'get'])
            ->getMock();

        $restApplication->expects(self::once())
            ->method('get')
            ->with(self::equalTo(ServiceProvider::INFLECTOR))
            ->will(self::returnValue($inflector));

        $restApplication->expects(self::once())
            ->method('callMethod')
            ->with(
                self::equalTo($resourceNamePlural),
                self::equalTo($methodName),
                self::equalTo([$request, $response])
            )
            ->will(self::returnValue('foobar'));

        self::assertSame('foobar', $restApplication->callGetChildResources($request, $response, ['resourceNamePlural' => $resourceNamePlural, 'id' => $resourceId, 'childNamePlural' => $childNamePlural]));
    }

    /**
     * @test
     */
    public function callCreateNewChildResourceSucceeds()
    {
        $resourceNamePlural      = 'tests';
        $resourceId              = 123;
        $resourceNameSingle      = 'test';
        $resourceNameSingleCamel = 'Test';
        $childNamePlural         = 'foos';
        $childNameSingle         = 'foo';
        $childNameSingleCamel    = 'Foo';
        $methodName              = 'createTestFoo';

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $inflector = $this->getMockBuilder(Inflector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $inflector->expects(self::exactly(2))
            ->method('singularize')
            ->withConsecutive(
                [self::equalTo($resourceNamePlural)],
                [self::equalTo($childNamePlural)]
            )
            ->will(self::onConsecutiveCalls($resourceNameSingle, $childNameSingle));

        $inflector->expects(self::exactly(2))
            ->method('camelize')
            ->withConsecutive(
                [self::equalTo($resourceNameSingle)],
                [self::equalTo($childNameSingle)]
            )
            ->will(
                self::onConsecutiveCalls($resourceNameSingleCamel, $childNameSingleCamel)
            );

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['callMethod', 'get'])
            ->getMock();

        $restApplication->expects(self::once())
            ->method('get')
            ->with(self::equalTo(ServiceProvider::INFLECTOR))
            ->will(self::returnValue($inflector));

        $restApplication->expects(self::once())
            ->method('callMethod')
            ->with(
                self::equalTo($resourceNamePlural),
                self::equalTo($methodName),
                self::equalTo([$request, $response])
            )
            ->will(self::returnValue('foobar'));

        self::assertSame('foobar', $restApplication->callCreateNewChildResource($request, $response, ['resourceNamePlural' => $resourceNamePlural, 'id' => $resourceId, 'childNamePlural' => $childNamePlural]));
    }

    /**
     * @test
     * @expectedException Mooti\Xizlr\Core\Exception\MethodNotAllowedException
     */
    public function callMethodNotAllowedThrowsMethodNotAllowedException()
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $restApplication = $this->getMockBuilder(RestApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $restApplication->callMethodNotAllowed($request, $response, []);
    }
}
