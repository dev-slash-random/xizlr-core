<?php
/*
 *
 * @author Ken Lalobo
 *
 */

namespace Mooti\Xizlr\Core;

use Interop\Container\ContainerInterface;

class Container implements ContainerInterface
{
    public function get($id)
    {
        return null;
    }

    public function has($id)
    {
        return false;
    }
}
