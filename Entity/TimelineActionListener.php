<?php

namespace Highco\TimelineBundle\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Highco\TimelineBundle\Model\TimelineActionInterface;

/**
 * TimelineActionListener
 *
 * @uses EventSubscriber
 * @author Francisco Facioni <fran6co@gmail.com>
 */
class TimelineActionListener implements EventSubscriber
{
    /**
     * @param LifecycleEventArgs $eventArgs eventArgs
     */
    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();

        if ($entity instanceof TimelineActionInterface) {
            $em = $eventArgs->getEntityManager();

            if (null !== $entity->getSubjectModel() && null !== $entity->getSubjectId()) {

                try {
                    $entity->setSubject($em->getReference($entity->getSubjectModel(), $entity->getSubjectId()));
                } catch (\Exception $e) {
                }
            }

            if (null !== $entity->getDirectComplementModel() && null !== $entity->getDirectComplementId()) {

                try {
                    $entity->setDirectComplement($em->getReference($entity->getDirectComplementModel(), $entity->getDirectComplementId()));
                } catch (\Exception $e) {
                }
            }

            if (null !== $entity->getIndirectComplementModel() && null !== $entity->getIndirectComplementId()) {

                try {
                    $entity->setIndirectComplement($em->getReference($entity->getIndirectComplementModel(), $entity->getIndirectComplementId()));
                } catch (\Exception $e) {
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array('postLoad');
    }
}
