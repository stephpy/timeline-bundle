<?php

namespace Spy\TimelineBundle\Driver\ORM;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\QueryBuilder;
use Spy\TimelineBundle\Driver\Doctrine\AbstractTimelineManager;
use Spy\Timeline\Driver\TimelineManagerInterface;
use Spy\Timeline\Model\ComponentInterface;
use Spy\Timeline\Model\TimelineInterface;
use Spy\Timeline\ResultBuilder\Pager\PagerInterface;

/**
 * TimelineManager
 *
 * @uses AbstractTimelineManager
 * @uses TimelineManagerInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class TimelineManager extends AbstractTimelineManager implements TimelineManagerInterface
{
    /**
     * @var array
     */
    protected $delayedQueries = array();

    /**
     * {@inheritdoc}
     */
    public function getTimeline(ComponentInterface $subject, array $options = array())
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array(
            'page'            => 1,
            'max_per_page'    => 10,
            'type'            => TimelineInterface::TYPE_TIMELINE,
            'context'         => 'GLOBAL',
            'filter'          => true,
            'paginate'        => true,
        ));

        $options = $resolver->resolve($options);

        $qb = $this->getActionBaseQueryBuilder($options['type'], $options['context'], $subject)
            ->select('a, ac, c')
            ->leftJoin('a.actionComponents', 'ac')
            ->leftJoin('ac.component', 'c')
            ->orderBy('t.createdAt', 'DESC');

        return $this->resultBuilder->fetchResults($qb, $options['page'], $options['max_per_page'], $options['filter'], $options['paginate']);
    }

    /**
     * {@inheritdoc}
     */
    public function countKeys(ComponentInterface $subject, array $options = array())
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array(
            'type'    => TimelineInterface::TYPE_TIMELINE,
            'context' => 'GLOBAL',
        ));

        $options = $resolver->resolve($options);

        return (int) $this->getTimelineBaseQueryBuilder($options['type'], $options['context'], $subject)
            ->select('COUNT(t)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(ComponentInterface $subject, $actionId, array $options = array())
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array(
            'type'    => TimelineInterface::TYPE_TIMELINE,
            'context' => 'GLOBAL',
        ));

        $options = $resolver->resolve($options);

        $timeline  = $this->getTimelineBaseQueryBuilder($options['type'], $options['context'], $subject)
            ->andWhere('t.action = :action')
            ->setParameter('action', $actionId)
            ->getQuery()
            ->getSingleResult();

        $this->objectManager->remove($timeline);
        // $manager->flush() handled by flush() method
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(ComponentInterface $subject, array $options = array())
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array(
            'type'    => TimelineInterface::TYPE_TIMELINE,
            'context' => 'GLOBAL',
        ));

        $options = $resolver->resolve($options);

        $qb = $this->getTimelineBaseQueryBuilder($options['type'], $options['context'], $subject);
        $qb->delete();

        // Delay query until flush() is called.
        $this->delayedQueries[] = $qb->getQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $results = array();
        $manager = $this->objectManager;
        try {
            $manager->getConnection()->beginTransaction();

            if (!empty($this->delayedQueries)) {
                foreach ($this->delayedQueries as $query) {
                    $results[] = $query->execute();
                }
            }

            $manager->flush();
            $manager->getConnection()->commit();

            $this->delayedQueries = array();
        } catch (\Exception $e) {
            if ($manager->getConnection()->isTransactionActive()) {
                $manager->getConnection()->rollback();
            }
            $manager->close();
            throw $e;
        }

        return $results;
    }

    /**
     * @param string             $type    type
     * @param string             $context context
     * @param ComponentInterface $subject subject
     *
     * @return QueryBuilder
     */
    protected function getTimelineBaseQueryBuilder($type, $context, ComponentInterface $subject)
    {
        $qb = $this->objectManager
            ->getRepository($this->metadata->getClass('timeline'))
            ->createQueryBuilder('t')
            ->innerJoin('t.action', 'a');

        return $this->getBaseQueryBuilder($qb, $type, $context, $subject);
    }

    /**
     * @param string             $type    type
     * @param string             $context context
     * @param ComponentInterface $subject subject
     *
     * @return QueryBuilder
     */
    protected function getActionBaseQueryBuilder($type, $context, ComponentInterface $subject)
    {
        $qb = $this->objectManager
            ->getRepository($this->metadata->getClass('action'))
            ->createQueryBuilder('a')
            ->innerJoin('a.timelines', 't');

        return $this->getBaseQueryBuilder($qb, $type, $context, $subject);
    }

    /**
     * @param QueryBuilder       $qb      qb
     * @param string             $type    type
     * @param string             $context context
     * @param ComponentInterface $subject subject
     *
     * @return QueryBuilder
     */
    protected function getBaseQueryBuilder(QueryBuilder $qb, $type, $context, ComponentInterface $subject)
    {
        if (!$subject->getId()) {
            throw new \InvalidArgumentException('Component must provide an id.');
        }

        return $qb
            ->where('t.type = :type')
            ->andWhere('t.context = :context')
            ->andWhere('t.subject = :subject')
            ->setParameter('type', $type)
            ->setParameter('context', $context)
            ->setParameter('subject', $subject)
            ;
    }
}
