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

use Mooti\Xizlr\Testable\Testable;
use Mooti\Xizlr\Core\Framework;

trait Xizlr
{
    use Testable {
        Testable::createNew as _createNew;
    }

    /**
     * @var Framework $framework
     */
    protected $framework;

    /**
     * @return Framework The current framework being used
     */
    public function getFramework(): Framework
    {
        return $this->framework;
    }

    /**
     * @param Framework $framework The frameowrk
     */
    public function setFramework(Framework $framework)
    {
         $this->framework = $framework;
    }

    /**
     * Create a new instance of a given class
     *
     * @param string $className The class to create
     *
     * @return object The new class
     */
    public function createNew(string $className)
    {
        $object = $this->_createNew($className);
        
        $traits = class_uses($object);

        if (isset($traits[Xizlr::class]) == true) {
            $object->setFramework($this->framework);
        }

        return $object;
    }
}
