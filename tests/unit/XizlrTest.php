<?php
namespace Mooti\Test\Unit\Xizlr\Core;

use Mooti\Xizlr\Core\Xizlr;
use Interop\Container\ContainerInterface;
use Mooti\Test\Unit\Xizlr\Core\Fixture\TestClassNoXizlr;
use Mooti\Test\Unit\Xizlr\Core\Fixture\TestClassWithXizlr;

class XizlrTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function setterAnGetterSucceeds()
    {
        $xizlr     = $this->getMockForTrait(Xizlr::class);
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $xizlr->setContainer($container);
        
        self::assertSame($container, $xizlr->getContainer());
    }

    /**
     * @test
     */
    public function getSucceeds()
    {
        $xizlr     = $this->getMockForTrait(Xizlr::class);
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container->expects(self::once())
            ->method('get')
            ->with(self::equalTo('foo'))
            ->will(self::returnValue('bar'));

        $xizlr->setContainer($container);
        
        self::assertSame('bar', $xizlr->get('foo'));
    }

    /**
     * @test
     */
    public function createNewNoXizlrSucceeds()
    {
        $xizlr = $this->getMockForTrait(Xizlr::class);

        self::assertInstanceOf(TestClassNoXizlr::class, $xizlr->createNew(TestClassNoXizlr::class));
    }

    /**
     * @test
     */
    public function createNewWithXizlrSucceeds()
    {
        $xizlr = $this->getMockForTrait(Xizlr::class);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $xizlr->setContainer($container);

        $newObject = $xizlr->createNew(TestClassWithXizlr::class);

        self::assertInstanceOf(TestClassWithXizlr::class, $newObject);

        self::assertSame($container, $newObject->getContainer());
    }
}
