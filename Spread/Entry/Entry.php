<?php

namespace Spy\TimelineBundle\Spread\Entry;

use Spy\TimelineBundle\Model\ComponentInterface;

/**
 * Entry
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Entry implements EntryInterface
{
    /**
     * @var ComponentInterface
     */
    protected $subject;

    /**
     * @param ComponentInterface $subject subject
     */
    public function __construct(ComponentInterface $subject)
    {
        $this->subject = $subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdent()
    {
        return sprintf('%s:%s', $this->subject->getModel(), serialize($this->subject->getIdentifier()));
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
