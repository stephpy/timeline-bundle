<?php

namespace Highco\TimelineBundle\Model;

use Symfony\Component\HttpFoundation\Request;

/**
 * TimelineAction
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class TimelineAction implements TimelineActionInterface
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
     * {@inheritdoc}
     */
    public function getSpreadTime()
    {
        return time();
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished()
    {
        return $this->statusCurrent == self::STATUS_PUBLISHED;
    }

    /**
     * {@inheritdoc}
     */
    public function hasDuplicateKey()
    {
        return null !== $this->duplicateKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsDuplicated($duplicated)
    {
        $this->duplicated = (bool) $duplicated;
    }

    /**
     * {@inheritdoc}
     */
    public function isDuplicated()
    {
        return (bool) $this->duplicated;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromRequest(Request $request)
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
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public static function create($subject, $verb, $directComplement = null, $indirectComplement = null)
    {
        if (!is_object($subject)) {
            throw new \InvalidArgumentException('Subject should be an object');
        }

        $object = new static();
        $object->setSubjectModel(get_class($subject));
        $object->setSubject($subject);
        $object->setSubjectId($subject->getId());

        $object->setVerb((string) $verb);

        if (!is_object($directComplement)) {
            $object->setDirectComplementText($directComplement);
        } else {
            $object->setDirectComplement($directComplement);
            $object->setDirectComplementModel(get_class($directComplement));
            $object->setDirectComplementId($directComplement->getId());
        }

        if (!is_object($indirectComplement)) {
            $object->setIndirectComplementText($indirectComplement);
        } else {
            $object->setIndirectComplement($indirectComplement);
            $object->setIndirectComplementModel(get_class($indirectComplement));
            $object->setIndirectComplementId($indirectComplement->getId());
        }

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject($subject)
    {
        if (!is_object($subject)) {
            throw new \InvalidArgumentException('direct complement must be an object');
        }

        $this->subject = $subject;
        $this->setSubjectModel(get_class($subject));
        $this->setSubjectId($subject->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubjectModel($subjectModel)
    {
        $this->subjectModel = $subjectModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubjectModel()
    {
        return $this->subjectModel;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubjectId($subjectId)
    {
        $this->subjectId = $subjectId;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubjectId()
    {
        return $this->subjectId;
    }

    /**
     * {@inheritdoc}
     */
    public function setVerb($verb)
    {
        $this->verb = $verb;
    }

    /**
     * {@inheritdoc}
     */
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     * {@inheritdoc}
     */
    public function setDirectComplement($directComplement)
    {
        if (!is_object($directComplement)) {
            throw new \InvalidArgumentException('direct complement must be an object');
        }

        $this->directComplement = $directComplement;
        $this->setDirectComplementModel(get_class($directComplement));
        $this->setDirectComplementId($directComplement->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectComplement()
    {
        if (null !== $this->directComplement) {
            return $this->directComplement;
        }

        return $this->getDirectComplementText();
    }

    /**
     * {@inheritdoc}
     */
    public function setDirectComplementText($directComplementText)
    {
        $this->directComplementText = $directComplementText;
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectComplementText()
    {
        return $this->directComplementText;
    }

    /**
     * {@inheritdoc}
     */
    public function setDirectComplementModel($directComplementModel)
    {
        $this->directComplementModel = $directComplementModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectComplementModel()
    {
        return $this->directComplementModel;
    }

    /**
     * {@inheritdoc}
     */
    public function setDirectComplementId($directComplementId)
    {
        $this->directComplementId = $directComplementId;
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectComplementId()
    {
        return $this->directComplementId;
    }

    /**
     * {@inheritdoc}
     */
    public function setIndirectComplement($indirectComplement)
    {
        if (!is_object($indirectComplement)) {
            throw new \InvalidArgumentException('indirect complement must be an object');
        }

        $this->indirectComplement = $indirectComplement;
        $this->setIndirectComplementModel(get_class($indirectComplement));
        $this->setIndirectComplementId($indirectComplement->getId());

    }

    /**
     * {@inheritdoc}
     */
    public function getIndirectComplement()
    {
        return $this->indirectComplement;
    }

    /**
     * {@inheritdoc}
     */
    public function setIndirectComplementText($indirectComplementText)
    {
        $this->indirectComplementText = $indirectComplementText;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndirectComplementText()
    {
        return $this->indirectComplementText;
    }

    /**
     * {@inheritdoc}
     */
    public function setIndirectComplementModel($indirectComplementModel)
    {
        $this->indirectComplementModel = $indirectComplementModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndirectComplementModel()
    {
        return $this->indirectComplementModel;
    }

    /**
     * {@inheritdoc}
     */
    public function setIndirectComplementId($indirectComplementId)
    {
        $this->indirectComplementId = $indirectComplementId;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getDuplicateKey()
    {
        return $this->duplicateKey;
    }

    /**
     * @param integer $duplicatePriority
     * {@inheritdoc}
     */
    public function setDuplicatePriority($duplicatePriority)
    {
        $this->duplicatePriority = (int) $duplicatePriority;
    }

    /**
     * {@inheritdoc}
     */
    public function getDuplicatePriority()
    {
        return (int) $this->duplicatePriority;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
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
