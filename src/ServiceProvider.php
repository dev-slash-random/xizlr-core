<?php

namespace Mooti\Xizlr\Core;

use JMS\Serializer\SerializerBuilder;
use ICanBoogie\Inflector;

class ServiceProvider implements ServiceProviderInterface
{
    const SERIALIZER = 'xizlr.core.serializer';
    const INFLECTOR  = 'xizlr.core.inflector';

    /**
     * Get the details of the services we are providing     
     *
     * @return array
     */
    public function getServices()
    {
        return [
            self::SERIALIZER => function ($c) { return SerializerBuilder::create()->build();},
            self::INFLECTOR  => function ($c) { return Inflector::get('en');}
        ];
    }
}