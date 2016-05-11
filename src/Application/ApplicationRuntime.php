<?php
/**
 * Runtime
 *
 * The application runtime. Contains information about the currently running application
 *
 * @package      Mooti
 * @subpackage   Framework     
 * @author       Ken Lalobo <ken@mooti.io>
 */ 

namespace Mooti\Framework\Application;

use Mooti\Framework\Framework;
use Mooti\Framework\Util\FileSystem;
use Mooti\Framework\Exception\FileSystemException;

class ApplicationRuntime
{
    use Framework;

    /** @var string The root directory of the application */
    protected $rootDirectory;

    /** @var string The name of the application */
    protected $name;

    /**
     * Attempts to find the root directory
     *
     * @return string The application root directory
     */
    public function locateRootDirectory()
    {
        $vendorDirectoryName = 'vendor';
        $fileDirectory    = __DIR__;
        $searchDirectories = [
            __DIR__.'/..',
            __DIR__.'/../..',
            __DIR__.'/../../..',
            __DIR__.'/../../../..',
            __DIR__.'/../../../../..'
        ];

        $fileSystem = $this->createNew(FileSystem::class);
        foreach ($searchDirectories as $searchDirectory) {
            if ($fileSystem->fileExists($searchDirectory . '/' . $vendorDirectoryName)) {
                return $fileSystem->getRealPath($searchDirectory);
            }
        }

        throw new FileSystemException('No application root directory found');
    }

    /**
     * Get the root directory
     *
     * @return string The application root directory
     */
    public function getRootDirectory()
    {
        if(isset($this->rootDirectory) == false) {
            $this->rootDirectory = $this->locateRootDirectory();
        }
        return $this->rootDirectory;
    }

    /**
     * Get the application name
     *
     * @return string The application name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the application name
     *
     * @param string $name The application name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}

