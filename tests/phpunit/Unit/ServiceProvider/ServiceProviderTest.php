<?php
namespace Mooti\Test\PHPUnit\Framework\Unit\ServiceProvider;

use Mooti\Framework\ServiceProvider\ServiceProvider;
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
