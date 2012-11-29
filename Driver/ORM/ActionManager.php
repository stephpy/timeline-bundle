<?php

namespace Spy\TimelineBundle\Driver\ORM;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Spy\TimelineBundle\Model\ActionInterface;
use Spy\TimelineBundle\Model\ComponentInterface;
use Spy\TimelineBundle\Driver\ActionManagerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\Expr;

/**
 * ActionManager
 *
 * @uses ActionManagerInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class ActionManager implements ActionManagerInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $actionClass;

    /**
     * @var string
     */
    protected $componentClass;

    /**
     * @var string
     */
    protected $actionComponentClass;

    /**
     * @param ObjectManager $objectManager        objectManager
     * @param string        $actionClass          actionClass
     * @param string        $componentClass       componentClass
     * @param string        $actionComponentClass actionComponentClass
     */
    public function __construct(ObjectManager $objectManager, $actionClass, $componentClass, $actionComponentClass)
    {
        $this->objectManager        = $objectManager;
        $this->actionClass          = $actionClass;
        $this->componentClass       = $componentClass;
        $this->actionComponentClass = $actionComponentClass;
    }

    /**
     * {@inheritdoc}
     */
    public function findActionsForIds(array $ids)
    {
        if (empty($ids)) {
            return array();
        }

        $qb = $this->objectManager
            ->getRepository($this->actionClass)
            ->createQueryBuilder('ta');

        $qb
            ->add('where', $qb->expr()->in('a.id', '?1'))
            ->orderBy('a.createdAt', 'DESC')
            ->setParameter(1, $ids);

        return $qb->getQuery()->getResult();
    }

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
            'offset'  => 0,
            'limit'   => 10,
            'status'  => ActionInterface::STATUS_PUBLISHED,
        ));

        $options = $resolver->resolve($options);

        return $this->getQueryBuilderForSubject($subject)
            ->orderBy('a.createdAt', 'DESC')
            ->andWhere('a.statusCurrent = :status')
            ->setParameter('status', $options['status'])
            ->setFirstResult($options['offset'])
            ->setMaxResults($options['limit'])
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function updateAction(ActionInterface $action)
    {
        $this->objectManager->persist($action);
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function create($subject, $verb, array $components = array())
    {
        $action = new $this->actionClass();
        $action->setVerb($verb);

        // subject is MANDATORY. Cannot pass scalar value.
        if (!$subject instanceof ComponentInterface) {
            if (!is_object($subject)) {
                $subject = $this->findOrCreateComponent($subject);
            }

            if (null === $subject) {
                throw new \Exception('Impossible to create component from subject.');
            }
        }

        $action->setSubject($subject, $this->actionComponentClass);

        foreach ($components as $type => $component) {
            if (!$component instanceof ComponentInterface && !is_scalar($component)) {
                $component = $this->findOrCreateComponent($component);

                if (null === $component) {
                    throw new \Exception(sprintf('Impossible to create component from %s.', $type));
                }
            }

            $action->addComponent($type, $component, $this->actionComponentClass);
        }

        return $action;
    }

    /**
     * {@inheritdoc}
     */
    public function findOrCreateComponent($model, $identifier = null)
    {
        list ($model, $identifier) = $this->clearModelAndIdentifier($model, $identifier);

        if (empty($model) || empty($identifier)) {
            return null;
        }

        $component = $this->getComponentRepository()
            ->createQueryBuilder('c')
            ->where('c.model = :model')
            ->andWhere('c.identifier = :identifier')
            ->setParameter('model', $model)
            ->setParameter('identifier', serialize($identifier))
            ->getQuery()
            ->getOneOrNullResult()
            ;

        if ($component) {
            return $component;
        }

        return $this->createComponent($model, $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function createComponent($model, $identifier = null)
    {
        list ($model, $identifier) = $this->clearModelAndIdentifier($model, $identifier);

        if (empty($model) || empty($identifier)) {
            return null;
        }

        $component = new $this->componentClass();
        $component->setModel($model);
        $component->setIdentifier($identifier);

        $this->objectManager->persist($component);
        $this->objectManager->flush();

        return $component;
    }

    /**
     * {@inheritdoc}
     */
    public function findComponents(array $concatIdents)
    {
        $qb = $this->getComponentRepository()
            ->createQueryBuilder('c');

        return $qb
            ->where(
                $qb->expr()->in(
                    $qb->expr()->concat('c.model', 'c.identifier'), $concatIdents
                )
            )
            ->getQuery()
            ->getResult();
    }

    protected function clearModelAndIdentifier($model, $identifier)
    {
        if (!is_object($model) && empty($identifier)) {
            throw new \LogicException('Model has to be an object or a scalar + an identifier in 2nd argument');
        }

        if (is_object($model)) {
            if (!method_exists($model, 'getId')) {
                throw new \LogicException('Model must have a getId method.');
            }

            $identifier = $model->getId();
            $model      = Doctrine::unsetProxyClass($model);
        }

        if (is_scalar($identifier)) {
            $identifier = (string) $identifier;
        } elseif (!is_array($identifier)) {
            throw new \InvalidArgumentException('Identifier has to be a scalar or an array');
        }

        return array($model, $identifier);
    }

    protected function getQueryBuilderForSubject(ComponentInterface $subject)
    {
        return $this->objectManager
            ->getRepository($this->actionClass)
            ->createQueryBuilder('a')
            ->innerJoin('a.actionComponents', 'ac', Expr\Join::WITH, '(ac.action = a AND ac.component = :subject AND ac.type = :type)')
            ->setParameter('subject', $subject)
            ->setParameter('type', 'subject');
    }

    protected function getComponentRepository()
    {
        return $this->objectManager->getRepository($this->componentClass);
    }
}
