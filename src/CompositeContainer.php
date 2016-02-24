<?php
/**
 * Container
 *
 * A simple Composite Container using Container Interop.
 *
 * @package      Xizlr
 * @subpackage   Core     
 * @author       Ken Lalobo <ken@mooti.io>
 */ 

namespace Mooti\Xizlr\Core;

use Interop\Container\ContainerInterface;
use Mooti\Xizlr\Core\Exception\ContainerNotFoundException;

class CompositeContainer implements ContainerInterface
{
    /**
     * @var array Containers that are contained within this composite container
     */
    protected $containers = array();

    /**
     * Adds a container to an internal queue of containers
     *
     * @param ContainerInterface $container The container to add
     *
     */
    public function addContainer(ContainerInterface $container)
    {
        $this->containers[] = $container;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundException  No entry was found for this identifier.
     * @throws ContainerException Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        foreach ($this->containers as $container) {
            if ($container->has($id)) {
                return $container->get($id);
            }
        }

        throw new ContainerNotFoundException('id '.$id.' was not found in the container');        
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     * 
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundException`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return boolean
     */
    public function has($id)
    {
        foreach ($this->containers as $container) {
            if ($container->has($id)) {
                return true;
            }
        }        
        return false;
    }
}
