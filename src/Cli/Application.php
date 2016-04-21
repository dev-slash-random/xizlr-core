<?php
/**
 *
 * The main CLi Application class
 *
 * @package      Mooti
 * @subpackage   Framework
 * @author       Ken Lalobo <ken@mooti.io>
 */

namespace Mooti\Framework\Cli;

use Symfony\Component\Console\Application as SymfonyApplication;
use Mooti\Framework\AbstractApplication;

class Application extends AbstractApplication
{
    /**
     * @var array
     */
    private $commands;

    /**
     * @param array $commands A list of command classes to run
     */
    public function __construct(array $commands)
    {
        $this->commands = $commands;
    }

    /**
     * Run the application
     *
     */
    public function runApplication()
    {
        $application = $this->createNew(SymfonyApplication::class, $this->name);
        foreach ($this->commands as $command) {
            $application->add($this->createNew($command));
        }        
        $application->run();
    }
}
