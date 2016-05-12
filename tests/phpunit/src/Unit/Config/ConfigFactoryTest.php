<?php
namespace Mooti\Test\PHPUnit\Framework\Unit\Config;

use Mooti\Framework\Config\AbstractConfig;
use Mooti\Framework\Config\ConfigFactory;
use Mooti\Framework\Exception\ConfigNotFoundException;
use Mooti\Factory\Exception\ClassNotFoundException;
use Mooti\Framework\Application\ApplicationRuntime;
use Mooti\Framework\ServiceProvider\ServiceProvider;
use ICanBoogie\Inflector;

class ConfigFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test     
     */
    public function setAndGetConfigSucceeds()
    {
        $name = 'Config/Test';
        $config = $this->getMockForAbstractClass(AbstractConfig::class);

        $configFactory = $this->getMockBuilder(ConfigFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $configFactory->setConfig($name, $config);
        self::assertSame($config, $configFactory->getConfig($name));
    }

    /**
     * @test
     * @expectedException \Mooti\Framework\Exception\ConfigNotFoundException
     * @expectedExceptionMessage Cannot find the config class for Mooti/Test (tried \Mooti\Config\Test)
     */
    public function getConfigThrowsConfigNotFoundException()
    {
        $rootDirectory = '/foo/bar';
        $configName = 'Mooti/Test';
        $className = '\Mooti\Config\Test';

        $applicationRuntime = $this->getMockBuilder(ApplicationRuntime::class)
            ->disableOriginalConstructor()
            ->getMock();

        $applicationRuntime->expects(self::once())
            ->method('getRootDirectory')
            ->will(self::returnValue($rootDirectory));

        $configFactory = $this->getMockBuilder(ConfigFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'createNew'])
            ->getMock();

        $configFactory->expects(self::once())
            ->method('get')
            ->with(self::equalTo(ServiceProvider::APPLICATION_RUNTIME))
            ->will(self::returnValue($applicationRuntime));

        $configFactory->expects(self::once())
            ->method('createNew')
            ->with(self::equalTo($className))
            ->will(self::throwException(new ClassNotFoundException));

        $configFactory->getConfig($configName);
    }

    /**
     * @test     
     */
    public function getConfigSucceeds()
    {
       $rootDirectory             = '/foo/bar';
        $configName               = 'Mooti/BaseApp/Test';
        $configNameBase           = 'Mooti/BaseApp';
        $configNameBaseHyphenated = 'mooti/base-app';
        $className                = '\Mooti\BaseApp\Config\Test';
        $fullConfigDirectoryPath  = '/foo/bar/config/mooti/base-app';

        $applicationRuntime = $this->getMockBuilder(ApplicationRuntime::class)
            ->disableOriginalConstructor()
            ->getMock();

        $applicationRuntime->expects(self::once())
            ->method('getRootDirectory')
            ->will(self::returnValue($rootDirectory));

        $config = $this->getMockBuilder(AbstractConfig::class)
            ->disableOriginalConstructor()
            ->setMethods(['init', 'open', 'setDirPath'])
            ->getMock();

        $config->expects(self::once())
            ->method('setDirPath')
            ->with(self::equalTo($fullConfigDirectoryPath));  

        $config->expects(self::once())
            ->method('open');

        $inflector = $this->getMockBuilder(Inflector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $inflector->expects(self::once())
            ->method('hyphenate')
            ->with(self::equalTo($configNameBase))
            ->will(self::returnValue($configNameBaseHyphenated));            

        $configFactory = $this->getMockBuilder(ConfigFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'createNew', 'setConfig'])
            ->getMock();

        $configFactory->expects(self::exactly(2))
            ->method('get')
            ->withConsecutive(
                [self::equalTo(ServiceProvider::APPLICATION_RUNTIME)],
                [self::equalTo(ServiceProvider::INFLECTOR)]
            )
            ->will(self::onConsecutiveCalls($applicationRuntime, $inflector));

        $configFactory->expects(self::once())
            ->method('createNew')
            ->with(self::equalTo($className))
            ->will(self::returnValue($config));

        $configFactory->expects(self::once())
            ->method('setConfig')
            ->with(self::equalTo($configName), self::equalTo($config));

        $configFactory->getConfig($configName);
    }
}
