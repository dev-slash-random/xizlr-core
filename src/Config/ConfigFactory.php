<?php
/**
 * Config Factory
 *
 * Config factory class
 *
 * @package      Mooti
 * @subpackage   Framework     
 * @author       Ken Lalobo <ken@mooti.io>
 */

namespace Mooti\Framework\Config;

use Mooti\Framework\Framework;
use Mooti\Factory\Exception\ClassNotFoundException;
use Mooti\Framework\Exception\ConfigNotFoundException;
use Mooti\Framework\ServiceProvider\ServiceProvider;

class ConfigFactory
{
    use Framework;

    /**
     * @var array $configs An array of currently loaded configs
     */
    protected $configs = [];

    /**
     * Get a config
     *
     * @param string $configName The name of the config. This namespaced (i.e Mooti/Framework/Database)
     *
     */
    public function setConfig($configName, AbstractConfig $config)
    {
        $this->configs[$configName] = $config;
    }

    /**
     * Get a config
     *
     * @param string $configName The name of the config. This namespaced (i.e Mooti/Framework/Database)
     *
     */
    public function getConfig($configName)
    {
        if (isset($this->configs[$configName])) {
            return $this->configs[$configName];
        }

        $applicationRootDirectory = $this->get(ServiceProvider::APPLICATION_RUNTIME)->getRootDirectory();
        $configDirectoryPath = $applicationRootDirectory . '/config/';

        $configNameParts = explode('/', $configName);

        $lastPart = array_pop($configNameParts);

        $className = '\\' . implode('\\', $configNameParts) . '\\Config\\' . $lastPart;

        try {
            $config = $this->createNew($className);
        } catch (ClassNotFoundException $e) {
            throw new ConfigNotFoundException('Cannot find the config class for '.$configName.' (tried '.$className.')');
        }

        $inflector = $this->get(ServiceProvider::INFLECTOR);
        $fullConfigDirectoryPath = $configDirectoryPath . $inflector->hyphenate(implode('/', $configNameParts));

        $config->setDirPath($fullConfigDirectoryPath);
        $config->open();
        $this->setConfig($configName, $config);
        return $config;
    }
}
