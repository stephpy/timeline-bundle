<?php

namespace Spy\TimelineBundle\Driver\Doctrine\ODM;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Spy\Timeline\Model\ComponentInterface;

class PostLoadListener implements EventSubscriber
{
    /**
     * @param LifecycleEventArgs $eventArgs eventArgs
     */
    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getDocument();

        if (!$entity instanceof ComponentInterface || null != $entity->getData()) {
            return;
        }

        try {
            $entity->setData(
                $eventArgs->getDocumentManager()->getReference(
                    $entity->getModel(),
                    $entity->getIdentifier()
                )
            );
        } catch (DocumentNotFoundException $e) {
            // if document has been deleted ...
        } catch (MappingException $e) {
            // if document is not a valid document or mapped super class
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
