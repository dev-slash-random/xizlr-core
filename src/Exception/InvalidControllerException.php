<?php
/**
 * InvalidControllerException
 *
 * @package      Xizlr
 * @subpackage   Core     
 * @author       Ken Lalobo <ken@mooti.io>
 */

namespace Mooti\Xizlr\Core\Exception;

use Interop\Container\Exception\NotFoundException;

class InvalidControllerException extends \Exception implements NotFoundException
{
}