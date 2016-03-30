<?php
namespace Mooti\Test\Unit\Xizlr\Core;

use Mooti\Xizlr\Core\ServiceProvider;
use ICanBoogie\Inflector;

class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getServicesSucceeds()
    {
        $serviceProvider = new ServiceProvider;
        $services = $serviceProvider->getServices();

        self::assertInternalType('array', $services);
        self::assertInstanceOf(Inflector::class, $services[ServiceProvider::INFLECTOR]());
    }
}
