<?php

namespace Highco\TimelineBundle\Timeline\Spread\Entry;

/**
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class EntryCollection implements \IteratorAggregate
{
    protected $coll;
    protected $duplicateOnGlobal = true;

    /**
     * __construct
     */
    public function __construct($duplicateOnGlobal = true)
    {
        $this->coll = new \ArrayIterator();
        $this->duplicateOnGlobal = $duplicateOnGlobal;
    }

    /**
     * @return array
     */
    public function getIterator()
    {
        return $this->coll;
    }

    /**
     * set
     *
     * @param string $context
     * @param Entry $entry
     */
    public function set($context, Entry $entry)
    {
        if (!isset($this->coll[$context])) {
            $this->coll[$context] = array();
        }

        $this->coll[$context][$entry->getIdent()] = $entry;

        if ($this->duplicateOnGlobal && $context !== 'GLOBAL') {
            $this->set('GLOBAL', $entry);
        }
    }

    /**
     * @return \ArrayIterator
     */
    public function getEntries()
    {
        return $this->coll;
    }
}
