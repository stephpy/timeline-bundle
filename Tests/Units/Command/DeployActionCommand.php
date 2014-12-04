<?php

namespace Spy\TimelineBundle\Tests\Units\Command;

use Spy\TimelineBundle\Command\DeployActionCommand as TestedCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use mageekguy\atoum;

class DeployActionCommand extends atoum\test
{
    public function beforeTestMethod($method)
    {
        define('STDIN', fopen("php://stdin", "r"));
    }

    public function testNoTimeline()
    {
        $actionManager = new \mock\Spy\Timeline\Driver\ActionManagerInterface();
        $this->mockGenerator()->orphanize('__construct');
        $deployer      = new \mock\Spy\Timeline\Spread\Deployer();

        $actionManager->getMockController()->findActionsWithStatusWantedPublished = array();

        $container = new \mock\Symfony\Component\DependencyInjection\ContainerInterface();
        $container->getMockController()->get = function ($v) use ($actionManager, $deployer) {
            if ($v == 'spy_timeline.action_manager') {
                return $actionManager;
            } elseif ($v == 'spy_timeline.spread.deployer') {
                return $deployer;
            }
        };

        $command = new TestedCommand();
        $command->setContainer($container);

        $application = new Application();
        $application->add($command);

        $command = $application->find('spy_timeline:deploy');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()), array());

        $this->mock($actionManager)
            ->call('findActionsWithStatusWantedPublished')
            ->once();

        $this->string($commandTester->getDisplay())
            ->isEqualTo('There is 0 action(s) to deploy'.chr(10).'Done'.chr(10));
    }

    public function testOneTimeline()
    {
        $actionManager = new \mock\Spy\Timeline\Driver\ActionManagerInterface();
        $this->mockGenerator()->orphanize('__construct');
        $deployer      = new \mock\Spy\Timeline\Spread\Deployer();
        $action        = new \mock\Spy\Timeline\Model\ActionInterface();

        $action->getMockController()->getId = 1;
        $actionManager->getMockController()->findActionsWithStatusWantedPublished = array($action);

        $container = new \mock\Symfony\Component\DependencyInjection\ContainerInterface();
        $container->getMockController()->get = function ($v) use ($actionManager, $deployer) {
            if ($v == 'spy_timeline.action_manager') {
                return $actionManager;
            } elseif ($v == 'spy_timeline.spread.deployer') {
                return $deployer;
            }
        };

        $command = new TestedCommand();
        $command->setContainer($container);

        $application = new Application();
        $application->add($command);

        $command = $application->find('spy_timeline:deploy');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()), array());

        $this->mock($actionManager)
            ->call('findActionsWithStatusWantedPublished')
            ->once();

        $this->string($commandTester->getDisplay())
            ->isEqualTo('There is 1 action(s) to deploy'.chr(10).'Deploy action 1'.chr(10).'Done'.chr(10));
    }
}
