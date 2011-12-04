<?php

namespace Highco\TimelineBundle\Timeline\Spread;

use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Timeline\Spread\Entry\EntryCollection;

interface InterfaceSpread
{
    public function supports(TimelineAction $timeline_action);
    public function process(TimelineAction $timeline_action, EntryCollection $coll);
}
