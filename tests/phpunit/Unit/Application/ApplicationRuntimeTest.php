<?php
namespace Mooti\Test\PHPUnit\Framework\Unit\Application;

use Mooti\Framework\Application\ApplicationRuntime;
use Mooti\Framework\Util\FileSystem;

class ApplicationRuntimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException Mooti\Framework\Exception\FileSystemException
     * @expectedExceptionMessage No application root directory found
     */
    public function locateRootDirectoryThrowsFileSystemException()
    {
        $fileSystem = $this->getMockBuilder(FileSystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fileSystem->expects(self::exactly(5))
            ->method('fileExists')
            ->will(self::returnValue(false));

        $applicationRuntime = $this->getMockBuilder(ApplicationRuntime::class)
            ->disableOriginalConstructor()
            ->setMethods(['createNew'])
            ->getMock();

        $applicationRuntime->expects(self::once())
            ->method('createNew')
            ->with(self::equalTo(FileSystem::class))
            ->will(self::returnValue($fileSystem));

        $applicationRuntime->locateRootDirectory();
    }

    /**
     * @test
     */
    public function locateRootDirectorySucceeds()
    {
        $path = '/foo/bar';

        $fileSystem = $this->getMockBuilder(FileSystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fileSystem->expects(self::once())
            ->method('fileExists')
            ->will(self::returnValue(true));

        $fileSystem->expects(self::once())
            ->method('getRealPath')
            ->will(self::returnValue($path));

        $applicationRuntime = $this->getMockBuilder(ApplicationRuntime::class)
            ->disableOriginalConstructor()
            ->setMethods(['createNew'])
            ->getMock();

        $applicationRuntime->expects(self::once())
            ->method('createNew')
            ->with(self::equalTo(FileSystem::class))
            ->will(self::returnValue($fileSystem));

        self::assertEquals($path, $applicationRuntime->locateRootDirectory());
    }

    /**
     * @test
     */
    public function getRootDirectory()
    {
        $path = '/foo/bar';

        $applicationRuntime = $this->getMockBuilder(ApplicationRuntime::class)
            ->disableOriginalConstructor()
            ->setMethods(['locateRootDirectory'])
            ->getMock();

        $applicationRuntime->expects(self::once())
            ->method('locateRootDirectory')
            ->will(self::returnValue($path));

        self::assertEquals($path, $applicationRuntime->getRootDirectory());
    }

    /**
     * @test
     */
    public function setAndGetNameSucceeds()
    {
        $name = 'testApp';

        $applicationRuntime = new ApplicationRuntime();
        $applicationRuntime->setName($name);
        self::assertEquals($name, $applicationRuntime->getName());
    }
}
