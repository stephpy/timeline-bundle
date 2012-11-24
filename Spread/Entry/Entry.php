<?php

namespace Spy\TimelineBundle\Spread\Entry;

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

    /**
     * create
     *
     * @param string $subjectModel The subject model
     * @param string $subjectId    The subject identifier
     *
     * @return Entry
     */
    public static function create($subjectModel, $subjectId)
    {
        $entry               = new self();
        $entry->subjectModel = $subjectModel;
        $entry->subjectId    = $subjectId;

        return $entry;
    }
}
