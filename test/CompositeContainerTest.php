<?php
namespace Mooti\Test\Xizlr\Core;

require dirname(__FILE__).'/../vendor/autoload.php';

use Mooti\Xizlr\Core\CompositeContainer;
use Mooti\Xizlr\Core\Container;

class CompositeContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getSucceeds()
    {
        $id    = 'foo';
        $value = 'bar';

        $container1 = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container1->expects(self::once())
            ->method('has')
            ->with(self::equalTo($id))
            ->will(self::returnValue(false));

        $container2 = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container2->expects(self::once())
            ->method('has')
            ->with(self::equalTo($id))
            ->will(self::returnValue(true));

        $container2->expects(self::once())
            ->method('get')
            ->with(self::equalTo($id))
            ->will(self::returnValue($value));

        $compositeContainer = $this->getMockBuilder(CompositeContainer::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $compositeContainer->addContainer($container1);
        $compositeContainer->addContainer($container2);

        self::assertSame($value, $compositeContainer->get($id));
    }

    /**
     * @test
     * @expectedException Interop\Container\Exception\NotFoundException
     */
    public function getThrowsNotFoundException()
    {
        $id    = 'foo';

        $container1 = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container1->expects(self::once())
            ->method('has')
            ->with(self::equalTo($id))
            ->will(self::returnValue(false));

        $container2 = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container2->expects(self::once())
            ->method('has')
            ->with(self::equalTo($id))
            ->will(self::returnValue(false));

        $compositeContainer = $this->getMockBuilder(CompositeContainer::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $compositeContainer->addContainer($container1);
        $compositeContainer->addContainer($container2);

        $compositeContainer->get($id);
    }

    /**
     * @test
     */
    public function hasSucceedsWithTrue()
    {
        $id    = 'foo';

        $container1 = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container1->expects(self::once())
            ->method('has')
            ->with(self::equalTo($id))
            ->will(self::returnValue(false));

        $container2 = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container2->expects(self::once())
            ->method('has')
            ->with(self::equalTo($id))
            ->will(self::returnValue(true));

        $compositeContainer = $this->getMockBuilder(CompositeContainer::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $compositeContainer->addContainer($container1);
        $compositeContainer->addContainer($container2);

        self::assertSame(true, $compositeContainer->has($id));
    }

    /**
     * @test
     */
    public function hasSucceedsWithFalse()
    {
        $id    = 'foo';

        $container1 = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container1->expects(self::once())
            ->method('has')
            ->with(self::equalTo($id))
            ->will(self::returnValue(false));

        $container2 = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container2->expects(self::once())
            ->method('has')
            ->with(self::equalTo($id))
            ->will(self::returnValue(false));

        $compositeContainer = $this->getMockBuilder(CompositeContainer::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $compositeContainer->addContainer($container1);
        $compositeContainer->addContainer($container2);

        self::assertSame(false, $compositeContainer->has($id));
    }
}
