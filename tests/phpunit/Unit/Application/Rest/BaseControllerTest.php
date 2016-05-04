<?php
namespace Mooti\Test\PHPUnit\Framework\Unit\Application\Rest;

use Mooti\Framework\Application\Rest\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use JsonSerializable;

class BaseControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function renderWithJsonSerializableSucceeds()
    {
        $contentArray = [
            'foo' => 'bar'
        ];

        $model = $this->getMockBuilder(JsonSerializable::class)
            ->disableOriginalConstructor()
            ->setMethods(array('jsonSerialize'))
            ->getMock();

        $model->expects(self::once())
            ->method('jsonSerialize')
            ->will(self::returnValue($contentArray));

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

        $baseConttroller =  new BaseController;

        self::assertSame($response, $baseConttroller->render($model, $response));
    }
}
