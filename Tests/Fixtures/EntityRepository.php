<?php

namespace Spy\TimelineBundle\Tests\Fixtures;

use Doctrine\ORM\EntityRepository as BaseEntityRepository;

/**
 * EntityRepository
 *
 * @uses EntityRepository
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class EntityRepository extends BaseEntityRepository
{
    /**
     * @return void
     */
    public function getTimelineResultsForModelAndOids()
    {
    }
}
