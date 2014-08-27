<?php

namespace Spy\TimelineBundle\Driver\ORM\QueryBuilder;

use Spy\Timeline\Driver\QueryBuilder\Criteria\Asserter;
use Spy\TimelineBundle\Driver\ORM\QueryBuilder\Criteria\CriteriaCollection;

class AsserterVisitor implements VisitorInterface
{
    /**
     * @var string
     */
    protected $dql;

    /**
     * {@inheritdoc}
     */
    public function visit($asserter, CriteriaCollection $criteriaCollection)
    {
        if (!$asserter instanceof Asserter) {
            throw new \Exception('AsserterVisitor accepts only Asserter instance');
        }

        $this->dql = $criteriaCollection->addFromAsserter($asserter)->getDql();
    }

    /**
     * {@inheritdoc}
     */
    public function getDql()
    {
        return $this->dql;
    }
}
