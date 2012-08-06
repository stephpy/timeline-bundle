<?php

namespace Highco\TimelineBundle\Entity;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Mapping\MappingException;
use Highco\TimelineBundle\Model\TimelineActionInterface;

/**
 * TimelineActionListener
 *
 * @uses EventSubscriber
 * @author Francisco Facioni <fran6co@gmail.com>
 * @author Stephane PY <py.stephane1@gmail.com>
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

            $this->buildReference($entity, 'Subject', $em);
            $this->buildReference($entity, 'DirectComplement', $em);
            $this->buildReference($entity, 'IndirectComplement', $em);
        }
    }

    /**
     * build one reference for an entity
     *
     * @param object        $entity entity to add reference
     * @param string        $name   name (Subject, DirectComplement, IndirectComplement)
     * @param EntityManager $em     em
     */
    protected function buildReference($entity, $name, EntityManager $em)
    {
        $modelMethod     = sprintf('get%sModel', $name);
        $model           = $entity->{$modelMethod}();

        $idMethod        = sprintf('get%sId', $name);
        $id              = $entity->{$idMethod}();

        $getObjectMethod = sprintf('get%s', $name);

        if (null !== $model && null !== $id && !is_object($entity->{$getObjectMethod}())) {
            $setObjectMethod = sprintf('set%s', $name);

            try {
                $entity->{$setObjectMethod}($em->getReference($model, $id));
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
