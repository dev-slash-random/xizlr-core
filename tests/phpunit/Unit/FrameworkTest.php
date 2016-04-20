<?php
namespace Mooti\Test\Unit\Framework;

use Mooti\Framework\Framework;
use Interop\Container\ContainerInterface;
use Mooti\Test\PHPUnit\Framework\Unit\Fixture\TestClassNoFramework;
use Mooti\Test\PHPUnit\Framework\Unit\Fixture\TestClassWithFramework;

class FrameworkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function setterAnGetterSucceeds()
    {
        $framework = $this->getMockForTrait(Framework::class);
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $framework->setContainer($container);
        
        self::assertSame($container, $framework->getContainer());
    }

    /**
     * @test
     */
    public function getSucceeds()
    {
        $framework = $this->getMockForTrait(Framework::class);
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container->expects(self::once())
            ->method('get')
            ->with(self::equalTo('foo'))
            ->will(self::returnValue('bar'));

        $framework->setContainer($container);
        
        self::assertSame('bar', $framework->get('foo'));
    }

    /**
     * @test
     */
    public function createNewNoFrameworkSucceeds()
    {
        $framework = $this->getMockForTrait(Framework::class);

        self::assertInstanceOf(TestClassNoFramework::class, $framework->createNew(TestClassNoFramework::class));
    }

    /**
     * @test
     */
    public function createNewWithFrameworkSucceeds()
    {
        $framework = $this->getMockForTrait(Framework::class);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $framework->setContainer($container);

        $newObject = $framework->createNew(TestClassWithFramework::class);

        self::assertInstanceOf(TestClassWithFramework::class, $newObject);

        self::assertSame($container, $newObject->getContainer());
    }
}
