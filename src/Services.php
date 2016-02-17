<?php

namespace Mooti\Xizlr\Core;

use JMS\Serializer\SerializerBuilder;
use ICanBoogie\Inflector;

class Services
{
    const SERIALIZER = 'mooti.system.serializer';
    const INFLECTOR  = 'mooti.system.inflector';

    public static function getDefinitions()
    {
        return [
            self::SERIALIZER => function ($c) { return SerializerBuilder::create()->build();},
            self::INFLECTOR  => function ($c) { return Inflector::get('en');}
        ];
    }
}