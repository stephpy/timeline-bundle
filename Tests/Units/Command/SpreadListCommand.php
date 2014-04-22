<?php

namespace Spy\TimelineBundle\Tests\Units\Command;

require_once __DIR__."/../../../vendor/autoload.php";

use atoum\AtoumBundle\Test\Units\Test;
use Spy\TimelineBundle\Command\SpreadListCommand as TestedCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;

class SpreadListCommand extends Test
{
    public function beforeTestMethod($method)
    {
        define('STDIN',fopen("php://stdin","r"));
    }

    public function testExecute()
    {
        $this->mockClass('Symfony\Component\DependencyInjection\ContainerInterface', '\Mock');
        $this->mockGenerator()->orphanize('__construct');
        $this->mockClass('Spy\Timeline\Spread\Deployer', '\Mock');

        $deployer      = new \Mock\Deployer();
        $deployer->getMockController()->getSpreads = array();

        $container = new \Mock\ContainerInterface();
        $container->getMockController()->get = function($v) use ($deployer) {
            if ($v == 'spy_timeline.spread.deployer') {
                return $deployer;
            }
        };

        $command = new TestedCommand();
        $command->setContainer($container);

        $application = new Application();
        $application->add($command);

        $command = $application->find('spy_timeline:spreads');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()), array());

        $this->string($commandTester->getDisplay())
            ->isEqualTo('There is 0 timeline spread(s) defined'.chr(10));

        // one spread
        $this->mockClass('Spy\TimelineBundle\Spread\SpreadInterface', '\Mock');

        $spread = new \Mock\SpreadInterface();
        $deployer->getMockController()->getSpreads = array($spread);

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()), array());

        $this->string($commandTester->getDisplay())
            ->isEqualTo('There is 1 timeline spread(s) defined'.chr(10).'- Mock\SpreadInterface'.chr(10));
    }
}
