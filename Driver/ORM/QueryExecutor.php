<?php

namespace Spy\TimelineBundle\Driver\ORM;

use Spy\Timeline\ResultBuilder\QueryExecutor\QueryExecutorInterface;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class QueryExecutor implements QueryExecutorInterface
{
    /**
     * @param mixed $query      query
     * @param int   $page       page
     * @param int   $maxPerPage maxPerPage
     *
     * @throws \Exception
     * @return \Traversable
     */
    public function fetch($query, $page = 1, $maxPerPage = 10)
    {
        if (!$query instanceof DoctrineQueryBuilder) {
            throw new \Exception('Not supported yet');
        }

        if ($maxPerPage) {
            $offset = ($page - 1) * (int) $maxPerPage;

            $query
                ->setFirstResult($offset)
                ->setMaxResults($maxPerPage)
            ;
        }

        // use pager even if it's a query executor, to fix number of results returned issue.
        $paginator   = new Paginator($query, true);

        return (array) $paginator->getIterator();
    }
}
