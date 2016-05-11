<?php

namespace Mooti\Framework\ServiceProvider;

use ICanBoogie\Inflector;
use Mooti\Framework\Application\ApplicationRuntime;
use Mooti\Framework\Config\ConfigFactory;

class ServiceProvider implements ServiceProviderInterface
{
    const INFLECTOR           = 'mooti.framework.inflector';
    const APPLICATION_RUNTIME = 'mooti.framework.applicationRuntime';
    const CONFIG_FACTORY      = 'mooti.framework.configFactory';

    /**
     * Get the details of the services we are providing     
     *
     * @return array
     */
    public function getServices()
    {
        return [
            self::INFLECTOR           => function () { return Inflector::get('en');},
            self::APPLICATION_RUNTIME => function () { return new ApplicationRuntime();},
            self::CONFIG_FACTORY      => function () { return new ConfigFactory();}
        ];
    }
}