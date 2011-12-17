<?php

namespace Highco\TimelineBundle\Timeline\Spread;

use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Timeline\Spread\Entry\EntryCollection;
use Highco\TimelineBundle\Timeline\Spread\Entry\Entry;

/**
 * Manager
 *
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Manager
{
    protected $spreads;
    protected $results;
    protected $options;

    /**
     * __construct
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->spreads = new \ArrayIterator();
        $this->options = $options;
        $this->results = new EntryCollection($options['on_global_context']);
    }

    /**
     * add
     *
     * @param InterfaceSpread $spread
     */
    public function add(InterfaceSpread $spread)
    {
        $this->spreads[] = $spread;
    }

    /**
     * process
     *
     * @param TimelineAction $timeline_action
     */
    public function process(TimelineAction $timeline_action)
    {
        // can be defined on config.yml
        if($this->options['on_me'])
        {
            $entry = new Entry();
            $entry->subject_model = $timeline_action->getSubjectModel();
            $entry->subject_id    = $timeline_action->getSubjectId();

            $this->results->set('GLOBAL', $entry);
        }

        foreach($this->spreads as $spread)
        {
            if($spread->supports($timeline_action))
            {
                $spread->process($timeline_action, $this->results);
            }
        }
    }

    /**
     * getResults
     *
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * clear the results of manager
     */
    public function clear()
    {
        $this->results = new EntryCollection();
    }
}
