<?php

namespace Highco\TimelineBundle\Model;

use Symfony\Component\HttpFoundation\Request;

/**
 * TimelineAction
 *
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
    protected $subjectModel;

    /**
     * @var integer
     */
    protected $subjectId;

    /**
     * @var string
     */
    protected $verb;

    /**
     * @var string
     */
    protected $directComplementText;

    /**
     * @var string
     */
    protected $directComplementModel;

    /**
     * @var integer
     */
    protected $directComplementId;

    /**
     * @var object
     */
    protected $directComplement;

    /**
     * @var string
     */
    protected $indirectComplementText;

    /**
     * @var string
     */
    protected $indirectComplementModel;

    /**
     * @var integer
     */
    protected $indirectComplementId;

    /**
     * @var object
     */
    protected $indirectComplement;

    /**
     * @var string
     */
    protected $statusCurrent = 'pending';

    /**
     * @var string
     */
    protected $statusWanted = 'published';

    /**
     * @var string
     */
    protected $duplicateKey;

    /**
     * @var integer
     */
    protected $duplicatePriority;

    /**
     * @var boolean
     */
    protected $duplicated = false;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Initialize createdAt, statusCurrent and statusWanted property
     */
    public function __construct()
    {
        $this->createdAt     = new \DateTime();
        $this->statusCurrent = self::STATUS_PENDING;
        $this->statusWanted  = self::STATUS_PUBLISHED;
    }

    /**
     * @return boolean
     */
    public function isPublished()
    {
        return $this->statusCurrent == self::STATUS_PUBLISHED;
    }

    /**
     * @return boolean
     */
    public function hasDuplicateKey()
    {
        return null !== $this->duplicateKey;
    }

    /**
     * @param boolean $duplicated
     */
    public function setIsDuplicated($duplicated)
    {
        $this->duplicated = (bool) $duplicated;
    }

    /**
     * @return boolean
     */
    public function isDuplicated()
    {
        return (bool) $this->duplicated;
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
     * Chuck Norris comments Fight 1337 of Vic Mc Key
     *
     * @param object $subject            The subject of the timeline action (Chuck Norris)
     * @param string $verb               The verb (comments)
     * @param object $directComplement   The direct complement (optional) (fight 1337)
     * @param object $indirectComplement The indirect complement (optional) (Vic Mc Key)
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
        $this->subjectModel = $subjectModel;
    }

    /**
     * @return string
     */
    public function getSubjectModel()
    {
        return $this->subjectModel;
    }

    /**
     * @param integer $subjectId
     */
    public function setSubjectId($subjectId)
    {
        $this->subjectId = $subjectId;
    }

    /**
     * @return integer
     */
    public function getSubjectId()
    {
        return $this->subjectId;
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
     * @param object $directComplement
     */
    public function setDirectComplement($directComplement)
    {
        if (!is_object($directComplement)) {
            throw new \InvalidArgumentException('direct complement should be an object');
        }

        $this->directComplement       = $directComplement;
        $this->setDirectComplementModel(get_class($directComplement));
        $this->setDirectComplementId($directComplement->getId());
    }

    /**
     * @return object|null
     */
    public function getDirectComplement()
    {
        if (null !== $this->directComplement) {
            return $this->directComplement;
        }

        return $this->getDirectComplementText();
    }

    /**
     * @param string $directComplementText
     */
    public function setDirectComplementText($directComplementText)
    {
        $this->directComplementText = $directComplementText;
    }

    /**
     * @return string
     */
    public function getDirectComplementText()
    {
        return $this->directComplementText;
    }

    /**
     * @param string $directComplementModel
     */
    public function setDirectComplementModel($directComplementModel)
    {
        $this->directComplementModel = $directComplementModel;
    }

    /**
     * @return string
     */
    public function getDirectComplementModel()
    {
        return $this->directComplementModel;
    }

    /**
     * @param integer $directComplementId
     */
    public function setDirectComplementId($directComplementId)
    {
        $this->directComplementId = $directComplementId;
    }

    /**
     * @return integer
     */
    public function getDirectComplementId()
    {
        return $this->directComplementId;
    }

    /**
     * @param object $indirectComplement
     */
    public function setIndirectComplement($indirectComplement)
    {
        if (!is_object($indirectComplement)) {
            throw new \InvalidArgumentException('indirect complement should be an object');
        }

        $this->indirectComplement = $indirectComplement;
        $this->setIndirectComplementModel(get_class($indirectComplement));
        $this->setIndirectComplementId($indirectComplement->getId());

    }

    /**
     * @return object|null
     */
    public function getIndirectComplement()
    {
        return $this->indirectComplement;
    }

    /**
     * @param string $indirectComplementText
     */
    public function setIndirectComplementText($indirectComplementText)
    {
        $this->indirectComplementText = $indirectComplementText;
    }

    /**
     * @return string
     */
    public function getIndirectComplementText()
    {
        return $this->indirectComplementText;
    }

    /**
     * @param string $indirectComplementModel
     */
    public function setIndirectComplementModel($indirectComplementModel)
    {
        $this->indirectComplementModel = $indirectComplementModel;
    }

    /**
     * @return string
     */
    public function getIndirectComplementModel()
    {
        return $this->indirectComplementModel;
    }

    /**
     * @param integer $indirectComplementId
     */
    public function setIndirectComplementId($indirectComplementId)
    {
        $this->indirectComplementId = $indirectComplementId;
    }

    /**
     * @return integer
     */
    public function getIndirectComplementId()
    {
        return $this->indirectComplementId;
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

        $this->statusCurrent = $statusCurrent;
    }

    /**
     * @return string
     */
    public function getStatusCurrent()
    {
        return $this->statusCurrent;
    }

    /**
     * @param string $statusWanted
     */
    public function setStatusWanted($statusWanted)
    {
        if (!$this->isValidStatus($statusWanted)) {
            throw new \InvalidArgumentException('Status "'.$statusWanted.'" is not valid');
        }

        $this->statusWanted = $statusWanted;
    }

    /**
     * @return string
     */
    public function getStatusWanted()
    {
        return $this->statusWanted;
    }

    /**
     * @param string $duplicateKey
     */
    public function setDuplicateKey($duplicateKey)
    {
        $this->duplicateKey = $duplicateKey;
    }

    /**
     * @return string
     */
    public function getDuplicateKey()
    {
        return $this->duplicateKey;
    }

    /**
     * @param integer $duplicatePriority
     */
    public function setDuplicatePriority($duplicatePriority)
    {
        $this->duplicatePriority = $duplicatePriority;
    }

    /**
     * @return integer
     */
    public function getDuplicatePriority()
    {
        return (int) $this->duplicatePriority;
    }

    /**
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return array(
            'id',
            'subjectModel',
            'subjectId',
            'verb',
            'directComplementText',
            'directComplementModel',
            'directComplementId',
            'indirectComplementText',
            'indirectComplementModel',
            'indirectComplementId',
            'statusCurrent',
            'statusWanted',
            'duplicateKey',
            'duplicatePriority',
            'createdAt',
        );
    }
}
