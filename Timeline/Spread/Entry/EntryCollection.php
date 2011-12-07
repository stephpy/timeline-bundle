<?php

namespace Highco\TimelineBundle\Timeline\Spread\Entry;

/**
 * EntryCollection
 *
 * @package
 * @version $id$
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class EntryCollection implements \IteratorAggregate
{
	protected $coll;
	protected $dupplicate_on_global = true;

	/**
	 * __construct
	 *
	 * @access public
	 * @return void
	 */
	public function __construct($dupplicate_on_global = true)
	{
		$this->coll = new \ArrayIterator();
		$this->dupplicate_on_global = $dupplicate_on_global;
	}

	/**
	 * getIterator
	 *
	 * @access public
	 * @return void
	 */
	public function getIterator()
	{
		return $this->coll;
	}

	/**
	 * set
	 *
	 * @param mixed $context
	 * @param mixed $entry
	 * @access public
	 * @return void
	 */
	public function set($context, $entry)
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
	 * @access public
	 * @return void
	 */
	public function getEntries()
	{
		return $this->coll;
	}
}
