<?php

namespace Highco\TimelineBundle\Timeline\Spread;

use Highco\TimelineBundle\Timeline\Token\Timeline;

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
    protected $results = array();

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->spreads = new \ArrayIterator();
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
     * @param Timeline $token
     * @access public
     * @return void
     */
    public function process(Timeline $token)
    {
        foreach($this->spreads as $spread)
        {
            if($spread->supports($token))
            {
                $spread->process($token);
                $this->results = array_merge($this->results, (array) $spread->getResults());
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
        $this->results = array();
    }
}
