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
        $serverVars = array(
            'REQUEST_METHOD' => 'GET'
        );
        $requestVars = array();

        $response = $this->getMockBuilder('\Mooti\Xizlr\Core\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $request = $this->getMockBuilder('\Mooti\Xizlr\Core\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $application = $this->getMockBuilder('\Mooti\Xizlr\Core\Application')
            ->disableOriginalConstructor()
            ->setMethods(array('instantiate'))
            ->getMock();

        $application->expects($this->exactly(2))
            ->method('instantiate')
            ->withConsecutive(
                array($this->equalTo('\Mooti\Xizlr\Core\Response')),
                array($this->equalTo('\Mooti\Xizlr\Core\Request'), $this->equalTo($serverVars), $this->equalTo($requestVars))
            )
            ->will(
                $this->onConsecutiveCalls(
                    $this->returnValue($response),
                    $this->returnValue($request)
                )
            );

        $this->assertSame($response, $application->run($serverVars, $requestVars));
    }
}
