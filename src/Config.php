<?php
/*
* Config class
*
* This stores and gets the config options. 
*
*/
namespace Mooti\Xizlr\Core;

use \Mooti\Xizlr\Core\Interfaces\Config as ConfigInterface;
use Mooti\Xizlr\Core\Exception\ConfigException;

class Config implements ConfigInterface
{

    /**
     * @var string The module name
     */
    private $moduleName;

    /**
     * @var string The environment the system is in
     */
    private $environmentName;

    /**
     * @var string The location of the default config files
     */
    private $defaultConfigDir;

    /**
     * @var string The location of the user config files that overwrite the default config
     */
    private $userConfigDir;

    /**
     * @var array containing config values
     */
    private $config;

    /**
     * @param string $moduleName       The module name
     * @param string $environmentName  The environment the system is in
     * @param string $defaultConfigDir The location of the default config files
     * @param string $userConfigDir    The location of the user config files that overwrite the default config
     */
    public function __construct($moduleName, $environmentName, $defaultConfigDir, $userConfigDir = null)
    {
        $this->moduleName  = $moduleName;
        $this->environmentName  = $environmentName;
        $this->defaultConfigDir = $defaultConfigDir;
        $this->userConfigDir    = $userConfigDir;
        $this->loadConfig();
    }

    /**
     *
     * @return array  The full config loaded
     */
    public function loadConfig()
    {
        $defaultFile = $this->defaultConfigDir.'/module.'.$this->moduleName.'.'.$this->environmentName.'.ini';
        $userFile    = (empty($userConfigDir) ? null : $this->userConfigDir.'/module.'.$this->moduleName.'.'.$this->environmentName.'.ini');

        if (!file_exists($defaultFile)) {
            throw new ConfigException('Cannot load config as "'.$defaultFile.'" does not exist');
        }

        $defaultConfig = parse_ini_file($defaultFile, true, INI_SCANNER_TYPED);
        $this->config = $defaultConfig;

        if (!empty($userFile)) {
            if (!file_exists($userFile)) {
                throw new ConfigException('Cannot load config. "'.$userFile.'" does not exist');
            }
            $userConfig = parse_ini_file($userFile, true, INI_SCANNER_TYPED);

            $this->config = array_replace_recursive($defaultConfig, $userConfig);
        }

        return $this->config;
    }

    /**
     * @param string $configName The name of the config
     */
    public function get($configName)
    {
        return $this->config[$configName];
    }

    /**
     * @param string $configName  The name of the config
     * @param mixed  $configValue The value of the config
     */
    public function set($configName, $configValue)
    {
        $this->config[$configName] = $configValue;
    }
}
