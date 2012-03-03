<?php

namespace Highco\TimelineBundle\Entity;

use Highco\TimelineBundle\Model\TimelineAction as BaseTimelineAction;

/**
 * @uses BaseTimelineAction
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class TimelineAction extends BaseTimelineAction
{
    /**
     * @param string $class
     */
    public function setSubjectModel($class)
    {
        parent::setSubjectModel($this->exceedDoctrineORMProxy($class));
    }

    /**
     * @param string $class
     */
    public function setDirectComplementModel($class)
    {
        parent::setDirectComplementModel($this->exceedDoctrineORMProxy($class));
    }

    /**
     * @param string $class
     */
    public function setIndirectComplementModel($class)
    {
        parent::setIndirectComplementModel($this->exceedDoctrineORMProxy($class));
    }

    /**
     * Because proxy of doctrine ORM are boring and spread supports will be impacted if we do not use it.
     *
     * @param string $class
     *
     * @return string
     */
    public function exceedDoctrineORMProxy($class)
    {
        if (empty($class)) {
            return $class;
        }

        $reflectionClass = new \ReflectionClass($class);
        if ($reflectionClass->implementsInterface('\Doctrine\ORM\Proxy\Proxy')) {
            $reflectionClass = $reflectionClass->getParentClass();
        }

        return $reflectionClass->getName();
    }
}
