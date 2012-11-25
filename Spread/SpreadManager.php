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
    protected $entryCollection;

    /**
     * @var boolean
     */
    protected $onSubject;

    /**
     * @param EntryCollection $entryCollection entryCollection
     * @param boolean         $onSubject       onSubject
     */
    public function __construct(EntryCollection $entryCollection, $onSubject = true)
    {
        $this->spreads         = new \ArrayIterator();
        $this->onSubject       = $onSubject;
        $this->entryCollection = $entryCollection;
    }

    /**
     * @param SpreadInterface $spread
     */
    public function add(SpreadInterface $spread)
    {
        $this->spreads[] = $spread;
    }

    /**
     * @param ActionInterface $action action
     */
    public function process(ActionInterface $action)
    {
        if ($this->onSubject) {
            $this->entryCollection->set(new Entry($action->getSubject()), 'GLOBAL');
        }

        foreach ($this->spreads as $spread) {
            if ($spread->supports($action)) {
                $spread->process($action, $this->entryCollection);
            }
        }

        return $this->getEntryCollection();
    }

    /**
     * Clears the entryCollection
     */
    public function clear()
    {
        $this->entryCollection->clear();
    }

    /**
     * @return EntryCollection
     */
    public function getEntryCollection()
    {
        return $this->entryCollection;
    }

    /**
     * @return \ArrayIterator of SpreadInterface
     */
    public function getSpreads()
    {
        return $this->spreads;
    }
}
