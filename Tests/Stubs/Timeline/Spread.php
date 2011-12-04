<?php

namespace Highco\TimelineBundle\Tests\Stubs\Timeline;

use Highco\TimelineBundle\Timeline\Spread\InterfaceSpread;
use Highco\TimelineBundle\Timeline\Token\Timeline;

class Spread implements InterfaceSpread
{
    protected $supports = true;

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
     * supports
     *
     * @param Timeline $token
     * @access public
     * @return void
     */
    public function supports(Timeline $token)
    {
        return $this->supports;
    }

    /**
     * setSupports
     *
     * @param mixed $v
     * @access public
     * @return void
     */
    public function setSupports($v)
    {
        $this->supports = (bool) $v;
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
        $this->results['mytimeline'][] = array(
            'subject_model' => '\EveryBody',
            'subject_id'    => 1,
        );
    }
}
