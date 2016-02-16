<?php
namespace Mooti\Xizlr\Core;

require dirname(__FILE__).'/../vendor/autoload.php';

use Mooti\Xizlr\Core\Xizlr;
use Interop\Container\ContainerInterface;
use Mooti\Test\Xizlr\Core\TestClassNoXizlr;
use Mooti\Test\Xizlr\Core\TestClassWithXizlr;

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
