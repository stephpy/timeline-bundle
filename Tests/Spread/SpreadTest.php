<?php

namespace Spy\TimelineBundle\Tests\Spread;

use Spy\TimelineBundle\Spread\Manager;
use Spy\TimelineBundle\Tests\Stubs\Spread as StubSpread;
use Spy\TimelineBundle\Model\TimelineAction;

use Spy\TimelineBundle\Spread\Entry\Entry;
use Spy\TimelineBundle\Spread\Entry\EntryCollection;

/**
 * SpreadTest
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class SpreadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testSupport
     */
    public function testSupport()
    {
        // manager has only one stub spread
        // @todo testing on_me, global
        // @todo testing with a lot of spreads
        $manager = new Manager();
        $manager->add(new StubSpread());

        $manager->process($this->getTimelineHydrated());

        $results = $manager->getResults();
        $entries = $results->getEntries();

        $entry = new Entry();
        $entry->subjectModel = "\EveryBody";
        $entry->subjectId = 1;

        $this->assertEquals(count($entries), 2);
        $this->assertTrue(isset($entries['mytimeline']));
        $this->assertTrue(isset($entries['mytimeline'][$entry->getIdent()]));
        $this->assertEquals(array_pop($entries['mytimeline']), $entry);
    }

    /**
     * testNotSupport
     *
     * @access public
     * @return void
     */
    public function testNotSupport()
    {
        $stub = new StubSpread();
        $stub->setSupports(false);

        $manager = new Manager();
        $manager->add($stub);

        $manager->process($this->getTimelineHydrated());

        $this->assertEquals($manager->getResults(), new EntryCollection());
    }

    /**
     * getTimelineHydrated
     *
     * @return TimelineAction
     */
    private function getTimelineHydrated()
    {
        $subject                            = new TimelineAction();
        $subject->setSubjectModel('\ChuckNorris');
        $subject->setSubjectId('1');
        $subject->setVerb('own');
        $subject->setDirectComplementModel('\world');
        $subject->setDirectComplementId('1');
        $subject->setIndirectComplementModel('\VicMacKey');
        $subject->setIndirectComplementId('1');

        return $subject;
    }
}
