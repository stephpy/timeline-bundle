<?php

namespace Spy\TimelineBundle\Driver\ORM\QueryBuilder;

use Spy\TimelineBundle\Driver\ORM\QueryBuilder\Criteria\CriteriaCollection;

/**
 * VisitorInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface VisitorInterface
{
    /**
     * @param object             $object             object
     * @param CriteriaCollection $criteriaCollection criteria collection
     */
    public function visit($object, CriteriaCollection $criteriaCollection);

    /**
     * @return string
     */
    public function getDql();
}
