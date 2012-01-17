<?php

namespace Highco\TimelineBundle\Entity;

use Highco\TimelineBundle\Model\TimelineAction as BaseTimelineAction;

/**
 * TimelineAction
 *
 * @uses BaseTimelineAction
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class TimelineAction extends BaseTimelineAction
{
    /**
     * setSubjectModel
     *
     * @param string $v
     * @return void
     */
    public function setSubjectModel($v)
    {
        return parent::setSubjectModel($this->exceedDoctrineORMProxy($v));
    }

    /**
     * setDirectComplementModel
     *
     * @param string $v
     * @return void
     */
    public function setDirectComplementModel($v)
    {
        return parent::setDirectComplementModel($this->exceedDoctrineORMProxy($v));
    }

    /**
     * setIndirectComplementModel
     *
     * @param string $v
     * @return void
     */
    public function setIndirectComplementModel($v)
    {
        return parent::setIndirectComplementModel($this->exceedDoctrineORMProxy($v));
    }

    /**
     * exceedDoctrineORMProxy
     *
     * Because proxy of doctrine ORM are boring and spread supports will be impacted if we do not use it.
     *
     * @param string $class
     * @return string
     */
    public function exceedDoctrineORMProxy($class)
    {
        if(empty($class))
        {
            return $class;
        }

        $reflectionClass = new \ReflectionClass($class);
        if($reflectionClass->implementsInterface('\Doctrine\ORM\Proxy\Proxy'))
        {
            $reflectionClass = $reflectionClass->getParentClass();
        }

        return $reflectionClass->getName();
    }
}
