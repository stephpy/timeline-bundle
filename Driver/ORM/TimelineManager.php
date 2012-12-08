<?php

namespace Spy\TimelineBundle\Driver\ORM;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\Common\Persistence\ObjectManager;
use Spy\TimelineBundle\Driver\AbstractTimelineManager;
use Spy\TimelineBundle\Driver\TimelineManagerInterface;
use Spy\TimelineBundle\Model\ActionInterface;
use Spy\TimelineBundle\Model\Collection;
use Spy\TimelineBundle\Model\ComponentInterface;
use Spy\TimelineBundle\Model\TimelineInterface;
use Spy\TimelineBundle\Pager\PagerInterface;

/**
 * TimelineManager
 *
 * @uses TimelineManagerInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class TimelineManager implements TimelineManagerInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var PagerInterface
     */
    protected $pager;

    /**
     * @var string
     */
    protected $timelineClass;

    /**
     * @var array
     */
    protected $delayedQueries = array();

    /**
     * @param ObjectManager  $objectManager objectManager
     * @param PagerInterface $pager         pager
     * @param string         $timelineClass timelineClass
     */
    public function __construct(ObjectManager $objectManager, PagerInterface $pager, $timelineClass)
    {
        $this->objectManager = $objectManager;
        $this->pager         = $pager;
        $this->timelineClass = $timelineClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeline(ComponentInterface $subject, array $options = array())
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array(
            'page'         => 1,
            'max_per_page' => 10,
            'type'         => TimelineInterface::TYPE_TIMELINE,
            'context'      => 'GLOBAL',
            'filter'       => true,
        ));

        $options = $resolver->resolve($options);

        $qb = $this->getBaseQueryBuilder($options['type'], $options['context'], $subject)
            ->innerJoin('t.action', 'a')
            ->leftJoin('a.actionComponents', 'ac')
            ->leftJoin('ac.component', 'c')
            ->orderBy('t.createdAt', 'DESC');

        $pager   = $this->pager->paginate($qb, $options['page'], $options['max_per_page']);
        $results = $pager->getItems();

        $actions = array_map(
            function ($timeline) {
                return $timeline->getAction();
            },
            $results
        );

        $pager->setItems($actions);

        if ($options['filter']) {
            return $this->pager->filter($pager);
        }

        return $pager;
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

        return (int) $this->getBaseQueryBuilder($options['type'], $options['context'], $subject)
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

        $timeline  = $this->getBaseQueryBuilder($options['type'], $options['context'], $subject)
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

        $qb = $this->getBaseQueryBuilder($options['type'], $options['context'], $subject);
        $qb->delete();

        // Delay query until flush() is called.
        $this->delayedQueries[] = $qb->getQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function createAndPersist(ActionInterface $action, ComponentInterface $subject, $context = 'GLOBAL', $type = TimelineInterface::TYPE_TIMELINE)
    {
        $timeline = new $this->timelineClass();

        $timeline->setType($type);
        $timeline->setAction($action);
        $timeline->setContext($context);
        $timeline->setSubject($subject);

        $this->objectManager->persist($timeline);
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
     * @param string             $type
     * @param string             $context
     * @param ComponentInterface $subject
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getBaseQueryBuilder($type, $context, ComponentInterface $subject)
    {
        if (!$subject->getId()) {
            throw new \InvalidArgumentException('Component must provide an id.');
        }

        return $this->objectManager
            ->getRepository($this->timelineClass)
            ->createQueryBuilder('t')
            ->where('t.type = :type')
            ->andWhere('t.context = :context')
            ->andWhere('t.subject = :subject')
            ->setParameter('type', $type)
            ->setParameter('context', $context)
            ->setParameter('subject', $subject)
            ;
    }
}
