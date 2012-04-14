<?php

namespace Highco\TimelineBundle\Tests\Stubs;

use Highco\TimelineBundle\Spread\SpreadInterface;

use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Spread\Entry\EntryCollection;
use Highco\TimelineBundle\Spread\Entry\Entry;

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
