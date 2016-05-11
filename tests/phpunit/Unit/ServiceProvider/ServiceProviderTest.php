<?php
namespace Mooti\Test\PHPUnit\Framework\Unit\ServiceProvider;

use Mooti\Framework\ServiceProvider\ServiceProvider;
use ICanBoogie\Inflector;
use Mooti\Framework\Application\ApplicationRuntime;
use Mooti\Framework\Config\ConfigFactory;

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
        self::assertInstanceOf(ApplicationRuntime::class, $services[ServiceProvider::APPLICATION_RUNTIME]());
        self::assertInstanceOf(ConfigFactory::class, $services[ServiceProvider::CONFIG_FACTORY]());
    }
}
