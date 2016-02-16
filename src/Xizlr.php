<?php
/*
* Main xizlr trait
*
* Use this in order to use the container
*
* @author Ken Lalobo
*
*/
namespace Mooti\Xizlr\Core;

use Mooti\Xizlr\Testable\Testable;
use Interop\Container\ContainerInterface;

trait Xizlr
{
    use Testable {
        Testable::createNew as _createNew;
    }

    /**
     * @var ContainerInterface $container
     */
    private $container;

    /**
     * @return ContainerInterface The current container being used
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param ContainerInterface $container The container
     */
    public function setContainer(ContainerInterface $container)
    {
         $this->container = $container;
    }

    /**
     * Create a new instance of a given class
     *
     * @param string $className The class to create
     *
     * @return object The new class
     */
    public function createNew($className)
    {
        $object = $this->_createNew($className);
        
        $traits = class_uses($object);

        if (isset($traits[Xizlr::class]) == true) {
            $object->setContainer($this->container);
        }

        return $object;
    }

    /**
     * Get a service from the container
     *
     * @param string $serviceName The service name
     *
     * @return object The service
     */
    public function getService($serviceName)
    {
        return $this->container[$serviceName];
    }
}
