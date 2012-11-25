<?php

namespace Spy\TimelineBundle\Spread;

use Spy\TimelineBundle\Model\Action;
use Spy\TimelineBundle\Model\ActionInterface;
use Spy\TimelineBundle\Driver\ActionManagerInterface;

/**
 * Deployer
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Deployer
{
    CONST DELIVERY_IMMEDIATE = 'immediate';
    CONST DELIVERY_WAIT      = 'wait';

    /**
     * @var SpreadManager
     */
    protected $spreadManager;

    /**
     * @var ActionManagerInterface
     */
    protected $actionManager;

    /**
     * @var string
     */
    protected $delivery;

    /**
     * @param SpreadManager          $spreadManager spreadManager
     * @param ActionManagerInterface $actionManager actionManager
     */
    public function __construct(SpreadManager $spreadManager, ActionManagerInterface $actionManager)
    {
        $this->spreadManager = $spreadManager;
        $this->actionManager = $actionManager;
    }

    /**
     * @param ActionInterface $action action
     */
    public function deploy(ActionInterface $action)
    {
        if (!$action->getId()) {
            $this->actionManager->updateAction($action);
        }

        if ($action->getStatusWanted() !== Action::STATUS_PUBLISHED) {
            return;
        }

        $results = $this->spreadManager->process($action);

        foreach ($results as $context => $entries) {
            foreach ($entries as $entry) {
                print "<pre>";
                var_dump($context, $entry->getIdent());
                print "</pre>";
            }
        }
        exit('ici');

        print "<pre>";
        var_dump($results);
        print "</pre>";
        exit('ici');

        /*
        foreach ($results as $context => $values) {
            foreach ($values as $entry) {
                $this->provider->persist($timelineAction, $context, $entry->subjectModel, $entry->subjectId);
                $this->notificationManager->notify($timelineAction, $context, $entry->subjectModel, $entry->subjectId);
            }
        }

        $this->provider->flush();

        $timelineAction->setStatusCurrent(TimelineAction::STATUS_PUBLISHED);
        $timelineAction->setStatusWanted(TimelineAction::STATUS_FROZEN);

        $this->timelineActionManager->updateTimelineAction($timelineAction);

        $this->spreadManager->clear();*/


        exit('DEPLOY');
    }

    /**
     * @param string $delivery delivery
     */
    public function setDelivery($delivery)
    {
        $availableDelivery = array(self::DELIVERY_IMMEDIATE, self::DELIVERY_WAIT);

        if (!in_array($delivery, $availableDelivery)) {
            throw new \InvalidArgumentException(sprintf('Delivery "%s" is not supported, (%s)', $delivery, implode(', ', $availableDelivery)));
        }

        $this->delivery = $delivery;
    }
}
