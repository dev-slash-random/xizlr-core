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

        $restApplication = $this->getMockBuilder(AbstractApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['createNew', 'setContainer', 'runApplication'])
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

        $restApplication = $this->getMockBuilder(AbstractApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['createNew', 'setContainer', 'runApplication'])
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
     * @expectedException Mooti\Framework\Exception\InvalidModuleException
     */
    public function registerModulesThrowsInvalidModuleException()
    {
        $moduleName = '\\TestModule';

        $restApplication = $this->getMockBuilder(AbstractApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['createNew', 'runApplication'])
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

        $restApplication = $this->getMockBuilder(AbstractApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['getContainer', 'createNew', 'runApplication'])
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
     * @expectedException Mooti\Framework\Exception\ContainerNotFoundException
     */
    public function runThrowsContainerNotFoundException()
    {
        $restApplication = $this->getMockBuilder(AbstractApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['getContainer', 'runApplication'])
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
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $restApplication = $this->getMockBuilder(AbstractApplication::class)
            ->disableOriginalConstructor()
            ->setMethods(['getContainer', 'runApplication'])
            ->getMock();

        $restApplication->expects(self::once())
            ->method('getContainer')
            ->will(self::returnValue($container));

        $restApplication->expects(self::once())
            ->method('runApplication');

        $restApplication->run();
    }    
}
