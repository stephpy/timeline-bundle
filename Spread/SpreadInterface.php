<?php

namespace Spy\TimelineBundle\Spread;

use Spy\TimelineBundle\Model\TimelineAction;
use Spy\TimelineBundle\Spread\Entry\EntryCollection;

/**
 * How to define a spread
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface SpreadInterface
{
    /**
     * You spread class is support the timeline action ?
     *
     * @param TimelineAction $timelineAction
     *
     * @return boolean
     */
    public function supports(TimelineAction $timelineAction);

    /**
     * @param  TimelineAction  $timelineAction TimelineAction we look for spreads
     * @param  EntryCollection $coll           Spreads defined on an EntryCollection
     * @return void
     */
    public function process(TimelineAction $timelineAction, EntryCollection $coll);
}
