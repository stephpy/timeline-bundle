<?php

namespace Highco\TimelineBundle\Spread;

use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Spread\Entry\EntryCollection;

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
     * @param TimelineAction  $timelineAction TimelineAction we look for spreads
     * @param EntryCollection $coll           Spreads defined on an EntryCollection
     */
    public function process(TimelineAction $timelineAction, EntryCollection $coll);
}
