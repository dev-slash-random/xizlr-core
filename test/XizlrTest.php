<?php
namespace Mooti\Xizlr\Core;

require dirname(__FILE__).'/../vendor/autoload.php';

use Mooti\Xizlr\Core\Xizlr;
use Mooti\Xizlr\Core\Framework;
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
        $framework = $this->getMockBuilder(Framework::class)
            ->disableOriginalConstructor()
            ->getMock();

        $xizlr->setFramework($framework);
        
        self::assertSame($framework, $xizlr->getFramework());
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

        $framework = $this->getMockBuilder(Framework::class)
            ->disableOriginalConstructor()
            ->getMock();

        $xizlr->setFramework($framework);

        $newObject = $xizlr->createNew(TestClassWithXizlr::class);

        self::assertInstanceOf(TestClassWithXizlr::class, $newObject);

        self::assertSame($framework, $newObject->getFramework());
    }
}
