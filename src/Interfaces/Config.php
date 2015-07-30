<?php
/*
* Config interface
*
* This stores and gets the config options. 
*
*/
namespace Mooti\Xizlr\Core\Interfaces;

interface Config
{
    /**
     * @param string $applicationName  The application name
     * @param string $environmentName  The environment the system is in
     * @param string $defaultConfigDir The location of the default config files
     * @param string $userConfigDir    The location of the user config files that overwrite the default config
     */
    public function __construct($applicationName, $environmentName, $defaultConfigDir, $userConfigDir = null);

    /**
     *
     * @return array  The full config loaded
     */
    public function loadConfig();

    /**
     * @param string $configName The name of the config
     */
    public function get($configName);

    /**
     * @param string $configName  The name of the config
     * @param mixed  $configValue The value of the config
     */
    public function set($configName, $configValue);
}
