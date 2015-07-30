<?php
namespace Mooti\Xizlr\Core;

require dirname(__FILE__).'/../vendor/autoload.php';

use \Mooti\Xizlr\Core;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \Mooti\Xizlr\Core\Exception\ConfigException
     * @expectedExceptionMessage Cannot load config as "/foo/default/foo.test.ini"
     */
    public function loadConfigDefaultFileThrowsConfigException()
    {
        $configDir = '/foo';

        $config = $this->getMockBuilder('\Mooti\Xizlr\Core\Config')
            ->setConstructorArgs(array('foo', 'test', $configDir.'/default', $configDir.'/user'))
            ->setMethods(null)
            ->getMock();

        $config->loadConfig();
    }

    /**
     * @test
     * @expectedException \Mooti\Xizlr\Core\Exception\ConfigException
     * @expectedExceptionMessage Cannot load config as "/vagrant/apps/xizlr-core/test/fixtures/config/user/bar.test.ini"
     */
    public function loadUserFileThrowsConfigException()
    {
        $configDir = __DIR__.'/fixtures/config';

        $config = $this->getMockBuilder('\Mooti\Xizlr\Core\Config')
            ->setConstructorArgs(array('bar', 'test', $configDir.'/default', $configDir.'/user'))
            ->setMethods(null)
            ->getMock();

        $config->loadConfig();
    }

    /**
     * @test
     */
    public function loadConfigSucceeds()
    {
        $expected = array(
            'module'   => array(
                'name' => 'foo'
            ),
            'database' => array(
                'host' => 'localhost',
                'port' => 3306
            ),
            'user'     => array(
                'name' => 'ken'
            )
        );

        $configDir = __DIR__.'/fixtures/config';

        $config = $this->getMockBuilder('\Mooti\Xizlr\Core\Config')
            ->setConstructorArgs(array('foo', 'test', $configDir.'/default', $configDir.'/user'))
            ->setMethods(null)
            ->getMock();

        $this->assertEquals($expected, $config->loadConfig());
        $this->assertEquals($expected['database'], $config->get('database'));
    }

    /**
     * @test
     */
    public function setAndGetConfigSucceed()
    {
        $configArray = array(
            'test' => 'foo'
        );

        $config = $this->getMockBuilder('\Mooti\Xizlr\Core\Config')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $config->set('bar', $configArray);

        $this->assertEquals($configArray, $config->get('bar'));
    }
}
