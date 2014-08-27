<?php

namespace Spy\TimelineBundle\Driver\ORM;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Model\ComponentInterface;
use Spy\Timeline\Driver\ActionManagerInterface;
use Spy\TimelineBundle\Driver\Doctrine\AbstractActionManager;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

class ActionManager extends AbstractActionManager implements ActionManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findActionsWithStatusWantedPublished($limit = 100)
    {
        return $this->getQueryBuilderForAction()
            ->where('a.statusWanted = :status')
            ->setParameter('status', ActionInterface::STATUS_PUBLISHED)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
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
            ->select('count(a)')
            ->andWhere('a.statusCurrent = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult()
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
            'paginate'     => false,
        ));

        $options = $resolver->resolve($options);

        $qb = $this->getQueryBuilderForSubject($subject)
            ->select('a, ac, c')
            ->leftJoin('ac.component', 'c')
            ->orderBy('a.createdAt', 'DESC')
            ->andWhere('a.statusCurrent = :status')
            ->setParameter('status', $options['status'])
        ;

        return $this->resultBuilder->fetchResults($qb, $options['page'], $options['max_per_page'], $options['filter'], $options['paginate']);
    }

    /**
     * {@inheritdoc}
     */
    public function findOrCreateComponent($model, $identifier = null, $flush = true)
    {
        $resolvedComponentData = $this->resolveModelAndIdentifier($model, $identifier);

        $component = $this->getComponentRepository()
            ->createQueryBuilder('c')
            ->where('c.model = :model')
            ->andWhere('c.identifier = :identifier')
            ->setParameter('model', $resolvedComponentData->getModel())
            ->setParameter('identifier', serialize($resolvedComponentData->getIdentifier()))
            ->getQuery()
            ->getOneOrNullResult()
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
        return $this->getComponentRepository()
            ->createQueryBuilder('c')
            ->where('c.hash = :hash')
            ->setParameter('hash', $hash)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findComponents(array $hashes)
    {
        if (empty($hashes)) {
            return array();
        }

        $qb = $this->getComponentRepository()->createQueryBuilder('c');

        return $qb->where(
                $qb->expr()->in('c.hash', $hashes)
            )
            ->getQuery()
            ->getResult()
        ;
    }

    protected function getQueryBuilderForSubject(ComponentInterface $subject)
    {
        return $this->getQueryBuilderForComponent($subject, 'subject');
    }

    /**
     * @param ComponentInterface $component component
     * @param string             $type      type
     *
     * @return QueryBuilder
     */
    public function getQueryBuilderForComponent(ComponentInterface $component, $type = null)
    {
        $qb = $this->getQueryBuilderForAction();

        if (null === $type) {
            $qb->innerJoin('a.actionComponents', 'ac2', Expr\Join::WITH, '(ac2.action = a AND ac2.component = :component)');
        } else {
            $qb->innerJoin('a.actionComponents', 'ac2', Expr\Join::WITH, '(ac2.action = a AND ac2.component = :component and ac2.type = :type)')
                ->setParameter('type', $type)
            ;
        }

        return $qb
            ->leftJoin('a.actionComponents', 'ac')
            ->setParameter('component', $component->getId())
        ;
    }

    /**
     * @param array $component Componentinterface
     *
     * @return QueryBuilder
     */
    public function getQueryBuilderForComponents(array $components)
    {
        $qb = $this->getQueryBuilderForAction();

        $c = 1;
        foreach ($components as $type => $component) {
            if (null === $type) {
                $qb->innerJoin('a.actionComponents', 'ac'.$c, Expr\Join::WITH, '(ac'.$c.'.action = a AND ac'.$c.'.component = :component'.$c.')');
            } else {
                $qb->innerJoin('a.actionComponents', 'ac'.$c, Expr\Join::WITH, '(ac'.$c.'.action = a AND ac'.$c.'.component = :component'.$c.' and ac'.$c.'.type = :type'.$c.')')
                 ->setParameter('type'.$c, $type);
            }
            $qb->setParameter('component'.$c, $component->getId());
            $c++;
        }

        return $qb->leftJoin('a.actionComponents', 'ac');
    }

    protected function getComponentRepository()
    {
        return $this->objectManager->getRepository($this->componentClass);
    }

    /**
     * Gets the query builder for an action.
     *
     * @return QueryBuilder
     */
    protected function getQueryBuilderForAction()
    {
        return $this->objectManager
            ->getRepository($this->actionClass)
            ->createQueryBuilder('a')
        ;
    }
}
