<?php

namespace Spy\TimelineBundle\Driver\Redis;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Predis\Client as PredisClient;
use Snc\RedisBundle\Client\Phpredis\Client as PhpredisClient;
use Spy\TimelineBundle\Driver\AbstractActionManager;
use Spy\Timeline\Driver\ActionManagerInterface;
use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Model\ComponentInterface;

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
     * @var PredisClient|PhpredisClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $prefix;

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
     * @param PredisClient|PhpredisClient $client               client
     * @param string                      $prefix               prefix
     * @param string                      $actionClass          actionClass
     * @param string                      $componentClass       componentClass
     * @param string                      $actionComponentClass actionComponentClass
     */
    public function __construct($client, $prefix, $actionClass, $componentClass, $actionComponentClass)
    {
        if (!$client instanceof PredisClient && !$client instanceof PhpredisClient) {
            throw new \InvalidArgumentException('You have to give a PhpRedisClient or a PredisClient');
        }

        $this->client               = $client;
        $this->prefix               = $prefix;
        $this->actionClass          = $actionClass;
        $this->componentClass       = $componentClass;
        $this->actionComponentClass = $actionComponentClass;
    }

    /**
     * @param array $ids ids
     *
     * @return array
     */
    public function findActionsForIds(array $ids)
    {
        if (empty($ids)) {
            return array();
        }

        $datas = $this->client->hmget($this->getActionKey(), $ids);

        return array_values(array_map(function($v) {
            return unserialize($v);
        }, $datas));
    }

    /**
     * {@inheritdoc}
     */
    public function findActionsWithStatusWantedPublished($limit = 100)
    {
        throw new \Exception('Method '.__METHOD__.' is currently not supported by redis driver');
    }

    /**
     * {@inheritdoc}
     */
    public function countActions(ComponentInterface $subject, $status = ActionInterface::STATUS_PUBLISHED)
    {
        throw new \Exception('Method '.__METHOD__.' is currently not supported by redis driver');
    }

    /**
     * {@inheritdoc}
     */
    public function getSubjectActions(ComponentInterface $subject, array $options = array())
    {
        throw new \Exception('Method '.__METHOD__.' is currently not supported by redis driver');
    }

    /**
     * {@inheritdoc}
     */
    public function updateAction(ActionInterface $action)
    {
        $action->setId($this->getNextId());

        $this->client->hset($this->getActionKey(), $action->getId(), serialize($action));

        $this->deployActionDependOnDelivery($action);
    }

    /**
     * {@inheritdoc}
     */
    public function findOrCreateComponent($model, $identifier = null, $flush = true)
    {
        return $this->createComponent($model, $identifier, $flush);
    }

    /**
     * {@inheritdoc}
     */
    public function createComponent($model, $identifier = null, $flush = true)
    {
        list ($model, $identifier, $data) = $this->resolveModelAndIdentifier($model, $identifier);

        if (empty($model) || empty($identifier)) {
            return null;
        }

        // we do not persist component on redis driver.
        $component = new $this->componentClass();
        $component->setModel($model);
        $component->setIdentifier($identifier);
        $component->setData($data);

        return $component;
    }

    /**
     * {@inheritdoc}
     */
    public function flushComponents()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function findComponents(array $concatIdents)
    {
        $components = array();

        foreach ($concatIdents as $concatIdent) {
            $components[] = call_user_func_array(array($this->componentClass, 'createFromHash'), array($concatIdent));
        }

        return $components;
    }

    /**
     * @return integer|double
     */
    protected function getNextId()
    {
        return ($this->client->hlen($this->getActionKey()) + 1);
    }

    /**
     * @return string
     */
    protected function getActionKey()
    {
        return sprintf('%s:action', $this->prefix);
    }

    /**
     * @param string       $model      model
     * @param string|array $identifier identifier
     *
     * @return array
     */
    protected function resolveModelAndIdentifier($model, $identifier)
    {
        if (!is_object($model) && empty($identifier)) {
            throw new \LogicException('Model has to be an object or a scalar + an identifier in 2nd argument');
        }

        $data = null;
        if (is_object($model)) {
            $data       = $model;
            $modelClass = get_class($model);

            if (!method_exists($model, 'getId')) {
                throw new \LogicException('Model must have a getId method.');
            }

            $identifier = $model->getId();
            $model      = $modelClass;
        }

        if (is_scalar($identifier)) {
            $identifier = (string) $identifier;
        } elseif (!is_array($identifier)) {
            throw new \InvalidArgumentException('Identifier has to be a scalar or an array');
        }

        return array($model, $identifier, $data);
    }
}
