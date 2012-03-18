<?php

namespace Highco\TimelineBundle\Timeline\Spread;

use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Timeline\Spread\Entry\EntryCollection;
use Highco\TimelineBundle\Timeline\Spread\Entry\Entry;

/**
 * Manager spread, retrieve with tags
 *
 * @package HighcoTimelineBundle
 * @release 1.0.0
 * @author  Stephane PY <py.stephane1@gmail.com>
 */
class Manager
{
    /**
     * @var \ArrayIterator
     */
    protected $spreads;

    /**
     * @var EntryCollection
     */
    protected $results;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->spreads = new \ArrayIterator();
        $this->options = $options;
        $this->results = new EntryCollection(isset($options['onGlobalContext']) ? $options['onGlobalContext'] : true);
    }

    /**
     * @param SpreadInterface $spread
     */
    public function add(SpreadInterface $spread)
    {
        $this->spreads[] = $spread;
    }

    /**
     * @param TimelineAction $timelineAction
     */
    public function process(TimelineAction $timelineAction)
    {
        // can be defined on config.yml
        if (isset($this->options['onMe']) && $this->options['onMe']) {
            $entry = new Entry();
            $entry->subjectModel = $timelineAction->getSubjectModel();
            $entry->subjectId    = $timelineAction->getSubjectId();

            $this->results->set('GLOBAL', $entry);
        }

        foreach ($this->spreads as $spread) {
            if ($spread->supports($timelineAction)) {
                $spread->process($timelineAction, $this->results);
            }
        }
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Clears the results of manager.
     */
    public function clear()
    {
        $this->results = new EntryCollection();
    }
}
