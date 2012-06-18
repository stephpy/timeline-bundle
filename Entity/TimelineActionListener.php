<?php

namespace Highco\TimelineBundle\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Highco\TimelineBundle\Model\TimelineActionInterface;

class TimelineActionListener implements EventSubscriber
{
    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();

        if ($entity instanceof TimelineActionInterface) {
            $em = $eventArgs->getEntityManager();

            if (null !== $entity->getSubjectModel() && null !== $entity->getSubjectId()) {
                $entity->setSubject($em->getReference($entity->getSubjectModel(), $entity->getSubjectId()));
            }

            if (null !== $entity->getDirectComplementModel() && null !== $entity->getDirectComplementId()) {
                $entity->setDirectComplement($em->getReference($entity->getDirectComplementModel(), $entity->getDirectComplementId()));
            }

            if (null !== $entity->getIndirectComplementModel() && null !== $entity->getIndirectComplementId()) {
                $entity->setIndirectComplement($em->getReference($entity->getIndirectComplementModel(), $entity->getIndirectComplementId()));
            }
        }
    }

    public function getSubscribedEvents()
    {
        return array('postLoad');
    }
}