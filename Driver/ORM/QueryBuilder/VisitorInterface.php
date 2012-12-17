<?php

namespace Spy\TimelineBundle\Driver\ORM\QueryBuilder;

/**
 * VisitorInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface VisitorInterface
{
    /**
     * @param object $object object
     */
    public function visit($object);

    /**
     * @return string
     */
    public function getDql();

    /**
     * @return itneger
     */
    public function getNbJoinsNeeded();

    /**
     * @return array
     */
    public function getParameters();
}
