<?php

namespace Spy\TimelineBundle\Tests\Units\Command;

require_once __DIR__."/../../../vendor/autoload.php";

use atoum\AtoumBundle\Test\Units\Test;
use Spy\TimelineBundle\Command\DeployActionCommand as TestedCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;

class DeployActionCommand extends Test
{
    public function beforeTestMethod($method)
    {
        define('STDIN',fopen("php://stdin","r"));
    }

    public function testNoTimeline()
    {
        $this->mockClass('Symfony\Component\DependencyInjection\ContainerInterface', '\Mock');
        $this->mockClass('Spy\Timeline\Driver\ActionManagerInterface', '\Mock');
        $this->mockGenerator()->orphanize('__construct');
        $this->mockClass('Spy\Timeline\Spread\Deployer', '\Mock');

        $actionManager = new \Mock\ActionManagerInterface();
        $deployer      = new \Mock\Deployer();

        $actionManager->getMockController()->findActionsWithStatusWantedPublished = array();

        $container = new \Mock\ContainerInterface();
        $container->getMockController()->get = function($v) use ($actionManager, $deployer) {
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
        $this->mockClass('Symfony\Component\DependencyInjection\ContainerInterface', '\Mock');
        $this->mockClass('Spy\Timeline\Driver\ActionManagerInterface', '\Mock');
        $this->mockClass('Spy\Timeline\Model\ActionInterface', '\Mock');
        $this->mockGenerator()->orphanize('__construct');
        $this->mockClass('Spy\Timeline\Spread\Deployer', '\Mock');

        $action        = new \Mock\ActionInterface();
        $actionManager = new \Mock\ActionManagerInterface();
        $deployer      = new \Mock\Deployer();

        $action->getMockController()->getId = 1;
        $actionManager->getMockController()->findActionsWithStatusWantedPublished = array($action);

        $container = new \Mock\ContainerInterface();
        $container->getMockController()->get = function($v) use ($actionManager, $deployer) {
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
