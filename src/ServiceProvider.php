<?php

namespace Mooti\Framework;

use ICanBoogie\Inflector;

class ServiceProvider implements ServiceProviderInterface
{
    const INFLECTOR  = 'mooti.framework.inflector';

    /**
     * Get the details of the services we are providing     
     *
     * @return array
     */
    public function getServices()
    {
        return [
            self::INFLECTOR  => function () { return Inflector::get('en');},
        ];
    }
}