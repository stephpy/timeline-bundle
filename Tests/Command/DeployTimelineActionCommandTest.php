<?php

namespace Spy\TimelineBundle\Tests\Command;

use Spy\TimelineBundle\Entity\TimelineAction;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;
use Spy\TimelineBundle\Command\DeployTimelineActionCommand;
use Symfony\Component\DependencyInjection\Container;

/**
 * DeployTimelineActionCommandTest
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class DeployTimelineActionCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testNoTimeline()
    {
        $manager = $this->getMock('\Spy\TimelineBundle\Entity\TimelineActionManager', array(), array(), '', false);
        $manager->expects($this->once())
            ->method('getTimelineWithStatusPublished')
            ->will($this->returnValue(array()));

        $deployer = $this->getMock('\Spy\TimelineBundle\Spread\Deployer', array(), array(), '', false);

        $container = new Container();
        $container->set('spy_timeline.timeline_action_manager', $manager);
        $container->set('spy_timeline.spread.deployer', $deployer);

        $command = new DeployTimelineActionCommand();
        $command->setContainer($container);

        $application = new Application();
        $application->add($command);

        $command = $application->find('spy_timeline:deploy');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()), array());

        $display = $commandTester->getDisplay();

        $result  = "There is 0 timeline action(s) to deploy\n";
        $result .= "Done\n";

        $this->assertEquals($display, $result);
    }

    public function testOneTimeline()
    {
        $timelineAction = $this->getMock('\Spy\TimelineBundle\Entity\TimelineAction');
        $timelineAction->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $manager = $this->getMock('\Spy\TimelineBundle\Entity\TimelineActionManager', array(), array(), '', false);
        $manager->expects($this->once())
            ->method('getTimelineWithStatusPublished')
            ->will($this->returnValue(array($timelineAction)));

        $deployer = $this->getMock('\Spy\TimelineBundle\Spread\Deployer', array(), array(), '', false);

        $container = new Container();
        $container->set('spy_timeline.timeline_action_manager', $manager);
        $container->set('spy_timeline.spread.deployer', $deployer);

        $command = new DeployTimelineActionCommand();
        $command->setContainer($container);

        $application = new Application();
        $application->add($command);

        $command = $application->find('spy_timeline:deploy');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()), array());

        $display = $commandTester->getDisplay();

        $result  = "There is 1 timeline action(s) to deploy\n";
        $result .= "Deploy timeline action 1\n";
        $result .= "Done\n";

        $this->assertEquals($display, $result);
    }
}
