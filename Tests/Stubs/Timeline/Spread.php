<?php

namespace Highco\TimelineBundle\Tests\Stubs\Timeline;

use Highco\TimelineBundle\Timeline\Spread\InterfaceSpread;

use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Timeline\Spread\Entry\EntryCollection;
use Highco\TimelineBundle\Timeline\Spread\Entry\Entry;

class Spread implements InterfaceSpread
{
    protected $supports = true;

    public function supports(TimelineAction $timeline_action)
    {
        return $this->supports;
    }

    public function setSupports($v)
    {
        $this->supports = (bool) $v;
    }

    public function process(TimelineAction $timeline_action, EntryCollection $coll)
    {
        $entry = new Entry();
        $entry->subject_model = "\EveryBody";
        $entry->subject_id = 1;

        $coll->set('mytimeline', $entry);
    }
}
