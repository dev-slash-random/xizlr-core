<?php
namespace Mooti\Test\PHPUnit\Framework\Unit;

use Mooti\Framework\AbstractApplication;
use Mooti\Framework\ServiceProvider;
use Mooti\Framework\Container;
use Mooti\Framework\ModuleInterface;

class AbstractApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function setAndGetNameSucceeds()
    {
        $name = 'testApp';
        $application = $this->getMockForAbstractClass(AbstractApplication::class);
        self::assertNull($application->getName());
        $application->setName($name);
        self::assertEquals($name, $application->getName());
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

        $application = $this->getMockBuilder(AbstractApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['createNew', 'setContainer', 'runApplication'])
            ->getMock();

        $application->expects(self::exactly(2))
            ->method('createNew')
            ->withConsecutive(
                [self::equalTo(Container::class)],
                [self::equalTo(ServiceProvider::class)]
            )
            ->will(self::onConsecutiveCalls($container, $xizlrServiceProvider));

        $application->expects(self::once())
            ->method('setContainer')
            ->with(self::equalTo($container));

        $application->bootstrap();
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

        $application = $this->getMockBuilder(AbstractApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['createNew', 'setContainer', 'runApplication'])
            ->getMock();

        $application->expects(self::exactly(2))
            ->method('createNew')
            ->withConsecutive(
                [self::equalTo(Container::class)],
                [self::equalTo(ServiceProvider::class)]
            )
            ->will(self::onConsecutiveCalls($container, $xizlrServiceProvider));

        $application->expects(self::once())
            ->method('setContainer')
            ->with(self::equalTo($container));

        $application->bootstrap($serviceProvider);
    }

    /**
     * @test
     * @expectedException Mooti\Framework\Exception\InvalidModuleException
     */
    public function registerModulesThrowsInvalidModuleException()
    {
        $moduleName = '\\TestModule';

        $application = $this->getMockBuilder(AbstractApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['createNew', 'runApplication'])
            ->getMock();

        $application->expects(self::once())
            ->method('createNew')
            ->with(self::equalTo($moduleName))
            ->will(self::returnValue(new \stdClass));

        $application->registerModules([$moduleName]);
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

        $application = $this->getMockBuilder(AbstractApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['getContainer', 'createNew', 'runApplication'])
            ->getMock();

        $application->expects(self::once())
            ->method('createNew')
            ->with(self::equalTo($moduleName))
            ->will(self::returnValue($module));

        $application->expects(self::once())
            ->method('getContainer')
            ->will(self::returnValue($container));

        $application->registerModules([$moduleName]);
    }


    /**
     * @test
     * @expectedException Mooti\Framework\Exception\ContainerNotFoundException
     */
    public function runThrowsContainerNotFoundException()
    {
        $application = $this->getMockBuilder(AbstractApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['getContainer', 'runApplication'])
            ->getMock();

        $application->expects(self::once())
            ->method('getContainer')
            ->will(self::returnValue(null));

        $application->run();
    }

    /**
     * @test
     */
    public function runSucceeds()
    {
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $application = $this->getMockBuilder(AbstractApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['getContainer', 'runApplication'])
            ->getMock();

        $application->expects(self::once())
            ->method('getContainer')
            ->will(self::returnValue($container));

        $application->expects(self::once())
            ->method('runApplication');

        $application->run();
    }    
}
