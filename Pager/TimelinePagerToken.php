<?php

namespace Spy\TimelineBundle\Pager;

/**
 * TimelinePagerToken
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class TimelinePagerToken
{
    protected $service;
    public $subjectClass;
    public $subjectId;
    public $context;
    public $options;

    CONST SERVICE_TIMELINE          = "timeline";
    CONST SERVICE_SUBJECT_TIMELINE  = "subject_timeline";
    CONST SERVICE_NOTIFICATION      = "notification";

    /**
     * @param string  $service      service
     * @param string  $subjectClass subjectClass
     * @param integer $subjectId    subjectId
     * @param string  $context      context
     * @param array   $options      options
     */
    public function __construct($service, $subjectClass, $subjectId, $context='GLOBAL',array $options = array())
    {
        $this->setService($service);
        $this->subjectClass = $subjectClass;
        $this->subjectId    = $subjectId;
        $this->context      = $context;
        $this->options      = $options;
    }

    /**
     * @param string $service service
     */
    public function setService($service)
    {
        if (!in_array($service, array(self::SERVICE_TIMELINE, self::SERVICE_SUBJECT_TIMELINE, self::SERVICE_NOTIFICATION))) {
            throw new \InvalidArgumentException('Invalid service, timeline subject_timeline, or notification, nothing else');
        }

        $this->service = $service;
    }

    /**
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }
}
