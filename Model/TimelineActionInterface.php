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
     * @param  boolean $duplicated
     * @return void
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
    public static function fromRequest(Request $request);

    /**
     * @return integer
     */
    public function getId();

    /**
     * @param  mixed $id
     * @return void
     */
    public function setId($id);

    /**
     * Chuck Norris comments Fight 1337 of Vic Mc Key
     *
     * @param  object         $subject            The subject of the timeline action (Chuck Norris)
     * @param  string         $verb               The verb (comments)
     * @param  object|null    $directComplement   The direct complement (optional) (fight 1337)
     * @param  object|null    $indirectComplement The indirect complement (optional) (Vic Mc Key)
     * @return TimelineAction
     */
    public static function create($subject, $verb, $directComplement = null, $indirectComplement = null);

    /**
     * @param  object $subject
     * @return void
     */
    public function setSubject($subject);

    /**
     * @return object|null
     */
    public function getSubject();

    /**
     * @param  string $subjectModel
     * @return void
     */
    public function setSubjectModel($subjectModel);

    /**
     * @return string
     */
    public function getSubjectModel();

    /**
     * @param  integer $subjectId
     * @return void
     */
    public function setSubjectId($subjectId);

    /**
     * @return integer
     */
    public function getSubjectId();

    /**
     * @param  string $verb
     * @return void
     */
    public function setVerb($verb);

    /**
     * @return string
     */
    public function getVerb();

    /**
     * @param  object $directComplement
     * @return void
     */
    public function setDirectComplement($directComplement);

    /**
     * @return object|null
     */
    public function getDirectComplement();

    /**
     * @param  string $directComplementText
     * @return void
     */
    public function setDirectComplementText($directComplementText);

    /**
     * @return string
     */
    public function getDirectComplementText();

    /**
     * @param  string $directComplementModel
     * @return void
     */
    public function setDirectComplementModel($directComplementModel);

    /**
     * @return string
     */
    public function getDirectComplementModel();

    /**
     * @param  integer $directComplementId
     * @return void
     */
    public function setDirectComplementId($directComplementId);

    /**
     * @return integer
     */
    public function getDirectComplementId();

    /**
     * @param  object $indirectComplement
     * @return void
     */
    public function setIndirectComplement($indirectComplement);

    /**
     * @return object|null
     */
    public function getIndirectComplement();

    /**
     * @param  string $indirectComplementText
     * @return void
     */
    public function setIndirectComplementText($indirectComplementText);

    /**
     * @return string
     */
    public function getIndirectComplementText();

    /**
     * @param  string $indirectComplementModel
     * @return void
     */
    public function setIndirectComplementModel($indirectComplementModel);

    /**
     * @return string
     */
    public function getIndirectComplementModel();

    /**
     * @param  integer $indirectComplementId
     * @return void
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
     * @param  string $statusCurrent
     * @return void
     */
    public function setStatusCurrent($statusCurrent);

    /**
     * @return string
     */
    public function getStatusCurrent();

    /**
     * @param  string $statusWanted
     * @return void
     */
    public function setStatusWanted($statusWanted);

    /**
     * @return string
     */
    public function getStatusWanted();

    /**
     * @param  string $duplicateKey
     * @return void
     */
    public function setDuplicateKey($duplicateKey);

    /**
     * @return string
     */
    public function getDuplicateKey();

    /**
     * @param  integer $duplicatePriority
     * @return void
     */
    public function setDuplicatePriority($duplicatePriority);

    /**
     * @return integer
     */
    public function getDuplicatePriority();

    /**
     * @param  DateTime $createdAt
     * @return void
     */
    public function setCreatedAt($createdAt);

    /**
     * @return DateTime
     */
    public function getCreatedAt();
}
