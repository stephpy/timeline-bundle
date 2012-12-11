<?php

namespace Spy\TimelineBundle\Driver\Redis\Pager;

use Spy\Timeline\Driver\ActionManagerInterface;
use Spy\Timeline\Filter\FilterManagerInterface;
use Spy\Timeline\Pager\PagerInterface;

/**
 * Pager
 *
 * @uses PagerInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Pager implements PagerInterface
{
    /**
     * @var FilterManagerInterface
     */
    protected $filterManager;

    /**
     * @var PredisClient|PhpredisClient
     */
    protected $client;

    /**
     * @var ActionManagerInterface
     */
    protected $actionManager;

    /**
     * @param FilterManagerInterface      $filterManager filterManager
     * @param PredisClient|PhpredisClient $client        client
     * @param ActionManagerInterface      $actionManager actionManager
     */
    public function __construct(FilterManagerInterface $filterManager, $client, ActionManagerInterface $actionManager)
    {
        $this->filterManager = $filterManager;
        $this->client        = $client;
        $this->actionManager = $actionManager;
    }


    /**
     * {@inheritdoc}
     */
    public function paginate($target, $page = 1, $limit = 10, $options = array())
    {
        if (!$target instanceof PagerToken) {
            throw new \Exception('Not supported, must give a PagerToken');
        }

        $offset = ($page - 1) * $limit;
        $limit  = $limit - 1; // due to redis

        $ids    = $this->client->zRevRange($target->key, $offset, ($offset + $limit));

        return  $this->actionManager->findActionsForIds($ids);
    }

    /**
     * {@inheritdoc}
     */
    public function filter($pager)
    {
        return $this->filterManager->filter($pager);
    }
}
