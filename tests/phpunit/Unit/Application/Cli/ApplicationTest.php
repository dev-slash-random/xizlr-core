<?php
namespace Mooti\Test\PHPUnit\Framework\Unit\Application\Cli;

use Mooti\Framework\Application\Cli\Application;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\Command;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function runApplicationSucceeds()
    {
        $name = 'testApp';

        $command1 = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command2 = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandNames = [
            'Command1',
            'Command2'
        ];

        $symfonyApplication = $this->getMockBuilder(SymfonyApplication::class)
            ->disableOriginalConstructor()
            ->getMock();

        $symfonyApplication->expects(self::exactly(2))
            ->method('add')
            ->withConsecutive(
                [self::equalTo($command1)],
                [self::equalTo($command2)]
            );

        $symfonyApplication->expects(self::once())
            ->method('run');

        $application = $this->getMockBuilder(Application::class)
            ->setConstructorArgs([$commandNames])
            ->setMethods(['createNew', 'getName'])
            ->getMock();

        $application->expects(self::once())
            ->method('getName')
            ->will(self::returnValue($name));

        $application->expects(self::exactly(3))
            ->method('createNew')
            ->withConsecutive(
                [SymfonyApplication::class, $name],
                [$commandNames[0]],
                [$commandNames[1]]
            )
            ->will(self::onConsecutiveCalls($symfonyApplication, $command1, $command2));

        self::assertNull($application->runApplication());
    }
}
