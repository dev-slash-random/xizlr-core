<?php
/*
* Config class
*
* This stores and gets the config options. 
* It uses a combination of json files (for default values)
* and redis (for custom values)
*
*/
namespace Mooti\Xizlr\Core;

class Config
{

    //array conating config values
    private $config;

    public function __construct($configLocation)
    {

    }

}
