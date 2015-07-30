<?php
/*
* Main xizlr trait
*
* Use this in order to use the framework
*
* @author Ken Lalobo
*
*/
namespace Mooti\Xizlr\Core;

use Mooti\Xizlr\Core\Application;

trait Xizlr
{
    /**
     * @return \Mooti\Xizlr\Core\Interfaces\Framework The current framework being used
     */
    public function getFramework()
    {
        return Application::getFramework();
    }

    /**
     * @param string $configName The name of the config

     * @return array The config as an array
     */
    public function getConfig($configName)
    {
        return Application::getFramework()->getConfig($configName);
    }

    /**
     * @param string $configName  The name of the config
     * @param array  $configValue  The value of the config
     */
    public function setConfig($configName, array $configValue)
    {
         Application::getFramework()->setConfig($configName, $configValue);
    }
}
