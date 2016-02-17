<?php
namespace Mooti\Test\Xizlr\Core;

require dirname(__FILE__).'/../vendor/autoload.php';

use Mooti\Xizlr\Core\Container;

class ConatinerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getSucceeds()
    {
        $id = 'foobar';
        $object = new \stdClass();

        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->setMethods(['offsetExists', 'offsetGet'])
            ->getMock();

        $container->expects(self::once())
            ->method('offsetExists')
            ->with($id)
            ->will(self::returnValue(true));

        $container->expects(self::once())
            ->method('offsetGet')
            ->with($id)
            ->will(self::returnValue($object));

        self::assertSame($object, $container->get($id));
    }

    /**
     * @test
     * @expectedException Interop\Container\Exception\NotFoundException
     */
    public function getThrowsNotFoundException()
    {
        $id = 'foobar';

        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->setMethods(['offsetExists'])
            ->getMock();

        $container->expects(self::once())
            ->method('offsetExists')
            ->with($id)
            ->will(self::returnValue(false));

        $container->get($id);
    }

    /**
     * @test
     */
    public function hasSucceeds()
    {
        $id = 'foobar';

        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->setMethods(['offsetExists'])
            ->getMock();

        $container->expects(self::once())
            ->method('offsetExists')
            ->with($id)
            ->will(self::returnValue(true));

        self::assertSame(true, $container->has($id));
    }
}
