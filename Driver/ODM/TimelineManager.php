<?php

namespace Spy\TimelineBundle\Driver\ODM;

use Spy\TimelineBundle\Driver\Doctrine\AbstractTimelineManager;
use Spy\Timeline\Driver\TimelineManagerInterface;
use Spy\Timeline\Model\ComponentInterface;
use Spy\Timeline\Model\TimelineInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Spy\Timeline\ResultBuilder\Pager\PagerInterface;

class TimelineManager extends AbstractTimelineManager implements TimelineManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTimeline(ComponentInterface $subject, array $options = array())
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array(
            'page'              => 1,
            'max_per_page'      => 10,
            'type'              => TimelineInterface::TYPE_TIMELINE,
            'context'           => 'GLOBAL',
            'filter'            => true,
            'group_by_action'   => true,
            'paginate'          => true,
        ));

        $options = $resolver->resolve($options);

        $qb = $this->getBaseQueryBuilder($options['type'], $options['context'], $subject)
            ->sort('createdAt', 'desc')
            ->sort('id', 'desc')
        ;

        $results = $this->resultBuilder->fetchResults($qb, $options['page'], $options['max_per_page'], $options['filter'], $options['paginate']);

        if ($options['group_by_action']) {
            $actions = array();
            foreach ($results as $result) {
                $actions[] = $result->getAction();
            }

            if ($results instanceof PagerInterface) {
                $results->setItems($actions);
            } else {
                $results = $actions;
            }
        }

        return $results;
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
            ->count()
        ;
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
            ->execute()
        ;
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
            ->execute()
        ;
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
