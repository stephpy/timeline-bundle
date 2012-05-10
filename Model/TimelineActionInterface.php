<?php

namespace Highco\TimelineBundle\Model;

use Symfony\Component\HttpFoundation\Request;

/**
 * TimelineActionInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface TimelineActionInterface
{
    /**
     * @return timestamp
     */
    public function getSpreadTime();

    /**
     * @return boolean
     */
    public function isPublished();

    /**
     * @return boolean
     */
    public function hasDuplicateKey();

    /**
     * @param boolean $duplicated
     */
    public function setIsDuplicated($duplicated);

    /**
     * @return boolean
     */
    public function isDuplicated();

    /**
     * @param Request $request
     *
     * @return TimelineAction
     */
    static public function fromRequest(Request $request);

    /**
     * @return integer
     */
    public function getId();

    /**
     * Chuck Norris comments Fight 1337 of Vic Mc Key
     *
     * @param object $subject            The subject of the timeline action (Chuck Norris)
     * @param string $verb               The verb (comments)
     * @param object $directComplement   The direct complement (optional) (fight 1337)
     * @param object $indirectComplement The indirect complement (optional) (Vic Mc Key)
     */
    public function create($subject, $verb, $directComplement = null, $indirectComplement = null);

    /**
     * @param object $subject
     */
    public function setSubject($subject);

    /**
     * @return object|null
     */
    public function getSubject();

    /**
     * @param string $subjectModel
     */
    public function setSubjectModel($subjectModel);

    /**
     * @return string
     */
    public function getSubjectModel();

    /**
     * @param integer $subjectId
     */
    public function setSubjectId($subjectId);

    /**
     * @return integer
     */
    public function getSubjectId();

    /**
     * @param string $verb
     */
    public function setVerb($verb);

    /**
     * @return string
     */
    public function getVerb();

    /**
     * @param object $directComplement
     */
    public function setDirectComplement($directComplement);

    /**
     * @return object|null
     */
    public function getDirectComplement();

    /**
     * @param string $directComplementText
     */
    public function setDirectComplementText($directComplementText);

    /**
     * @return string
     */
    public function getDirectComplementText();

    /**
     * @param string $directComplementModel
     */
    public function setDirectComplementModel($directComplementModel);

    /**
     * @return string
     */
    public function getDirectComplementModel();

    /**
     * @param integer $directComplementId
     */
    public function setDirectComplementId($directComplementId);

    /**
     * @return integer
     */
    public function getDirectComplementId();

    /**
     * @param object $indirectComplement
     */
    public function setIndirectComplement($indirectComplement);

    /**
     * @return object|null
     */
    public function getIndirectComplement();

    /**
     * @param string $indirectComplementText
     */
    public function setIndirectComplementText($indirectComplementText);

    /**
     * @return string
     */
    public function getIndirectComplementText();

    /**
     * @param string $indirectComplementModel
     */
    public function setIndirectComplementModel($indirectComplementModel);

    /**
     * @return string
     */
    public function getIndirectComplementModel();

    /**
     * @param integer $indirectComplementId
     */
    public function setIndirectComplementId($indirectComplementId);

    /**
     * @return integer
     */
    public function getIndirectComplementId();

    /**
     * @param string $status
     *
     * @return boolean
     */
    public function isValidStatus($status);

    /**
     * @param string $statusCurrent
     */
    public function setStatusCurrent($statusCurrent);

    /**
     * @return string
     */
    public function getStatusCurrent();

    /**
     * @param string $statusWanted
     */
    public function setStatusWanted($statusWanted);

    /**
     * @return string
     */
    public function getStatusWanted();

    /**
     * @param string $duplicateKey
     */
    public function setDuplicateKey($duplicateKey);

    /**
     * @return string
     */
    public function getDuplicateKey();

    /**
     * @param integer $duplicatePriority
     */
    public function setDuplicatePriority($duplicatePriority);

    /**
     * @return integer
     */
    public function getDuplicatePriority();

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt($createdAt);

    /**
     * @return DateTime
     */
    public function getCreatedAt();
}
