<?php

namespace Highco\TimelineBundle\Timeline\Spread\Entry;

/**
 * EntryCollection
 *
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class EntryCollection implements \IteratorAggregate
{
	protected $coll;
	protected $dupplicate_on_global = true;

	/**
	 * __construct
	 */
	public function __construct($dupplicate_on_global = true)
	{
		$this->coll = new \ArrayIterator();
		$this->dupplicate_on_global = $dupplicate_on_global;
	}

	/**
	 * getIterator
	 *
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
		if(false === isset($this->coll[$context]))
		{
			$this->coll[$context] = array();
		}

		$this->coll[$context][$entry->getIdent()] = $entry;

		if($this->dupplicate_on_global && $context !== "GLOBAL")
		{
			$this->set("GLOBAL", $entry);
		}
	}

	/**
	 * getEntries
	 *
	 * @return array
	 */
	public function getEntries()
	{
		return $this->coll;
	}
}
