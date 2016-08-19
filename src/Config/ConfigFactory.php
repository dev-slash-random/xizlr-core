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
     * Set a config     
     *
     * @param string $configName The name of the config.
     *
     */
    public function setConfig($configName, AbstractConfig $config)
    {
        $this->configs[$configName] = $config;
    }

    /**
     * Get a config using a is namespaced config name.
     * This uses camel case and dot notation (i.e mooti.test.fooBar)
     *
     * @param string $configName The name of the config.
     *
     */
    public function getConfig($configName)
    {
        if (isset($this->configs[$configName])) {
            return $this->configs[$configName];
        }

        $applicationRootDirectory = $this->get(ServiceProvider::APPLICATION_RUNTIME)->getRootDirectory();
        $configDirectoryPath = $applicationRootDirectory . '/config/';

        $configNameParts = array_map(function ($item) { return ucfirst($item); }, explode('.', $configName));

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
