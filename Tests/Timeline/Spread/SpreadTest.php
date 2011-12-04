<?php

namespace Highco\TimelineBundle\Tests\Timeline\Spread;

use Highco\TimelineBundle\Timeline\Spread\Manager;
use Highco\TimelineBundle\Tests\Stubs\Timeline\Spread as StubSpread;
use Highco\TimelineBundle\Timeline\Token\Timeline;

class SpreadTest extends \PHPUnit_Framework_TestCase
{
    public function testSupport()
    {
        // manager has only one stub spread
        // @todo testing with a lot of spreads
        $manager = new Manager();
        $manager->add(new StubSpread());

        $manager->process($this->getTimelineHydrated());

        $results = $manager->getResults();
        $this->assertEquals($results, array(
            'mytimeline' => array(
                0 => array(
                    'subject_model' => '\EveryBody',
                    'subject_id'    => 1,
                )
            )
        ));
    }

    public function testNotSupport()
    {
        $stub = new StubSpread();
        $stub->setSupports(false);

        $manager = new Manager();
        $manager->add($stub);

        $manager->process($this->getTimelineHydrated());

        $this->assertEquals($manager->getResults(), array());
    }

    /**
     * getTimelineHydrated
     *
     * @access private
     * @return void
     */
    private function getTimelineHydrated()
    {
        $subject                            = new Timeline();
        $subject->subject_model             = '\ChuckNorris';
        $subject->subject_id                = '1';
        $subject->verb                      = 'own';
        $subject->direct_complement_model   = '\world';
        $subject->direct_complement_id      = '1';
        $subject->indirect_complement_model = '\VicMacKey';
        $subject->indirect_complement_id    = '1';

        return $subject;
    }
}
