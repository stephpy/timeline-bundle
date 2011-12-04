<?php

namespace Highco\TimelineBundle\Timeline\Spread;

use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Timeline\Spread\Entry\EntryCollection;
use Highco\TimelineBundle\Timeline\Spread\Entry\Entry;

/**
 * Manager
 *
 * @package
 * @version $id$
 * @author Stephane PY <s.py@bleuroy.com>
 */
class Manager
{
    protected $spreads;
    protected $results;
    //@todo set this configurable
    protected $spread_to_me = true;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->spreads = new \ArrayIterator();
        $this->results = new EntryCollection();
    }

    /**
     * add
     *
     * @param InterfaceSpread $spread
     * @access public
     * @return void
     */
    public function add(InterfaceSpread $spread)
    {
        $this->spreads[] = $spread;
    }

    /**
     * process
     *
     * @param TimelineAction $timeline_action
     * @access public
     * @return void
     */
    public function process(TimelineAction $timeline_action)
    {
        if($this->spread_to_me)
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
     * @access public
     * @return void
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * clear
     *
     * @access public
     * @return void
     */
    public function clear()
    {
        $this->results = new EntryCollection();
    }
}
