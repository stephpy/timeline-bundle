<?php

namespace Spy\TimelineBundle\Tests\Stubs;

use Spy\TimelineBundle\Spread\SpreadInterface;

use Spy\TimelineBundle\Model\TimelineAction;
use Spy\TimelineBundle\Spread\Entry\EntryCollection;
use Spy\TimelineBundle\Spread\Entry\Entry;

/**
 * Spread
 *
 * @uses SpreadInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Spread implements SpreadInterface
{
    /**
     * @var boolean
     */
    protected $supports = true;

    /**
     * @param TimelineAction $timelineAction
     *
     * @return boolean
     */
    public function supports(TimelineAction $timelineAction)
    {
        return $this->supports;
    }

    /**
     * @param boolean $v
     */
    public function setSupports($v)
    {
        $this->supports = (bool) $v;
    }

    /**
     * {@inheritDoc}
     */
    public function process(TimelineAction $timelineAction, EntryCollection $coll)
    {
        $entry = new Entry();
        $entry->subjectModel = "\EveryBody";
        $entry->subjectId = 1;

        $coll->set('mytimeline', $entry);
    }
}
