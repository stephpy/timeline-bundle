<?php

namespace Highco\TimelineBundle\Timeline\Spread;

use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Timeline\Spread\Entry\EntryCollection;

/**
 * Spread
 *
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface InterfaceSpread
{
    /**
     * supports
     *
     * You spread class is support the timeline action ?
     *
     * @param TimelineAction $timeline_action
     * @return boolean
     */
    public function supports(TimelineAction $timeline_action);

    /**
     * process
     *
     * @param TimelineAction $timeline_action
     * @param EntryCollection $coll
     */
    public function process(TimelineAction $timeline_action, EntryCollection $coll);
}
