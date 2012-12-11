<?php

namespace Spy\TimelineBundle\Spread\Entry;

use Spy\Timeline\Model\ComponentInterface;

/**
 * EntryUnaware
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class EntryUnaware implements EntryInterface
{
    /**
     * @var string
     */
    protected $subjectModel;

    /**
     * @var string
     */
    protected $subjectId;

    /**
     * @var ComponentInterface
     */
    protected $subject;

    /**
     * @var boolean
     */
    protected $strict;

    /**
     * @param string  $subjectModel subjectModel
     * @param string  $subjectId    subjectId
     * @param boolean $strict       If strict (component fetch is mandatory,
     * if nothing is returned, exception will be throwed)
     */
    public function __construct($subjectModel, $subjectId, $strict = false)
    {
        $this->subjectModel = $subjectModel;

        if (is_scalar($subjectId)) {
            $subjectId = (string) $subjectId;
        } elseif (!is_array($subjectId)) {
            throw new \InvalidArgumentException('SubjectId has to be a scalar or an array');
        }

        $this->subjectId    = $subjectId;
        $this->strict       = $strict;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdent()
    {
        return $this->subjectModel.'#'.serialize($this->subjectId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param ComponentInterface $subject subject
     */
    public function setSubject(ComponentInterface $subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getSubjectModel()
    {
        return $this->subjectModel;
    }

    /**
     * @return string
     */
    public function getSubjectId()
    {
        return $this->subjectId;
    }

    /**
     * @return boolean
     */
    public function isStrict()
    {
        return $this->strict;
    }
}
