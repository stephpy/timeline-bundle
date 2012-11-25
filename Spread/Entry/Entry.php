<?php

namespace Spy\TimelineBundle\Spread\Entry;

use Spy\TimelineBundle\Model\Component;

/**
 * Entry
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Entry
{
    /**
     * @var Component
     */
    public $subject;

    /**
     * @param Component $subject subject
     */
    public function __construct(Component $subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getIdent()
    {
        return sprintf('%s:%s', $this->subject->getModel(), serialize($this->subject->getIdentifier()));
    }
}
