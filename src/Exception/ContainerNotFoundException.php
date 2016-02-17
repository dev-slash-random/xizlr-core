<?php
/**
 * ContainerNotFoundException
 *
 * Extends the Interop/NotFoundException
 *
 * @package      Xizlr
 * @subpackage   Core     
 * @author       Ken Lalobo <ken@mooti.io>
 */ 

namespace Mooti\Xizlr\Core\Exception;

use Interop\Container\Exception\NotFoundException;

class ContainerNotFoundException extends \Exception implements NotFoundException
{
}