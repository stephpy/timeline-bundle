<?php

namespace Highco\TimelineBundle\Tests\Timeline\Spread;

use Highco\TimelineBundle\Timeline\Spread\Manager;
use Highco\TimelineBundle\Tests\Stubs\Timeline\Spread as StubSpread;
use Highco\TimelineBundle\Model\TimelineAction;

use Highco\TimelineBundle\Timeline\Spread\Entry\Entry;
use Highco\TimelineBundle\Timeline\Spread\Entry\EntryCollection;

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
        $entries = $results->getEntries();

        $entry = new Entry();
        $entry->subject_model = "\EveryBody";
        $entry->subject_id = 1;

        $this->assertEquals(count($entries), 1);
        $this->assertTrue(isset($entries['mytimeline']));
        $this->assertTrue(isset($entries['mytimeline'][$entry->getIdent()]));
        $this->assertEquals(array_pop($entries['mytimeline']), $entry);
    }

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
     * @return void
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
