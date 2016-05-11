<?php

namespace Mooti\Framework\ServiceProvider;

use ICanBoogie\Inflector;
use Mooti\Framework\Application\ApplicationRuntime;

class ServiceProvider implements ServiceProviderInterface
{
    const INFLECTOR           = 'mooti.framework.inflector';
    const APPLICATION_RUNTIME = 'mooti.framework.applicationRuntime';

    /**
     * Get the details of the services we are providing     
     *
     * @return array
     */
    public function getServices()
    {
        return [
            self::INFLECTOR           => function () { return Inflector::get('en');},
            self::APPLICATION_RUNTIME => function () { return new ApplicationRuntime();}
        ];
    }
}