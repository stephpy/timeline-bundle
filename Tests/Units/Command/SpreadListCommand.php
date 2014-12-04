<?php

namespace Spy\TimelineBundle\Tests\Units\Command;

use mageekguy\atoum;
use Spy\TimelineBundle\Command\SpreadListCommand as TestedCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;

class SpreadListCommand extends  atoum\test
{
    public function beforeTestMethod($method)
    {
        define('STDIN', fopen("php://stdin", "r"));
    }

    public function testExecute()
    {
        $this->mockGenerator()->orphanize('__construct');
        $deployer      = new \mock\Spy\Timeline\Spread\Deployer();
        $deployer->getMockController()->getSpreads = array();

        $container = new \mock\Symfony\Component\DependencyInjection\ContainerInterface();
        $container->getMockController()->get = function ($v) use ($deployer) {
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
        $spread = new \mock\Spy\TimelineBundle\Spread\SpreadInterface();
        $deployer->getMockController()->getSpreads = array($spread);

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()), array());

        $this->string($commandTester->getDisplay())
            ->isEqualTo('There is 1 timeline spread(s) defined'.chr(10).'- mock\Spy\TimelineBundle\Spread\SpreadInterface'.chr(10));
    }
}
