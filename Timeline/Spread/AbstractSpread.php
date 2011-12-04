<?php

namespace Highco\TimelineBundle\Timeline\Spread;

class AbstractSpread
{
    CONST TIMELINE_GLOBAL = "Global";

    protected $results;

    /**
     * getResults
     *
     * @access public
     * @return void
     */
    public function getResults()
    {
        return array_filter($this->results);
    }

    /**
     * addResult
     *
     * @param mixed $subject_model
     * @param mixed $subject_id
     * @param mixed $timeline
     * @access public
     * @return void
     */
    public function addResult($subject_model, $subject_id, $timeline = self::TIMELINE_GLOBAL)
    {
        if(false === isset($this->results[$timeline]))
            $this->results[$timeline] = array();

        $this->results[$timeline][] = array(
            'subject_model' => $subject_model,
            'subject_id' => $subject_id,
        );
    }
}
