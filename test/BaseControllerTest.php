<?php
namespace Mooti\Test\Xizlr\Core;

require dirname(__FILE__).'/../vendor/autoload.php';

use Mooti\Xizlr\Core\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use JMS\Serializer\Serializer;

class BaseControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function renderWithArraySucceeds()
    {
        $contentArray = [
            'foo' => 'bar'
        ];

        $statusCode = 200;

        $contentString = '{"foo":"bar"}';

        $headers = $this->getMockBuilder(ResponseHeaderBag::class)
            ->disableOriginalConstructor()
            ->getMock();

        $headers->expects(self::once())
            ->method('set')
            ->with(
                self::equalTo('Content-Type'),
                self::equalTo('application/json')
            );

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response->headers = $headers;

        $response->expects(self::once())
            ->method('setStatusCode')
            ->with(self::equalTo($statusCode));

        $response->expects(self::once())
            ->method('setContent')
            ->with(self::equalTo($contentString));

        $serializer = $this->getMockBuilder(Serializer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serializer->expects(self::once())
            ->method('serialize')
            ->with($contentArray)
            ->will(self::returnValue($contentString));

        $abstractController = $this->getMockBuilder(BaseController::class)
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();

        $abstractController->expects(self::once())
            ->method('get')
            ->with('mooti.system.serializer')
            ->will(self::returnValue($serializer));

        self::assertSame($response, $abstractController->render($contentArray, $response));
    }
}
