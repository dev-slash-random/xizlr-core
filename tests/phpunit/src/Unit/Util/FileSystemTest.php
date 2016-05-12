<?php

namespace Mooti\Test\PHPUnit\Framework\Unit\Util;
 
use Mooti\Framework\Util\FileSystem;
use Mooti\Framework\Exception\FileSystemException;

class FileSystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getCurrentWorkingDirectorySucceeds()
    {
        $fileSystem = new FileSystem;
        self::assertEquals(getcwd(), $fileSystem->getCurrentWorkingDirectory());
    }

    /**
     * @test
     */
    public function fileExistsReturnsTrue()
    {
        $fileSystem = new FileSystem;
        self::assertTrue($fileSystem->fileExists(__FILE__));
    }

    /**
     * @test
     */
    public function fileExistsReturnsFalse()
    {
        $fileSystem = new FileSystem;
        self::assertFalse($fileSystem->fileExists('/foo/bar'));
    }

    /**
     * @test
     */
    public function getRealPathSucceeds()
    {
        $contents   = uniqid();
        $path       = '/tmp/'.uniqid().'.txt';

        $fileSystem = new FileSystem;
        $fileSystem->filePutContents($path, $contents);
        self::assertContains($path, $fileSystem->getRealPath($path));
        unlink($path);
    }

    /**
     * @test
     */
    public function fileGetContentsSucceeds()
    {
        $fileSystem = new FileSystem;
        self::assertEquals('bar', $fileSystem->fileGetContents(__DIR__.'/../../../fixtures/files/foo.txt'));
    }

    /**
     * @test
     * @expectedException Mooti\Framework\Exception\FileSystemException
     */
    public function fileGetContentsThrowsFileSystemException()
    {
        $fileSystem = new FileSystem;
        $fileSystem->fileGetContents('/foo/bar');
    }

    /**
     * @test
     */
    public function filePutContentsSucceeds()
    {
        $contents = uniqid();
        $path     = '/tmp/'.uniqid().'.txt';

        $fileSystem = new FileSystem;
        $fileSystem->filePutContents($path, $contents);
        self::assertEquals($contents, $fileSystem->fileGetContents($path));
        unlink($path);
    }

    /**
     * @test
     * @expectedException Mooti\Framework\Exception\FileSystemException
     * @expectedExceptionMessage File /foo/bar could not be written
     */
    public function filePutContentsThrowsFileSystemException()
    {
        $fileSystem = new FileSystem;
        $fileSystem->filePutContents('/foo/bar', 'test');
    }

    /**
     * @test
     */
    public function createDirectorySucceeds()
    {
        $path = '/tmp/'.uniqid();

        $fileSystem = new FileSystem;
        $fileSystem->createDirectory($path);
        self::assertTrue(is_dir($path));
        rmdir($path);
    }

    /**
     * @test
     * @expectedException Mooti\Framework\Exception\FileSystemException
     * @expectedExceptionMessage Directory /572b679aaf597 could not be created
     */
    public function createDirectoryThrowsFileSystemException()
    {
        $path = '/572b679aaf597';
        $fileSystem = new FileSystem;
        $fileSystem->createDirectory($path);
        rmdir($path);
    }

    /**
     * @test
     */
    public function changeDirectorySucceeds()
    {
        $oldPath = __DIR__;
        $path    = realpath(__DIR__.'/../../../fixtures/files');
        $fileSystem = new FileSystem;
        $fileSystem->changeDirectory($path);
        self::assertEquals($path, $fileSystem->getCurrentWorkingDirectory());
        self::assertNotEquals($oldPath, $fileSystem->getCurrentWorkingDirectory());
    }

    /**
     * @test
     * @expectedException Mooti\Framework\Exception\FileSystemException
     * @expectedExceptionMessage Directory /572b685014d03 does not exist
     */
    public function changeDirectoryThrowsFileSystemException()
    {
        $path = '/572b685014d03';        
        $fileSystem = new FileSystem;
        $fileSystem->changeDirectory($path);
    }

}
