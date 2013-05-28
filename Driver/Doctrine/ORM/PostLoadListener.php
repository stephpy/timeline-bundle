<?php

namespace Spy\TimelineBundle\Driver\Doctrine\ORM;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityNotFoundException;
use Spy\Timeline\Model\ComponentInterface;

/**
 * PostLoadListener
 *
 * @uses EventSubscriber
 * @author Francisco Facioni <fran6co@gmail.com>
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class PostLoadListener implements EventSubscriber
{
    /**
     * @param LifecycleEventArgs $eventArgs eventArgs
     */
    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();

        if (!$entity instanceof ComponentInterface || null != $entity->getData()) {
            return;
        }

        try {
            $entity->setData(
                $eventArgs->getEntityManager()->getReference(
                    $entity->getModel(),
                    $entity->getIdentifier()
                )
            );
        } catch (EntityNotFoundException $e) {
            // if entity has been deleted ...
        } catch (MappingException $e) {
            // if entity is not a valid entity or mapped super class
        }
    }

    /**
     * @return array<string>
     */
    public function getSubscribedEvents()
    {
        return array('postLoad');
    }
}
