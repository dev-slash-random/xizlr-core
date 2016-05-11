<?php
/**
 * Container
 *
 * A simple Container Interop base container.
 *
 * @package      Mooti
 * @subpackage   Framework     
 * @author       Ken Lalobo <ken@mooti.io>
 */ 

namespace Mooti\Framework;

use Interop\Container\ContainerInterface;
use Mooti\Framework\ServiceProvider\ServiceProviderInterface;
use Mooti\Framework\Exception\ItemNotFoundException;

class Container implements ContainerInterface
{
    /**
     * @var array $items The items in the container
     */
    private $items = array();

    /**
     * Sets an entry in the container by its identifier.
     *
     * @param string $id    Identifier of the entry to place.
     * @param mixed  $value Entry to place
     *
     */
    public function set($id, $value)
    {
        $this->items[$id] = $value;
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
        if (isset($this->items[$id]) == false) {
            throw new ItemNotFoundException('id '.$id.' was not found in the container');
        }

        $item = $this->items[$id];

        if (is_callable($item) == false) {
            return $item;
        }
        
        $newItem = $item();
        $this->items[$id] = $newItem;

        if (gettype($newItem) != 'object') {
            return $newItem;
        }

        $traits = class_uses($newItem);
        if (isset($traits[Framework::class]) == true) {
            $newItem->setContainer($this);
        }

        return $newItem;
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
        return isset($this->items[$id]);
    }

    /**
     * Add services to the container
     *
     * @param ServiceProviderInterface $serviceProvider A service provider
     *
     */
    public function registerServices(ServiceProviderInterface $serviceProvider)
    {
        $services = $serviceProvider->getServices();      
        foreach ($services as $id => $service) {
            if ($this->has($id) == false) {
                $this->set($id, $service);
            }
        }
    }
}
