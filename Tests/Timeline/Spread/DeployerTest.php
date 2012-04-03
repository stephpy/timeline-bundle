<?php

namespace Highco\TimelineBundle\Tests\Timeline\Spread;

use Highco\TimelineBundle\Timeline\Spread\Deployer;

use Highco\TimelineBundle\Timeline\Spread\Entry\EntryCollection;
use Highco\TimelineBundle\Timeline\Spread\Entry\Entry;

/**
 * DeployerTest
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class DeployerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testDeployNotWantToBePublished
     */
    public function testDeployNotWantToBePublished()
    {
        $spreadManager         = $this->getMock('Highco\TimelineBundle\Timeline\Spread\Manager');
        $timelineActionManager = $this->getMock('Highco\TimelineBundle\Entity\TimelineActionManager', array(), array(), '', false);
        $provider              = $this->getMock('Highco\TimelineBundle\Timeline\Provider\Redis', array(), array(), '', false);
        $notificationManager   = $this->getMock('Highco\TimelineBundle\Timeline\Notification\NotificationManager');

        $spreadManager->expects($this->never())->method('process');
        $spreadManager->expects($this->never())->method('getResults');
        $spreadManager->expects($this->never())->method('clear');

        $provider->expects($this->never())->method('persist');
        $provider->expects($this->never())->method('flush');

        $timelineActionManager->expects($this->never())->method('updateTimelineAction');

        $notificationManager->expects($this->never())->method('notify');

        $ta = $this->getMock('Highco\TimelineBundle\Entity\TimelineAction');
        $ta->expects($this->once())
            ->method('getStatusWanted')
            ->will($this->returnValue('stollen'));

        $ta->expects($this->never())->method('setStatusCurrent');
        $ta->expects($this->never())->method('setStatusWanted');

        $deployer = new Deployer($spreadManager, $timelineActionManager, $provider, $notificationManager);
        $deployer->deploy($ta);
    }

    /**
     * testDeploy
     */
    public function testDeploy()
    {
        /* --- define entry colletion --- */
        $entryCollection = new EntryCollection();
        $entry = new Entry();
        $entry->subjectModel = "ChuckNorris";
        $entry->subjectId = 9999999999;

        $entryCollection->set('GLOBAL', $entry);
        $entryCollection->set('CONTEXT', $entry);

        $entry = new Entry();
        $entry->subjectModel = "VicMcKey";
        $entry->subjectId = 13;

        $entryCollection->set('GLOBAL', $entry);
        /* ---- end define ---- */

        $spreadManager         = $this->getMock('Highco\TimelineBundle\Timeline\Spread\Manager');
        $timelineActionManager = $this->getMock('Highco\TimelineBundle\Entity\TimelineActionManager', array(), array(), '', false);
        $provider              = $this->getMock('Highco\TimelineBundle\Timeline\Provider\Redis', array(), array(), '', false);
        $notificationManager   = $this->getMock('Highco\TimelineBundle\Timeline\Notification\NotificationManager');

        $ta = $this->getMock('Highco\TimelineBundle\Entity\TimelineAction');
        $ta->expects($this->once())
            ->method('getStatusWanted')
            ->will($this->returnValue('published'));

        $spreadManager->expects($this->once())
            ->method('process')
            ->with($this->equalTo($ta));

        $spreadManager->expects($this->once())
            ->method('getResults')
            ->will($this->returnValue($entryCollection));

        /* ---- test persists --- */
        $provider->expects($this->at(0))
            ->method('persist')
            ->with($this->equalTo($ta), $this->equalTo('GLOBAL'), $this->equalTo('ChuckNorris'), $this->equalTo('9999999999'));
        $provider->expects($this->at(1))
            ->method('persist')
            ->with($this->equalTo($ta), $this->equalTo('GLOBAL'), $this->equalTo('VicMcKey'), $this->equalTo('13'));
        $provider->expects($this->at(2))
            ->method('persist')
            ->with($this->equalTo($ta), $this->equalTo('CONTEXT'), $this->equalTo('ChuckNorris'), $this->equalTo('9999999999'));
        /* ---- test notify --- */
        $notificationManager->expects($this->at(0))
            ->method('notify')
            ->with($this->equalTo($ta), $this->equalTo('GLOBAL'), $this->equalTo('ChuckNorris'), $this->equalTo('9999999999'));
        $notificationManager->expects($this->at(1))
            ->method('notify')
            ->with($this->equalTo($ta), $this->equalTo('GLOBAL'), $this->equalTo('VicMcKey'), $this->equalTo('13'));
        $notificationManager->expects($this->at(2))
            ->method('notify')
            ->with($this->equalTo($ta), $this->equalTo('CONTEXT'), $this->equalTo('ChuckNorris'), $this->equalTo('9999999999'));

        $provider->expects($this->once())->method('flush');

        $ta->expects($this->once())
            ->method('setStatusCurrent')
            ->with($this->equalTo(\Highco\TimelineBundle\Model\TimelineAction::STATUS_PUBLISHED));

        $ta->expects($this->once())
            ->method('setStatusWanted')
            ->with($this->equalTo(\Highco\TimelineBundle\Model\TimelineAction::STATUS_FROZEN));

        $timelineActionManager->expects($this->once())
            ->method('updateTimelineAction')
            ->with($this->equalTo($ta));

        $spreadManager->expects($this->once())
            ->method('clear');

        $deployer = new Deployer($spreadManager, $timelineActionManager, $provider, $notificationManager);
        $deployer->deploy($ta);
    }
}
