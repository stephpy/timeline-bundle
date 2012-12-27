<?php

namespace Spy\TimelineBundle\Driver\ORM;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Model\ComponentInterface;
use Spy\Timeline\Driver\ActionManagerInterface;
use Spy\TimelineBundle\Driver\Doctrine\AbstractActionManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\Expr;

/**
 * ActionManager
 *
 * @uses AbstractActionManager
 * @uses ActionManagerInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class ActionManager extends AbstractActionManager implements ActionManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findActionsWithStatusWantedPublished($limit = 100)
    {
        return $this->objectManager
            ->getRepository($this->actionClass)
            ->createQueryBuilder('a')
            ->where('a.statusWanted = :status')
            ->setParameter('status', ActionInterface::STATUS_PUBLISHED)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
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
            ->getSingleScalarResult();
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
            ->setParameter('status', $options['status']);

        return $this->resultBuilder->fetchResults($qb, $options['page'], $options['max_per_page'], $options['filter'], $options['paginate']);
    }

    /**
     * {@inheritdoc}
     */
    public function findOrCreateComponent($model, $identifier = null, $flush = true)
    {
        list ($modelResolved, $identifierResolved, $data) = $this->resolveModelAndIdentifier($model, $identifier);

        if (empty($modelResolved) || null === $identifierResolved || '' === $identifierResolved) {
            if (is_array($identifierResolved)) {
                $identifierResolved = implode(', ', $identifierResolved);
            }

            throw new \Exception(sprintf('To find a component, you have to give a model (%s) and an identifier (%s)', $modelResolved, $identifierResolved));
        }

        $component = $this->getComponentRepository()
            ->createQueryBuilder('c')
            ->where('c.model = :model')
            ->andWhere('c.identifier = :identifier')
            ->setParameter('model', $modelResolved)
            ->setParameter('identifier', serialize($identifierResolved))
            ->getQuery()
            ->getOneOrNullResult()
            ;

        if ($component) {
            $component->setData($data);

            return $component;
        }

        return $this->createComponent($model, $identifier, $flush);
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

        return $this->getComponentRepository()
            ->createQueryBuilder('c')
            ->where(
                $qb->expr()->in('c.hash', $hashes)
            )
            ->getQuery()
            ->getResult();
    }

    protected function getQueryBuilderForSubject(ComponentInterface $subject)
    {
        return $this->objectManager
            ->getRepository($this->actionClass)
            ->createQueryBuilder('a')
            ->innerJoin('a.actionComponents', 'ac2', Expr\Join::WITH, '(ac2.action = a AND ac2.component = :subject AND ac2.type = :type)')
            ->leftJoin('a.actionComponents', 'ac')
            ->setParameter('subject', $subject)
            ->setParameter('type', 'subject')
            ;
    }

    protected function getComponentRepository()
    {
        return $this->objectManager->getRepository($this->componentClass);
    }
}
