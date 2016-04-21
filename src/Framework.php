<?php
/*
* Main xizlr trait
*
* Use this in order to use the container
*
* @author Ken Lalobo
*
*/
namespace Mooti\Framework;

use Mooti\Factory\Factory;
use Interop\Container\ContainerInterface;

trait Framework
{
    use Factory;

    /**
     * @var ContainerInterface $container
     */
    protected $container;

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

        if (isset($traits[Framework::class]) == true) {
            $object->setContainer($this->container);
        }

        return $object;
    }

    /**
     * Get an item from from the container
     *
     * @param string $id The Id of the item
     *
     * @return mixed The item
     */
    public function get($id)
    {
        return $this->container->get($id);
    }
}
