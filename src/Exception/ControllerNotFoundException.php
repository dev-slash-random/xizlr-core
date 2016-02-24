<?php
/**
 * ControllerNotFoundException
 *
 *
 * @package      Xizlr
 * @subpackage   Core     
 * @author       Ken Lalobo <ken@mooti.io>
 */

namespace Mooti\Xizlr\Core\Exception;

use Interop\Container\Exception\NotFoundException;

class ControllerNotFoundException extends \Exception implements NotFoundException
{
}