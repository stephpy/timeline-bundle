<?php

namespace Highco\TimelineBundle\Tests\Command;

use Highco\TimelineBundle\Entity\TimelineAction;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Highco\TimelineBundle\Command\DeployTimelineActionCommand;

class DeployTimelineActionCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testNoTimeline()
    {
        //@todo use mock instead of use real connection !
        /*$results = $this->em->getRepository('HighcoTimelineBundle:TimelineAction')
            ->createQueryBuilder('t')
            ->getQuery()
            ->getResult();

        $this->assertEquals(count($results), 0);

        $application = new Application($this->client->getKernel());
        $application->add(new DeployTimelineActionCommand());

        $command = $application->find('highco:timeline-deploy');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()), array());

        $display = $commandTester->getDisplay();

        $result  = "There is 0 timeline action(s) to deploy\n";
        $result .= "Done\n";

        $this->assertEquals($display, $result);*/
    }

    public function testOneTimeline()
    {
        //@todo use mock instead of use real connection !
        /*$this->createTimelineAction(1);

        $results = $this->em->getRepository('HighcoTimelineBundle:TimelineAction')
            ->createQueryBuilder('t')
            ->getQuery()
            ->getResult();

        $this->assertEquals(count($results), 1);
        foreach ($results as $result)
        {
            $this->assertEquals($result->getStatusWanted(), TimelineAction::STATUS_PUBLISHED);
            $this->assertEquals($result->getStatusCurrent(), TimelineAction::STATUS_PENDING);
        }

        $application = new Application($this->client->getKernel());
        $application->add(new DeployTimelineActionCommand());

        $command = $application->find('highco:timeline-deploy');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()), array());

        $display = $commandTester->getDisplay();

        $result  = "There is 1 timeline action(s) to deploy\n";
        $result .= "Deploy timeline action 1\n";
        $result .= "Done\n";

        $this->assertEquals($display, $result);

        $results = $this->em->getRepository('HighcoTimelineBundle:TimelineAction')
            ->createQueryBuilder('t')
            ->getQuery()
            ->getResult();

        $this->assertEquals(count($results), 1);
        foreach ($results as $result)
        {
            $this->assertEquals($result->getStatusWanted(), TimelineAction::STATUS_FROZEN);
            $this->assertEquals($result->getStatusCurrent(), TimelineAction::STATUS_PUBLISHED);
        }*/
    }

    public function testTwoTimelineWithLimit()
    {
        //@todo use mock instead of use real connection !
        /*$this->createTimelineAction(2);

        $results = $this->em->getRepository('HighcoTimelineBundle:TimelineAction')
            ->createQueryBuilder('t')
            ->getQuery()
            ->getResult();

        $this->assertEquals(count($results), 2);
        foreach ($results as $result)
        {
            $this->assertEquals($result->getStatusWanted(), TimelineAction::STATUS_PUBLISHED);
            $this->assertEquals($result->getStatusCurrent(), TimelineAction::STATUS_PENDING);
        }

        $application = new Application($this->client->getKernel());
        $application->add(new DeployTimelineActionCommand());

        $command = $application->find('highco:timeline-deploy');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '--limit' => 1));

        $display = $commandTester->getDisplay();

        $result  = "There is 1 timeline action(s) to deploy\n";
        $result .= "Deploy timeline action 1\n";
        $result .= "Done\n";

        $this->assertEquals($display, $result);

        $results = $this->em->getRepository('HighcoTimelineBundle:TimelineAction')
            ->createQueryBuilder('t')
            ->getQuery()
            ->getResult();

        $this->assertEquals(count($results), 2);
        $first = array_shift($results);
        $this->assertEquals($first->getStatusWanted(), TimelineAction::STATUS_FROZEN);
        $this->assertEquals($first->getStatusCurrent(), TimelineAction::STATUS_PUBLISHED);

        $second = array_shift($results);
        $this->assertEquals($second->getStatusWanted(), TimelineAction::STATUS_PUBLISHED);
        $this->assertEquals($second->getStatusCurrent(), TimelineAction::STATUS_PENDING);
         */
    }

    /**
     * createTimelineAction
     *
     * @param int $howMany
     * @access protected
     * @return void
     */
    protected function createTimelineAction($howMany = 1)
    {
        //@todo use mock instead of use real connection !
        /*for($i = 0; $i < (int) $howMany; $i++)
        {
            $stub = $this->getMock('Highco\TimelineBundle\Tests\Command\EntityStub');
            $stub->expects($this->once())
                ->method('getId')
                ->will($this->returnValue(1));

            $entry = new TimelineAction();
            $entry->setSubject($stub);

            $entry->setVerb('Own');

            $stub = $this->getMock('Highco\TimelineBundle\Tests\Command\EntityStub');
            $stub->expects($this->once())
                ->method('getId')
                ->will($this->returnValue(2));

            $entry->setDirectComplement($stub);

            $stub = $this->getMock('Highco\TimelineBundle\Tests\Command\EntityStub');
            $stub->expects($this->once())
                ->method('getId')
                ->will($this->returnValue(3));
            $entry->setIndirectComplement($stub);

            $this->em->persist($entry);
        }

        $this->em->flush();*/
    }
}
