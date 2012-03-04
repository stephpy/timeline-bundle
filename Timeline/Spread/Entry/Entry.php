<?php

namespace Highco\TimelineBundle\Timeline\Spread\Entry;

/**
 * Entry
 *
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Entry
{
    public $subject_model;
    public $subject_id;

    /**
     * @return string
     */
    public function getIdent()
    {
        return sprintf('%s:%s', $this->subject_model, $this->subject_id);
    }
}
