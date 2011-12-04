<?php

namespace Highco\TimelineBundle\Timeline\Spread\Entry;

/**
 * EntryCollection
 *
 * @package
 * @version $id$
 * @author Your name <yourmail@yourhost.com>
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
	public function __construct()
	{
		$this->coll = new \ArrayIterator();
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
