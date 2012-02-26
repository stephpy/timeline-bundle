<?php

namespace Highco\TimelineBundle\Tests\Command;

use Highco\TimelineBundle\Tests\AbstractDoctrineConnection;
use Doctrine\ORM\Tools\SchemaTool;
use Highco\TimelineBundle\Entity\TimelineAction;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Highco\TimelineBundle\Command\DeployTimelineActionCommand;

class DeployTimelineActionCommandTest extends AbstractDoctrineConnection
{
    public function setUp()
    {
        parent::setUp();

        $this->redis = $this->container->get('snc_redis.test_client');

        $this->container->get('highco.timeline.provider.redis')
            ->setRedis($this->redis);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->redis->flushDb();
    }

    /*public function testNoTimeline()
    {
        $results = $this->em->getRepository('HighcoTimelineBundle:TimelineAction')
            ->createQueryBuilder('t')
            ->getQuery()
            ->getResult();

        $this->assertEquals(count($results), 0);

        $application = new Application($this->kernel);
        $application->add(new DeployTimelineActionCommand());

        $command = $application->find('highco:timeline-deploy');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()), array());

        $display = $commandTester->getDisplay();

        $result  = "There is 0 timeline action(s) to deploy\n";
        $result .= "Done\n";

        $this->assertEquals($display, $result);
    }

    public function testOneTimeline()
    {
        $this->createTimelineAction(1);

        $results = $this->em->getRepository('HighcoTimelineBundle:TimelineAction')
            ->createQueryBuilder('t')
            ->getQuery()
            ->getResult();

        $this->assertEquals(count($results), 1);
        foreach($results as $result)
        {
            $this->assertEquals($result->getStatusWanted(), TimelineAction::STATUS_PUBLISHED);
            $this->assertEquals($result->getStatusCurrent(), TimelineAction::STATUS_PENDING);
        }

        $application = new Application($this->kernel);
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
        foreach($results as $result)
        {
            $this->assertEquals($result->getStatusWanted(), TimelineAction::STATUS_FROZEN);
            $this->assertEquals($result->getStatusCurrent(), TimelineAction::STATUS_PUBLISHED);
        }
    }*/

    /**
     * testTwoTimelineWithLimit
     *
     * @access public
     * @return void
     */
    public function testTwoTimelineWithLimit()
    {
        $this->createTimelineAction(2);

        $results = $this->em->getRepository('HighcoTimelineBundle:TimelineAction')
            ->createQueryBuilder('t')
            ->getQuery()
            ->getResult();

        $this->assertEquals(count($results), 2);
        foreach($results as $result)
        {
            $this->assertEquals($result->getStatusWanted(), TimelineAction::STATUS_PUBLISHED);
            $this->assertEquals($result->getStatusCurrent(), TimelineAction::STATUS_PENDING);
        }

        $application = new Application($this->kernel);
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
        for($i = 0; $i < (int) $howMany; $i++)
        {
            $entry = new TimelineAction();
            $entry->setSubjectModel('\MYTEST');
            $entry->setSubjectId(1);
            $entry->setVerb('Own');
            $entry->setDirectComplementModel('\World');
            $entry->setDirectComplementId(1);
            $entry->setIndirectComplementModel('\VicMcKey');
            $entry->setIndirectComplementId(1);

            $this->em->persist($entry);
        }

        $this->em->flush();
    }


}
