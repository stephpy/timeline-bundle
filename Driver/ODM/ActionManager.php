<?php

namespace Spy\TimelineBundle\Driver\ODM;

use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Model\ComponentInterface;
use Spy\Timeline\Driver\ActionManagerInterface;
use Spy\TimelineBundle\Driver\Doctrine\AbstractActionManager;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionManager extends AbstractActionManager implements ActionManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findActionsWithStatusWantedPublished($limit = 100)
    {
        return $this->objectManager
            ->createQueryBuilder($this->actionClass)
            ->field('statusWanted')->equals(ActionInterface::STATUS_PUBLISHED)
            ->limit($limit)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function countActions(ComponentInterface $subject, $status = ActionInterface::STATUS_PUBLISHED)
    {
        if (!$subject->getId()) {
            throw new \InvalidArgumentException('Subject has to be persisted');
        }

        return (int) $this->getQueryBuilderForSubject($subject)
            ->field('statusCurrent')->equals($status)
            ->eagerCursor(true)
            ->getQuery()
            ->count()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubjectActions(ComponentInterface $subject, array $options = array())
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array(
            'page'         => 1,
            'max_per_page' => 10,
            'status'       => ActionInterface::STATUS_PUBLISHED,
            'filter'       => true,
            'paginate'     => true,
        ));

        $options = $resolver->resolve($options);

        $qb = $this->getQueryBuilderForSubject($subject)
            ->sort('createdAt', 'desc')
            ->field('statusCurrent')->equals($options['status'])
        ;

        return $this->resultBuilder->fetchResults($qb, $options['page'], $options['max_per_page'], $options['filter'], $options['paginate']);
    }

    /**
     * {@inheritdoc}
     */
    public function findOrCreateComponent($model, $identifier = null, $flush = true)
    {
        $resolvedComponentData = $this->resolveModelAndIdentifier($model, $identifier);

        $component = $this->objectManager
            ->createQueryBuilder($this->componentClass)
            ->field('model')->equals($resolvedComponentData->getModel())
            ->field('identifier')->equals($resolvedComponentData->getIdentifier())
            ->getQuery()
            ->getSingleResult()
        ;

        if ($component) {
            $component->setData($resolvedComponentData->getData());

            return $component;
        }

        return $this->createComponentFromResolvedComponentData($resolvedComponentData, $flush);
    }

    /**
     * {@inheritdoc}
     */
    public function findComponentWithHash($hash)
    {
        return $this->objectManager
            ->getRepository($this->componentClass)
            ->createQueryBuilder('c')
            ->field('hash')->equals($hash)
            ->getQuery()
            ->getSingleResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findComponents(array $hashes)
    {
        return $this->objectManager
            ->getRepository($this->componentClass)
            ->createQueryBuilder('c')
            ->field('hash')->in($hashes)
            ->getQuery()
            ->execute()
        ;
    }

    protected function getQueryBuilderForSubject(ComponentInterface $subject)
    {
        return $this->objectManager
            ->getRepository($this->actionClass)
            ->createQueryBuilder('a')
            ->field('subject.id')->equals($subject->getId())
        ;
    }
}
