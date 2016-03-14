<?php
namespace Mooti\Test\Xizlr\Core;

require dirname(__FILE__).'/../vendor/autoload.php';

use Mooti\Xizlr\Core\ServiceProvider;

class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getServicesSucceeds()
    {
        $serviceProvider = $this->getMockBuilder(ServiceProvider::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        self::assertInternalType('array', $serviceProvider->getServices());
    }
}
