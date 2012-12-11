<?php

namespace Spy\TimelineBundle\Driver\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityNotFoundException as ORMNotFoundException;
use Doctrine\ORM\Mapping\MappingException as ORMMappingException;
use Doctrine\ODM\MongoDB\DocumentNotFoundException as ODMNotFoundException;
use Doctrine\ODM\MongoDB\Mapping\MappingException as ODMMappingException;
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
        } catch (ORMNotFoundException $e) {
            // if entity has been deleted ...
        } catch (ODMNotFoundException $e) {
            // for odm ...
        } catch (ORMMappingException $e) {
            // if entity is not a valid entity or mapped super class
        } catch (ODMMappingException $e) {
            // for odm ...
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
