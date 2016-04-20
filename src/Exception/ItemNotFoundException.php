<?php
/**
 * ItemNotFoundException
 *
 *
 * @package      Mooti
 * @subpackage   Framework
 * @author       Ken Lalobo <ken@mooti.io>
 */

namespace Mooti\Framework\Exception;

use Interop\Container\Exception\NotFoundException;

class ItemNotFoundException extends \Exception implements NotFoundException
{
}