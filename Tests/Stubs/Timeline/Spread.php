<?php

namespace Highco\TimelineBundle\Tests\Stubs\Timeline;

use Highco\TimelineBundle\Timeline\Spread\SpreadInterface;

use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Timeline\Spread\Entry\EntryCollection;
use Highco\TimelineBundle\Timeline\Spread\Entry\Entry;

class Spread implements SpreadInterface
{
    protected $supports = true;

    public function supports(TimelineAction $timelineAction)
    {
        return $this->supports;
    }

    public function setSupports($v)
    {
        $this->supports = (bool) $v;
    }

    public function process(TimelineAction $timelineAction, EntryCollection $coll)
    {
        $entry = new Entry();
        $entry->subjectModel = "\EveryBody";
        $entry->subjectId = 1;

        $coll->set('mytimeline', $entry);
    }
}
