<?php

namespace Highco\TimelineBundle\Timeline\Spread;

use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Timeline\Spread\Entry\EntryCollection;

/**
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface InterfaceSpread
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
     * @param TimelineAction  $timelineAction
     * @param EntryCollection $coll
     */
    function process(TimelineAction $timelineAction, EntryCollection $coll);
}
