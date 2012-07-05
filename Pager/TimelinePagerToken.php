<?php

namespace Highco\TimelineBundle\Pager;

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

    CONST SERVICE_TIMELINE     = "timeline";
    CONST SERVICE_NOTIFICATION = "notification";

    /**
     * @param string  $service      service
     * @param string  $subjectClass subjectClass
     * @param integer $subjectId    subjectId
     */
    public function __construct($service, $subjectClass, $subjectId)
    {
        $this->setService($service);
        $this->subjectClass = $subjectClass;
        $this->subjectId    = $subjectId;
    }

    /**
     * @param string $service service
     */
    public function setService($service)
    {
        if (!in_array($service, array(self::SERVICE_TIMELINE, self::SERVICE_NOTIFICATION))) {
            throw new \InvalidArgumentException('Invalid service, timeline or notification, nothing else');
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
