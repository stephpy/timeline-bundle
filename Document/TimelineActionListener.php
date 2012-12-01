<?php

namespace Highco\TimelineBundle\Document;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Highco\TimelineBundle\Model\TimelineActionInterface;

/**
 * TimelineActionListener
 *
 * @uses EventSubscriber
 * @author Chris Jones <leeked@gmail.com>
 */
class TimelineActionListener implements EventSubscriber
{
    /**
     * @param LifecycleEventArgs $eventArgs eventArgs
     */
    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getDocument();

        if ($entity instanceof TimelineActionInterface) {
            $dm = $eventArgs->getDocumentManager();

            $this->buildReference($entity, 'Subject', $dm);
            $this->buildReference($entity, 'DirectComplement', $dm);
            $this->buildReference($entity, 'IndirectComplement', $dm);
        }
    }

    /**
     * build one reference for an entity
     *
     * @param object        $entity entity to add reference
     * @param string        $name   name (Subject, DirectComplement, IndirectComplement)
     * @param EntityManager $dm     em
     */
    protected function buildReference($entity, $name, EntityManager $dm)
    {
        $modelMethod     = sprintf('get%sModel', $name);
        $model           = $entity->{$modelMethod}();

        $idMethod        = sprintf('get%sId', $name);
        $id              = $entity->{$idMethod}();

        $getObjectMethod = sprintf('get%s', $name);

        if (null !== $model && null !== $id && !is_object($entity->{$getObjectMethod}())) {
            $setObjectMethod = sprintf('set%s', $name);

            try {
                $entity->{$setObjectMethod}($dm->getReference($model, $id));
            } catch (EntityNotFoundException $e) {
                // if entity has been deleted ...
            } catch (MappingException $e) {
                // if entity is not a valid entity or mapped super class
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
