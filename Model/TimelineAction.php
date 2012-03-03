<?php

namespace Highco\TimelineBundle\Model;

use Symfony\Component\HttpFoundation\Request;

/**
 * TimelineAction
 *
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class TimelineAction
{
    CONST STATUS_PENDING   = 'pending';
    CONST STATUS_PUBLISHED = 'published';
    CONST STATUS_FROZEN    = 'frozen';

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var object
     */
    protected $subject;

    /**
     * @var string
     */
    protected $subject_model;

    /**
     * @var integer
     */
    protected $subject_id;

    /**
     * @var string
     */
    protected $verb;

    /**
     * @var string
     */
    protected $direct_complement_text;

    /**
     * @var string
     */
    protected $direct_complement_model;

    /**
     * @var integer
     */
    protected $direct_complement_id;

    /**
     * @var object
     */
    protected $direct_complement;

    /**
     * @var string
     */
    protected $indirect_complement_text;

    /**
     * @var string
     */
    protected $indirect_complement_model;

    /**
     * @var integer
     */
    protected $indirect_complement_id;

    /**
     * @var object
     */
    protected $indirect_complement;

    /**
     * @var string
     */
    protected $status_current = 'pending';

    /**
     * @var string
     */
    protected $status_wanted = 'published';

    /**
     * @var string
     */
    protected $dupplicate_key;

    /**
     * @var integer
     */
    protected $dupplicate_priority;

    /**
     * @var boolean
     */
    protected $dupplicated = false;

    /**
     * @var \DateTime
     */
    protected $created_at;

    public function __construct()
    {
        $this->created_at     = new \DateTime();
        $this->status_current = self::STATUS_PENDING;
        $this->status_wanted  = self::STATUS_PUBLISHED;
    }

    /**
     * @return boolean
     */
    public function isPublished()
    {
        return $this->status_current == self::STATUS_PUBLISHED;
    }

    /**
     * @return boolean
     */
    public function hasDupplicateKey()
    {
        return null !== $this->dupplicate_key;
    }

    /**
     * @param boolean $v
     */
    public function setIsDupplicated($dupplicated)
    {
        $this->dupplicated = (bool) $dupplicated;
    }

    /**
     * @return boolean
     */
    public function isDupplicated()
    {
        return (bool) $this->dupplicated;
    }

    /**
     * @param Request $request
     *
     * @return TimelineAction
     */
    static public function fromRequest(Request $request)
    {
        if (null === $request->get('subject_model')) {
            throw new \InvalidArgumentException('You have to define subject model on "'.__CLASS__.'"');
        }

        if (null === $request->get('subject_id')) {
            throw new \InvalidArgumentException('You have to define subject id on "'.__CLASS__.'"');
        }

        $subject = new self();
        $subject->setSubjectModel($request->get('subject_model'));
        $subject->setSubjectId($request->get('subject_id'));
        $subject->setVerb($request->get('verb'));
        $subject->setDirectComplementText($request->get('direct_complement_text'));
        $subject->setDirectComplementModel($request->get('direct_complement_model'));
        $subject->setDirectComplementId($request->get('direct_complement_id'));
        $subject->setIndirectComplementText($request->get('indirect_complement_text'));
        $subject->setIndirectComplementModel($request->get('indirect_complement_model'));
        $subject->setIndirectComplementId($request->get('indirect_complement_id'));

        return $subject;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param object $subject
     * @param string $verb
     * @param object $directComplement
     * @param object $indirectComplement
     */
    public function create($subject, $verb, $directComplement = null, $indirectComplement = null)
    {
        if (!is_object($subject)) {
            throw new \InvalidArgumentException('Subject should be an object');
        }

        $this->setSubject($subject);
        $this->setSubjectModel(get_class($subject));
        $this->setSubjectId($subject->getId());

        $this->setVerb((string) $verb);

        if (!is_object($directComplement)) {
            $this->setDirectComplementText($directComplement);
        } else {
            $this->setDirectComplement($directComplement);
            $this->setDirectComplementModel(get_class($directComplement));
            $this->setDirectComplementId($directComplement->getId());
        }

        if (!is_object($indirectComplement)) {
            $this->setIndirectComplementText($indirectComplement);
        } else {
            $this->setIndirectComplement($indirectComplement);
            $this->setIndirectComplementModel(get_class($indirectComplement));
            $this->setIndirectComplementId($indirectComplement->getId());
        }
    }

    /**
     * @param object $subject
     */
    public function setSubject($subject)
    {
        if (!is_object($subject)) {
            throw new \InvalidArgumentException('direct complement should be an object');
        }

        $this->subject = $subject;
        $this->setSubjectModel(get_class($subject));
        $this->setSubjectId($subject->getId());
    }

    /**
     * @return object|null
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subjectModel
     */
    public function setSubjectModel($subjectModel)
    {
        $this->subject_model = $subjectModel;
    }

    /**
     * @return string
     */
    public function getSubjectModel()
    {
        return $this->subject_model;
    }

    /**
     * @param integer $subjectId
     */
    public function setSubjectId($subjectId)
    {
        $this->subject_id = $subjectId;
    }

    /**
     * @return integer
     */
    public function getSubjectId()
    {
        return $this->subject_id;
    }

    /**
     * @param string $verb
     */
    public function setVerb($verb)
    {
        $this->verb = $verb;
    }

    /**
     * @return string
     */
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     * @param object $direct_complement
     */
    public function setDirectComplement($direct_complement)
    {
        if (!is_object($direct_complement)) {
            throw new \InvalidArgumentException('direct complement should be an object');
        }

        $this->direct_complement       = $direct_complement;
        $this->setDirectComplementModel(get_class($direct_complement));
        $this->setDirectComplementId($direct_complement->getId());
    }

    /**
     * @return object|null
     */
    public function getDirectComplement()
    {
        if (null !== $this->direct_complement) {
            return $this->direct_complement;
        }

        return $this->getDirectComplementText();
    }

    /**
     * @param string $directComplementText
     */
    public function setDirectComplementText($directComplementText)
    {
        $this->direct_complement_text = $directComplementText;
    }

    /**
     * @return string
     */
    public function getDirectComplementText()
    {
        return $this->direct_complement_text;
    }

    /**
     * @param string $directComplementModel
     */
    public function setDirectComplementModel($directComplementModel)
    {
        $this->direct_complement_model = $directComplementModel;
    }

    /**
     * @return string
     */
    public function getDirectComplementModel()
    {
        return $this->direct_complement_model;
    }

    /**
     * @param integer $directComplementId
     */
    public function setDirectComplementId($directComplementId)
    {
        $this->direct_complement_id = $directComplementId;
    }

    /**
     * @return integer
     */
    public function getDirectComplementId()
    {
        return $this->direct_complement_id;
    }

    /**
     * @param object $indirectComplement
     */
    public function setIndirectComplement($indirectComplement)
    {
        if (!is_object($indirectComplement)) {
            throw new \InvalidArgumentException('indirect complement should be an object');
        }

        $this->indirect_complement = $indirectComplement;
        $this->setIndirectComplementModel(get_class($indirectComplement));
        $this->setIndirectComplementId($indirectComplement->getId());

    }

    /**
     * @return object|null
     */
    public function getIndirectComplement()
    {
        return $this->indirect_complement;
    }

    /**
     * @param string $indirectComplementText
     */
    public function setIndirectComplementText($indirectComplementText)
    {
        $this->indirect_complement_text = $indirectComplementText;
    }

    /**
     * @return string
     */
    public function getIndirectComplementText()
    {
        return $this->indirect_complement_text;
    }

    /**
     * @param string $indirectComplementModel
     */
    public function setIndirectComplementModel($indirectComplementModel)
    {
        $this->indirect_complement_model = $indirectComplementModel;
    }

    /**
     * @return string
     */
    public function getIndirectComplementModel()
    {
        return $this->indirect_complement_model;
    }

    /**
     * @param integer $indirectComplementId
     */
    public function setIndirectComplementId($indirectComplementId)
    {
        $this->indirect_complement_id = $indirectComplementId;
    }

    /**
     * @return integer
     */
    public function getIndirectComplementId()
    {
        return $this->indirect_complement_id;
    }

    /**
     * @param string $status
     *
     * @return boolean
     */
    public function isValidStatus($status)
    {
        return in_array((string) $status, array(
            self::STATUS_PENDING,
            self::STATUS_PUBLISHED,
            self::STATUS_FROZEN,
        ));
    }

    /**
     * @param string $statusCurrent
     */
    public function setStatusCurrent($statusCurrent)
    {
        if (!$this->isValidStatus($statusCurrent)) {
            throw new \InvalidArgumentException('Status "'.$statusCurrent.'" is not valid');
        }

        $this->status_current = $statusCurrent;
    }

    /**
     * @return string
     */
    public function getStatusCurrent()
    {
        return $this->status_current;
    }

    /**
     * @param string $statusWanted
     */
    public function setStatusWanted($statusWanted)
    {
        if (!$this->isValidStatus($statusWanted)) {
            throw new \InvalidArgumentException('Status "'.$statusWanted.'" is not valid');
        }

        $this->status_wanted = $statusWanted;
    }

    /**
     * @return string
     */
    public function getStatusWanted()
    {
        return $this->status_wanted;
    }

    /**
     * @param string $dupplicateKey
     */
    public function setDupplicateKey($dupplicateKey)
    {
        $this->dupplicate_key = $dupplicateKey;
    }

    /**
     * @return string
     */
    public function getDupplicateKey()
    {
        return $this->dupplicate_key;
    }

    /**
     * @param integer $dupplicatePriority
     */
    public function setDupplicatePriority($dupplicatePriority)
    {
        $this->dupplicate_priority = $dupplicatePriority;
    }

    /**
     * @return integer
     */
    public function getDupplicatePriority()
    {
        return (int) $this->dupplicate_priority;
    }

    /**
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function __sleep()
    {
        return array(
            'id',
            'subject_model',
            'subject_id',
            'verb',
            'direct_complement_text',
            'direct_complement_model',
            'direct_complement_id',
            'indirect_complement_text',
            'indirect_complement_model',
            'indirect_complement_id',
            'status_current',
            'status_wanted',
            'dupplicate_key',
            'dupplicate_priority',
            'created_at',
        );
    }
}
