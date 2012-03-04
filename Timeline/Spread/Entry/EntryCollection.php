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
    protected $dupplicateOnGlobal = true;

    /**
     * __construct
     */
    public function __construct($dupplicateOnGlobal = true)
    {
        $this->coll = new \ArrayIterator();
        $this->dupplicateOnGlobal = $dupplicateOnGlobal;
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

        if ($this->dupplicateOnGlobal && $context !== 'GLOBAL') {
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
