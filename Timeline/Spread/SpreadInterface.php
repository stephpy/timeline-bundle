<?php

namespace Highco\TimelineBundle\Timeline\Spread;

use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Timeline\Spread\Entry\EntryCollection;

/**
 * How to define a spread
 *
 * @package HighcoTimelineBundle
 * @release 1.0.0
 * @author  Stephane PY <py.stephane1@gmail.com>
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
    function supports(TimelineAction $timelineAction);

    /**
     * @param TimelineAction  $timelineAction TimelineAction we look for spreads
     * @param EntryCollection $coll           Spreads defined on an EntryCollection
     */
    function process(TimelineAction $timelineAction, EntryCollection $coll);
}
