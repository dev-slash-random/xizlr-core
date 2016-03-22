<?php

namespace Mooti\Xizlr\Core;

use JMS\Serializer\SerializerBuilder;
use ICanBoogie\Inflector;
use GUMP;

class ServiceProvider implements ServiceProviderInterface
{
    const INFLECTOR  = 'xizlr.core.inflector';
    const VALIDATOR  = 'xizlr.core.validator';

    /**
     * Get the details of the services we are providing     
     *
     * @return array
     */
    public function getServices()
    {
        return [
            self::INFLECTOR  => function () { return Inflector::get('en');},
            self::VALIDATOR  => function () { return new GUMP();}
        ];
    }
}