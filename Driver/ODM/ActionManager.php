<?php

namespace Spy\TimelineBundle\Driver\ODM;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Spy\TimelineBundle\Model\ActionInterface;
use Spy\TimelineBundle\Model\ComponentInterface;
use Spy\TimelineBundle\Driver\AbstractActionManager;
use Spy\TimelineBundle\Driver\ActionManagerInterface;
use Spy\TimelineBundle\Pager\PagerInterface;
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
     * @param ObjectManager  $objectManager        objectManager
     * @param PagerInterface $pager                pager
     * @param string         $actionClass          actionClass
     * @param string         $componentClass       componentClass
     * @param string         $actionComponentClass actionComponentClass
     */
    public function __construct(ObjectManager $objectManager, PagerInterface $pager, $actionClass, $componentClass, $actionComponentClass)
    {
        $this->objectManager        = $objectManager;
        $this->pager                = $pager;
        $this->actionClass          = $actionClass;
        $this->componentClass       = $componentClass;
        $this->actionComponentClass = $actionComponentClass;
    }

    /**
     * {@inheritdoc}
     */
    public function findActionsWithStatusWantedPublished($limit = 100)
    {
        exit('WIP');
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
        exit('WIP');
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
        exit('WIP');
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array(
            'page'         => 1,
            'max_per_page' => 10,
            'status'       => ActionInterface::STATUS_PUBLISHED,
            'filter'       => true,
        ));

        $options = $resolver->resolve($options);

        $qb = $this->getQueryBuilderForSubject($subject)
            ->sort('createdAt', 'desc')
            ->field('status')->equals($options['status']);

        $results = $qb->getQuery()
            ->execute();

        $pager   = $this->pager->paginate($qb, $options['page'], $options['max_per_page']);

        if ($options['filter']) {
            return $this->pager->filter($pager);
        }

        return $pager;
    }

    /**
     * {@inheritdoc}
     */
    public function updateAction(ActionInterface $action)
    {
        $this->objectManager->persist($action);
        $this->objectManager->flush();

        $this->deployActionDependOnDelivery($action);
    }

    /**
     * {@inheritdoc}
     */
    public function findOrCreateComponent($model, $identifier = null)
    {
        list ($modelResolved, $identifierResolved, $data) = $this->resolveModelAndIdentifier($model, $identifier);

        if (empty($modelResolved) || empty($identifierResolved)) {
            if (is_array($identifierResolved)) {
                $identifierResolved = implode(', ', $identifierResolved);
            }

            throw new \Exception(sprintf('To find a component, you have to give a model (%s) and an identifier (%s)', $modelResolved, $identifierResolved));
        }

        $component = $this->getComponentRepository()
            ->createQueryBuilder('Component')
            ->field('model')->equals($modelResolved)
            ->field('identifier')->equals($identifierResolved)
            ->getQuery()
            ->getSingleResult();

        if ($component) {
            $component->setData($data);

            return $component;
        }

        return $this->createComponent($model, $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function createComponent($model, $identifier = null)
    {
        list ($model, $identifier, $data) = $this->resolveModelAndIdentifier($model, $identifier);

        if (empty($model) || empty($identifier)) {
            if (is_array($identifier)) {
                $identifier = implode(', ', $identifier);
            }

            throw new \Exception(sprintf('To create a component, you have to give a model (%s) and an identifier (%s)', $model, $identifier));
        }

        $component = new $this->componentClass();
        $component->setModel($model);
        $component->setData($data);
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
        return array();
        exit('WIP');
        $qb = $this->getComponentRepository()
            ->createQueryBuilder('c');

        return $qb
            ->where(
                $qb->expr()->in(
                    $qb->expr()->concat('c.model',
                        $qb->expr()->concat($qb->expr()->literal('#'), 'c.identifier'))
                    , $concatIdents
                )
            )
            ->getQuery()
            ->getResult();
    }

    protected function resolveModelAndIdentifier($model, $identifier)
    {
        if (!is_object($model) && empty($identifier)) {
            throw new \LogicException('Model has to be an object or a scalar + an identifier in 2nd argument');
        }

        $data = null;

        if (is_object($model)) {
            $data       = $model;
            $modelClass = get_class($model);
            $metadata   = $this->objectManager->getClassMetadata($modelClass);

            // if object is linked to doctrine
            if (null !== $metadata) {
                $fields     = $metadata->getIdentifier();
                if (!is_array($fields)) {
                    $fields = array($fields);
                }
                $many       = count($fields) > 1;

                $identifier = array();
                foreach ($fields as $field) {
                    $getMethod = sprintf('get%s', ucfirst($field));
                    $value     = (string) $model->{$getMethod}();

                    //Do not use it: https://github.com/stephpy/TimelineBundle/issues/59
                    //$value = (string) $metadata->reflFields[$field]->getValue($model);

                    if (empty($value)) {
                        throw new \Exception(sprintf('Field "%s" of model "%s" return an empty result, model has to be persisted.', $field, $modelClass));
                    }

                    $identifier[$field] = $value;
                }

                if (!$many) {
                    $identifier = current($identifier);
                }

                $model = $metadata->name;
            } else {
                if (!method_exists($model, 'getId')) {
                    throw new \LogicException('Model must have a getId method.');
                }

                $identifier = $model->getId();
                $model      = $modelClass;
            }
        }

        if (is_scalar($identifier)) {
            $identifier = (string) $identifier;
        } elseif (!is_array($identifier)) {
            throw new \InvalidArgumentException('Identifier has to be a scalar or an array');
        }

        return array($model, $identifier, $data);
    }

    protected function getQueryBuilderForSubject(ComponentInterface $subject)
    {
        exit('todo.');
        return $this->objectManager
            ->getRepository($this->actionClass)
            ->createQueryBuilder('a')
            ->field('actionComponents.Component.id')->equals($subject->getId())
            ->field('actionComponents.Component.type')->equals('subject');
            ;
    }

    protected function getComponentRepository()
    {
        return $this->objectManager->getRepository($this->componentClass);
    }
}
