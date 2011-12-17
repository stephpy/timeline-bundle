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
    public function supports(TimelineAction $timeline_action);
    public function process(TimelineAction $timeline_action, EntryCollection $coll);
}
