<?php

namespace Highco\TimelineBundle\Timeline\Spread\Entry;

/**
 * Entry
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Entry
{
    public $subjectModel;
    public $subjectId;

    /**
     * @return string
     */
    public function getIdent()
    {
        return sprintf('%s:%s', $this->subjectModel, $this->subjectId);
    }
}
