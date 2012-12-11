<?php

namespace Spy\TimelineBundle\Driver\ODM;

use Doctrine\Common\Persistence\ObjectManager;
use Spy\Timeline\Driver\AbstractTimelineManager;
use Spy\Timeline\Driver\TimelineManagerInterface;
use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Model\Collection;
use Spy\Timeline\Model\ComponentInterface;
use Spy\Timeline\Model\TimelineInterface;
use Spy\Timeline\Pager\PagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->sort('createdAt', 'desc');

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
            ->getQuery()
            ->count();
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

        $this->getBaseQueryBuilder($options['type'], $options['context'], $subject)
            ->field('action.id')->equals($actionId)
            ->remove()
            ->getQuery()
            ->execute();
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

        $this->getBaseQueryBuilder($options['type'], $options['context'], $subject)
            ->remove()
            ->getQuery()
            ->execute();
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
        $this->objectManager->flush();
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
            ->createQueryBuilder('Timeline')
            ->field('type')->equals($type)
            ->field('context')->equals($context)
            ->field('subject.id')->equals($subject->getId())
            ;
    }
}
