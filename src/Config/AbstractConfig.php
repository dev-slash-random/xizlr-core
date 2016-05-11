<?php
/**
 * Config
 *
 * Abstract config class
 *
 * @package      Mooti
 * @subpackage   Framework     
 * @author       Ken Lalobo <ken@mooti.io>
 */

namespace Mooti\Framework\Config;
    
use Mooti\Framework\Util\FileSystem;
use Mooti\Framework\Framework;
use Mooti\Framework\Exception\DataValidationException;
use Mooti\Framework\Exception\MalformedDataException;
use Mooti\Validator\Validator;

abstract class AbstractConfig
{
    use Framework;

    /**
     * @var array $filename The filename of the config
     */
    protected $filename;

    /**
     * @var string $dirPath The directory path
     */
    protected $dirPath;

    /**
     * @var array $rules The config rules
     */
    protected $rules = [];

    /**
     * @var array $configData The config data
     */
    protected $configData = [];

    /**
     * This is used to initialise the config data with default values
     *
     * @param string $filename The filename of the config
     *
     */
    abstract public function init();

    /**
     * Sets the confogs filename
     *
     * @param string $filename The filename of the config
     *
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Set the directory the config file is in
     *
     * @param string $dirPath The directory path
     *
     */
    public function setDirPath($dirPath)
    {
        $this->dirPath = $dirPath;
    }

    /**
     * Set the config rules
     *
     * @param array $rules The config rules
     *
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Set the config data
     *
     * @param array $configData The config data
     *
     */
    public function setConfigData(array $configData)
    {
        $this->configData = $configData;
    }

    /**
     * Get the config data
     *
     * @return array The config data
     *
     */
    public function getConfigData()
    {
        return $this->configData;
    }

    /**
     * Get the full file path of the config file
     *
     * @return string The full file path
     *
     */
    public function getFilepath()
    {
        if (isset($this->dirPath)) {
            $filepath = $this->dirPath .'/'.$this->filename;
        } else {
            $fileSystem = $this->createNew(FileSystem::class);
            $filepath   = $fileSystem->getCurrentWorkingDirectory() .'/'.$this->filename;    
        }
        return $filepath;        
    }    

    /**
     * Validate some config data
     *
     * @param array $rules      The config rules
     * @param array $configData The config data
     *
     * @throws Mooti\Framework\Exception\DataValidationException
     */
    public function validateConfig(array $rules, array $configData)
    {
        $validator = $this->createNew(Validator::class);
        
        if ($validator->isValid($rules, $configData) == false) {
            throw new DataValidationException('The config is invalid: ' . print_r($validator->getErrors(), 1));
        }
    }

    /**
     * Open the config file and populate the config data
     *
     * @throws Mooti\Framework\Exception\MalformedDataException
     */
    public function open()
    {
        $fileSystem = $this->createNew(FileSystem::class);

        $filepath = $this->getFilepath();

        $contents = $fileSystem->fileGetContents($filepath);

        $this->configData = json_decode($contents, true);

        if (isset($this->configData) == false) {
            throw new MalformedDataException('The contents of the file "'.$filepath.'" are not valid json');
        }
        $this->validateConfig($this->rules, $this->configData);
    }

    /**
     * Save the config file based on the config data
     *
     */
    public function save()
    {
        $filepath = $this->getFilepath();
        $this->validateConfig($this->rules, $this->configData);
        $fileSystem = $this->createNew(FileSystem::class);
        $fileSystem->filePutContents($filepath, json_encode($this->configData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
