<?php

namespace Spy\TimelineBundle\Spread\Entry;

/**
 * A collection of entry
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class EntryCollection implements \IteratorAggregate
{
    protected $coll;
    protected $duplicateOnGlobal = true;

    /**
     * @param boolean $duplicateOnGlobal Each timeline action are automatically pushed on Global context
     */
    public function __construct($duplicateOnGlobal = true)
    {
        $this->coll              = new \ArrayIterator();
        $this->duplicateOnGlobal = $duplicateOnGlobal;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->coll;
    }

    /**
     * @param EntryInterface $entry   entry you want to push
     * @param string         $context context where you want to push
     */
    public function set(EntryInterface $entry, $context)
    {
        if (!isset($this->coll[$context])) {
            $this->coll[$context] = array();
        }

        $this->coll[$context][$entry->getIdent()] = $entry;

        if ($this->duplicateOnGlobal && $context !== 'GLOBAL') {
            $this->set($entry, 'GLOBAL');
        }
    }

    /**
     * @return \ArrayIterator
     */
    public function getEntries()
    {
        return $this->coll;
    }

    /**
     * Clear entries
     */
    public function clear()
    {
        $this->coll = new \ArrayIterator();
    }
}
