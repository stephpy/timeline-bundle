<?php

namespace Highco\TimelineBundle\Model;

/**
 * TimelineAction
 *
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class TimelineAction
{
    CONST STATUS_PENDING   = "pending";
    CONST STATUS_PUBLISHED = "published";
    CONST STATUS_FROZEN    = "frozen";

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var object $subject
     */
    protected $subject;

    /**
     * @var string $subject_model
     */
    protected $subject_model;

    /**
     * @var integer $subject_id
     */
    protected $subject_id;

    /**
     * @var string $verb
     */
    protected $verb;

    /**
     * @var string $direct_complement_model
     */
    protected $direct_complement_model;

    /**
     * @var integer $direct_complement_id
     */
    protected $direct_complement_id;

    /**
     * @var object $direct_complement
     */
    protected $direct_complement;

    /**
     * @var string $indirect_complement_model
     */
    protected $indirect_complement_model;

    /**
     * @var integer $indirect_complement_id
     */
    protected $indirect_complement_id;

    /**
     * @var object $indirect_complement
     */
    protected $indirect_complement;

    /**
     * @var string $status_current
     */
    protected $status_current = "pending";

    /**
     * @var string $status_wanted
     */
    protected $status_wanted = "published";

    /**
     * @var string $dupplicate_key
     */
    protected $dupplicate_key;

    /**
     * @var integer $dupplicate_priority
     */
    protected $dupplicate_priority;

    /**
     * @var boolean $dupplicated
     */
    protected $dupplicated = false;

    /**
     * @var datetime $created_at
     */
    protected $created_at;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->created_at     = new \DateTime();
        $this->status_current = self::STATUS_PENDING;
        $this->status_wanted  = self::STATUS_PUBLISHED;
    }

    /**
     * isPublished
     *
     * @return boolean
     */
    public function isPublished()
    {
        return $this->status_current == self::STATUS_PUBLISHED;
    }

    /**
     * hasDupplicateKey
     *
     * @return boolean
     */
    public function hasDupplicateKey()
    {
        return false === is_null($this->dupplicate_key);
    }

    /**
     * setIsDupplicated
     *
     * @param boolean $v
     */
    public function setIsDupplicated($v)
    {
        $this->dupplicated = (bool) $v;
    }

    /**
     * isDupplicated
     *
     * @return boolean
     */
    public function isDupplicated()
    {
        return (bool) $this->dupplicated;
    }

    /**
     * fromRequest
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return TimelineAction
     */
    static public function fromRequest(\Symfony\Component\HttpFoundation\Request $request)
    {
        if(is_null($request->get('subject_model')))
        {
            throw new \InvalidArgumentException('You have to define subject model on "'.__CLASS__.'"');
        }

        if(is_null($request->get('subject_id')))
        {
            throw new \InvalidArgumentException('You have to define subject id on "'.__CLASS__.'"');
        }

        $subject  = new self();
        $subject->setSubjectModel($request->get('subject_model'));
        $subject->setSubjectId($request->get('subject_id'));
        $subject->setVerb($request->get('verb'));
        $subject->setDirectComplementModel($request->get('direct_complement_model'));
        $subject->setDirectComplementId($request->get('direct_complement_id'));
        $subject->setIndirectComplementModel($request->get('indirect_complement_model'));
        $subject->setIndirectComplementId($request->get('indirect_complement_id'));

        return $subject;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * create
     *
     * @param object $subject
     * @param string $verb
     * @param object $direct_complement
     * @param object $indirect_complement
     */
    public function create($subject, $verb, $direct_complement, $indirect_complement = null)
    {
        if(false === is_object($subject))
        {
            throw new \InvalidArgumentException('Subject should be an object');
        }

        $this->setSubjectModel(get_class($subject));
        $this->setSubjectId($subject->getId());

        $this->setVerb((string) $verb);

        if(false === is_object($direct_complement))
        {
            throw new \InvalidArgumentException('Direct complement should be an object');
        }

        $this->setDirectComplementModel(get_class($direct_complement));
        $this->setDirectComplementId($direct_complement->getId());

        if(is_null($indirect_complement_id))
        {
            return;
        }

        if(false === is_object($indirect_complement))
        {
            throw new \InvalidArgumentException('Direct complement should be an object');
        }

        $this->setIndirectComplementModel(get_class($indirect_complement));
        $this->setIndirectComplementId($indirect_complement->getId());
    }

    /**
     * setSubject
     *
     * @param object $subject
     */
    public function setSubject($subject)
    {
        if(false === is_object($subject))
        {
            throw new \InvalidArgumentException('direct complement should be an object');
        }

        $this->subject = $subject;
    }

    /**
     * getSubject
     *
     * @return object|null
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set subject_model
     *
     * @param string $subjectModel
     */
    public function setSubjectModel($subjectModel)
    {
        $this->subject_model = $subjectModel;
    }

    /**
     * Get subject_model
     *
     * @return string
     */
    public function getSubjectModel()
    {
        return $this->subject_model;
    }

    /**
     * Set subject_id
     *
     * @param integer $subjectId
     */
    public function setSubjectId($subjectId)
    {
        $this->subject_id = $subjectId;
    }

    /**
     * Get subject_id
     *
     * @return integer
     */
    public function getSubjectId()
    {
        return $this->subject_id;
    }

    /**
     * Set verb
     *
     * @param string $verb
     */
    public function setVerb($verb)
    {
        $this->verb = $verb;
    }

    /**
     * Get verb
     *
     * @return string
     */
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     * setDirectComplement
     *
     * @param object $direct_complement
     */
    public function setDirectComplement($direct_complement)
    {
        if(false === is_object($direct_complement))
        {
            throw new \InvalidArgumentException('direct complement should be an object');
        }

        $this->direct_complement = $direct_complement;
    }

    /**
     * getDirectComplement
     *
     * @return object|null
     */
    public function getDirectComplement()
    {
        return $this->direct_complement;
    }

    /**
     * Set direct_complement_model
     *
     * @param string $directComplementModel
     */
    public function setDirectComplementModel($directComplementModel)
    {
        $this->direct_complement_model = $directComplementModel;
    }

    /**
     * Get direct_complement_model
     *
     * @return string
     */
    public function getDirectComplementModel()
    {
        return $this->direct_complement_model;
    }

    /**
     * Set direct_complement_id
     *
     * @param integer $directComplementId
     */
    public function setDirectComplementId($directComplementId)
    {
        $this->direct_complement_id = $directComplementId;
    }

    /**
     * Get direct_complement_id
     *
     * @return integer
     */
    public function getDirectComplementId()
    {
        return $this->direct_complement_id;
    }

    /**
     * setIndirectComplement
     *
     * @param object $indirect_complement
     */
    public function setIndirectComplement($indirect_complement)
    {
        if(false === is_object($indirect_complement))
        {
            throw new \InvalidArgumentException('indirect complement should be an object');
        }

        $this->indirect_complement = $indirect_complement;
    }

    /**
     * getIndirectComplement
     *
     * @return object|null
     */
    public function getIndirectComplement()
    {
        return $this->indirect_complement;
    }

    /**
     * Set indirect_complement_model
     *
     * @param string $indirectComplementModel
     */
    public function setIndirectComplementModel($indirectComplementModel)
    {
        $this->indirect_complement_model = $indirectComplementModel;
    }

    /**
     * Get indirect_complement_model
     *
     * @return string
     */
    public function getIndirectComplementModel()
    {
        return $this->indirect_complement_model;
    }

    /**
     * Set indirect_complement_id
     *
     * @param integer $indirectComplementId
     */
    public function setIndirectComplementId($indirectComplementId)
    {
        $this->indirect_complement_id = $indirectComplementId;
    }

    /**
     * Get indirect_complement_id
     *
     * @return integer
     */
    public function getIndirectComplementId()
    {
        return $this->indirect_complement_id;
    }

    /**
     * isValidStatus
     *
     * @param string $v
     * @return boolean
     */
    public function isValidStatus($v)
    {
        return in_array((string) $v, array(
            self::STATUS_PENDING,
            self::STATUS_PUBLISHED,
            self::STATUS_FROZEN,
        ));
    }

    /**
     * Set status_current
     *
     * @param string $statusCurrent
     */
    public function setStatusCurrent($statusCurrent)
    {
        if(false === $this->isValidStatus($statusCurrent))
        {
            throw new \InvalidArgumentException('Status "'.$statusCurrent.'" is not valid');
        }

        $this->status_current = $statusCurrent;
    }

    /**
     * Get status_current
     *
     * @return string
     */
    public function getStatusCurrent()
    {
        return $this->status_current;
    }

    /**
     * Set status_wanted
     *
     * @param string $statusWanted
     */
    public function setStatusWanted($statusWanted)
    {
        if(false === $this->isValidStatus($statusWanted))
        {
            throw new \InvalidArgumentException('Status "'.$statusWanted.'" is not valid');
        }

        $this->status_wanted = $statusWanted;
    }

    /**
     * Get status_wanted
     *
     * @return string
     */
    public function getStatusWanted()
    {
        return $this->status_wanted;
    }

    /**
     * Set dupplicate_key
     *
     * @param string $dupplicateKey
     */
    public function setDupplicateKey($dupplicateKey)
    {
        $this->dupplicate_key = $dupplicateKey;
    }

    /**
     * Get dupplicate_key
     *
     * @return string
     */
    public function getDupplicateKey()
    {
        return $this->dupplicate_key;
    }

    /**
     * Set dupplicate_priority
     *
     * @param integer $dupplicatePriority
     */
    public function setDupplicatePriority($dupplicatePriority)
    {
        $this->dupplicate_priority = $dupplicatePriority;
    }

    /**
     * Get dupplicate_priority
     *
     * @return integer
     */
    public function getDupplicatePriority()
    {
        return (int) $this->dupplicate_priority;
    }

    /**
     * Set created_at
     *
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
}
