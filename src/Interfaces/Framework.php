<?php
/*
* Main xizlr framework interface
*
* @author Ken Lalobo
*
*/
namespace Mooti\Xizlr\Core\Interfaces;

use Mooti\Xizlr\Core\Interfaces\Config;

interface Framework
{
    /**
     * @param \Mooti\Xizlr\Core\Interfaces\Config $config The config object
     */
    public function __construct(Config $config);

    /**
     * @param  string $configName The name of the config
     *
     * @return array The config as an array
     */
    public function getConfig($configName);

    /**
     * @param string $configName  The name of the config
     * @param array  $configValue  The value of the config
     */
    public function setConfig($configName, array $configValue);
}
