<?php
namespace Mooti\Xizlr\Core;

require dirname(__FILE__).'/../vendor/autoload.php';

use \AllSaints\Shipping\Entity;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function runSucceeds()
    {
        $application = new Application();

        $this->assertEquals('PONG', $application->run());
    }
}
