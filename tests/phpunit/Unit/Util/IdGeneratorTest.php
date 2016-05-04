<?php
namespace Mooti\Test\PHPUnit\Framework\Unit;

use Mooti\Framework\Util\IdGenerator;

class UtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function uuidV4Succeeds()
    {
    	$idGenerator = new IdGenerator();
        self::assertRegExp('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $idGenerator->uuidV4());
    }
}
