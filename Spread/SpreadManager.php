<?php

namespace Spy\TimelineBundle\Spread;

use Spy\TimelineBundle\Model\ActionInterface;
use Spy\TimelineBundle\Spread\Entry\Entry;
use Spy\TimelineBundle\Spread\Entry\EntryCollection;

/**
 * SpreadManager
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class SpreadManager
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
     * @var boolean
     */
    protected $onSubject;

    /**
     * @var boolean
     */
    protected $onGlobalContext;

    /**
     * @param array $options
     */
    public function __construct($onSubject = true, $onGlobalContext = true)
    {
        $this->spreads         = new \ArrayIterator();
        $this->onSubject       = $onSubject;
        $this->onGlobalContext = $onGlobalContext;
        $this->results         = new EntryCollection($this->onGlobalContext);
    }

    /**
     * @param SpreadInterface $spread
     */
    public function add(SpreadInterface $spread)
    {
        $this->spreads[] = $spread;
    }

    /**
     * @return \ArrayIterator of SpreadInterface
     */
    public function getSpreads()
    {
        return $this->spreads;
    }

    /**
     * @param ActionInterface $action action
     */
    public function process(ActionInterface $action)
    {
        if ($this->onSubject) {
            $this->results->set('GLOBAL', new Entry($action->getSubject()));
        }

        foreach ($this->spreads as $spread) {
            if ($spread->supports($action)) {
                $spread->process($action, $this->results);
            }
        }

        return $this->getResults();
    }

    /**
     * @return EntryCollection
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
        $this->results = new EntryCollection($this->onGlobalContext);
    }
}
