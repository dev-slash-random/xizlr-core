<?php
namespace Mooti\Test\PHPUnit\Framework\Unit\Config;

use Mooti\Framework\Config\AbstractConfig;
use Mooti\Framework\Util\FileSystem;
use Mooti\Validator\Validator;
use Mooti\Framework\Exception\DataValidationException;
use Mooti\Framework\Exception\MalformedDataException;

class AbstractConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getSetConfigDataSucceeds()
    {
        $data = [
            'foo' => 'bar'
        ];
        $config = $this->getMockBuilder(AbstractConfig::class)
            ->disableOriginalConstructor()
            ->setMethods(['init'])
            ->getMock();

        $config->setConfigData($data);
        self::assertEquals($data, $config->getConfigData());
    }

    /**
     * @test
     */
    public function getFilepathWithDirPathSucceeds()
    {
        $filename = 'bar.json';
        $dirPath = '/foo';
        $filepath = '/foo/bar.json';

        $config = $this->getMockBuilder(AbstractConfig::class)
            ->disableOriginalConstructor()
            ->setMethods(['init'])
            ->getMock();

        $config->setFilename($filename);
        $config->setDirPath($dirPath);
        self::assertEquals($filepath, $config->getFilepath());
    }

    /**
     * @test
     */
    public function getFilepathWithNoDirPathSucceeds()
    {
        $filename = 'bar.json';
        $dirPath = '/foo';
        $filepath = '/foo/bar.json';

        $fileSystem = $this->getMockBuilder(FileSystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fileSystem->expects(self::once())
            ->method('getCurrentWorkingDirectory')
            ->will(self::returnValue($dirPath));

        $config = $this->getMockBuilder(AbstractConfig::class)
            ->disableOriginalConstructor()
            ->setMethods(['init', 'createNew', 'getCurrentWorkingDirectory'])
            ->getMock();

        $config->expects(self::once())
            ->method('createNew')
            ->with(self::equalTo(FileSystem::class))
            ->will(self::returnValue($fileSystem));

        $config->setFilename($filename);
        self::assertEquals($filepath, $config->getFilepath());
    }

    /**
     * @test
     * @expectedException \Mooti\Framework\Exception\DataValidationException
     * @expectedExceptionMessage The config is invalid: Array
(
    [stuff] => things
)
     */
    public function validateConfigThrowsDataValidationException()
    {
        $rules = [
            'some' => 'rules'
        ];

        $data = [
            'bad' => 'data'
        ];

        $errors = [
            'stuff' => 'things'
        ];

        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $validator->expects(self::once())
            ->method('isValid')
            ->with(self::equalTo($rules), self::equalTo($data))
            ->will(self::returnValue(false));

        $validator->expects(self::once())
            ->method('getErrors')
            ->will(self::returnValue($errors));

        $config = $this->getMockBuilder(AbstractConfig::class)
            ->disableOriginalConstructor()
            ->setMethods(['init', 'createNew'])
            ->getMock();

        $config->expects(self::once())
            ->method('createNew')
            ->with(self::equalTo(Validator::class))
            ->will(self::returnValue($validator));

        $config->validateConfig($rules, $data);
    }

    /**
     * @test
     */
    public function validateConfigSucceeds()
    {
        $rules = [
            'some' => 'rules'
        ];

        $data = [
            'good' => 'data'
        ];

        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $validator->expects(self::once())
            ->method('isValid')
            ->with(self::equalTo($rules), self::equalTo($data))
            ->will(self::returnValue(true));

        $config = $this->getMockBuilder(AbstractConfig::class)
            ->disableOriginalConstructor()
            ->setMethods(['init', 'createNew'])
            ->getMock();

        $config->expects(self::once())
            ->method('createNew')
            ->with(self::equalTo(Validator::class))
            ->will(self::returnValue($validator));

        $config->validateConfig($rules, $data);
    }

    /**
     * @test
     * @expectedException \Mooti\Framework\Exception\MalformedDataException
     * @expectedExceptionMessage The contents of the file "/foo/bar.json" are not valid json
     */
    public function openThrowsMalformedDataException()
    {
        $filepath = '/foo/bar.json';
        $contents = 'error';

        $fileSystem = $this->getMockBuilder(FileSystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fileSystem->expects(self::once())
            ->method('fileGetContents')
            ->with(self::equalTo($filepath))
            ->will(self::returnValue($contents));

        $config = $this->getMockBuilder(AbstractConfig::class)
            ->disableOriginalConstructor()
            ->setMethods(['init', 'createNew', 'getFilepath'])
            ->getMock();

        $config->expects(self::once())
            ->method('createNew')
            ->with(self::equalTo(FileSystem::class))
            ->will(self::returnValue($fileSystem));

        $config->expects(self::once())
            ->method('getFilepath')
            ->will(self::returnValue($filepath));

        $config->open();
    }

    /**
     * @test
     */
    public function openSucceeds()
    {
        $filepath = '/foo/bar.json';
        $contents = '{"foo":"bar"}';
        $data = [
            'foo' => 'bar'
        ];
        $rules = [
            'some' => 'rules'
        ];

        $fileSystem = $this->getMockBuilder(FileSystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fileSystem->expects(self::once())
            ->method('fileGetContents')
            ->with(self::equalTo($filepath))
            ->will(self::returnValue($contents));

        $config = $this->getMockBuilder(AbstractConfig::class)
            ->disableOriginalConstructor()
            ->setMethods(['init', 'createNew', 'getFilepath', 'validateConfig'])
            ->getMock();

        $config->expects(self::once())
            ->method('createNew')
            ->with(self::equalTo(FileSystem::class))
            ->will(self::returnValue($fileSystem));

        $config->expects(self::once())
            ->method('validateConfig')
            ->with(self::equalTo($rules), self::equalTo($data));

        $config->expects(self::once())
            ->method('getFilepath')
            ->will(self::returnValue($filepath));

        $config->setRules($rules);

        $config->open();
    }

    /**
     * @test
     */
    public function saveSucceeds()
    {
        $filepath = '/foo/bar.json';
        $contents = "{\n    \"foo\": \"bar\"\n}";
        $data = [
            'foo' => 'bar'
        ];
        $rules = [
            'some' => 'rules'
        ];

        $fileSystem = $this->getMockBuilder(FileSystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fileSystem->expects(self::once())
            ->method('filePutContents')
            ->with(self::equalTo($filepath), self::equalTo($contents));

        $config = $this->getMockBuilder(AbstractConfig::class)
            ->disableOriginalConstructor()
            ->setMethods(['init', 'createNew', 'getFilepath', 'validateConfig'])
            ->getMock();

        $config->expects(self::once())
            ->method('createNew')
            ->with(self::equalTo(FileSystem::class))
            ->will(self::returnValue($fileSystem));

        $config->expects(self::once())
            ->method('validateConfig')
            ->with(self::equalTo($rules), self::equalTo($data));

        $config->expects(self::once())
            ->method('getFilepath')
            ->will(self::returnValue($filepath));

        $config->setRules($rules);
        $config->setConfigData($data);

        $config->save();
    }
}
