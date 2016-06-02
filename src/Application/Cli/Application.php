<?php
/**
 *
 * The main CLi Application class
 *
 * @package      Mooti
 * @subpackage   Framework
 * @author       Ken Lalobo <ken@mooti.io>
 */

namespace Mooti\Framework\Application\Cli;

use Symfony\Component\Console\Application as SymfonyApplication;
use Mooti\Framework\Application\AbstractApplication;

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
        $application = $this->createNew(SymfonyApplication::class, $this->getName());
        foreach ($this->commands as $commandClass) {
            $command = $this->createNew($commandClass);
            $application->add($command);
            if ($command instanceof Command && $command->isDefaultCommand()) {            
                $application->setDefaultCommand($command->getName());
            }
        }        
        $application->run();
    }
}
