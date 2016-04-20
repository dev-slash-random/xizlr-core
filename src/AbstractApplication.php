<?php
/**
 *
 * The abstract Application class
 *
 * @package      Mooti
 * @subpackage   Framework    
 * @author       Ken Lalobo <ken@mooti.io>
 */

namespace Mooti\Framework;

use Mooti\Framework\Framework;
use Mooti\Framework\Exception\InvalidModuleException;
use Mooti\Framework\Exception\ContainerNotFoundException;

abstract class AbstractApplication
{
    use Framework;

    /**
     * Bootstrap the application
     *
     * @param ServiceProviderInterface $serviceProvider An option service provider
     */
    public function bootstrap(ServiceProviderInterface $serviceProvider = null)
    {
        $container = $this->createNew(Container::class);

        if (isset($serviceProvider)) {
            $container->registerServices($serviceProvider);    
        }

        $xizlrServiceProvider = $this->createNew(ServiceProvider::class);
        $container->registerServices($xizlrServiceProvider);

        $this->setContainer($container);
    }

    /**
     * Register some modules
     *
     */
    public function registerModules($modules = [])
    {
        for($i = 0; $i < sizeof($modules); $i++) {
            $moduleName = $modules[$i];

            $module = $this->createNew($moduleName);

            if (!$module instanceof ModuleInterface) {
                throw new InvalidModuleException('The module at position '.($i+1).' is invalid');
            }

            $serviceProvider = $module->getServiceProvider();
            $this->getContainer()->registerServices($serviceProvider);
        }
    }

    /**
     * Run the application
     *
     */
    public function run()
    {
        if (empty($this->getContainer()) == true) {
            throw new ContainerNotFoundException('The container cannot be found. Have you forgotten to bootstrap your application?');
        }

        $this->runApplication();
    }

    /**
     * Run the application
     *
     */
    abstract public function runApplication();
}
