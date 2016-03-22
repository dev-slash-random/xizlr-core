<?php
namespace Mooti\Test\Xizlr\Core;

require dirname(__FILE__).'/../vendor/autoload.php';

use Mooti\Xizlr\Core\ServiceProvider;
use ICanBoogie\Inflector;
use GUMP;

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
        self::assertInstanceOf(GUMP::class, $services[ServiceProvider::VALIDATOR]());
    }
}
