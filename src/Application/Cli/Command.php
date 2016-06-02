<?php
namespace Mooti\Framework\Application\Cli;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mooti\Framework\Framework;
use Mooti\Framework\Util\FileSystem;
use Symfony\Component\Process\Process;
use Mooti\Framework\Exception\FileSystemException;

class Command extends SymfonyCommand
{
    protected $isDefaultCommand = false;

    public function isDefaultCommand()
    {
        return $this->isDefaultCommand;
    }

    public function runShellCommand($command, OutputInterface $output)
    {
        $output->writeln('Run: '.$command);
        $process = $this->createNew(Process::class, $command);
        $process->setTimeout(3600);
        $process->mustRun(function ($type, $buffer) use ($output) {
            $output->writeln(trim($buffer));
        });
    }
}
