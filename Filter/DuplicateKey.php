<?php

namespace Spy\TimelineBundle\Filter;

use Spy\TimelineBundle\Model\Collection;

/**
 * Defined on "Resources/doc/filter.markdown"
 * This filter will unset from collection timeline_actions which
 * has same duplicate_key property
 *
 * @uses AbstractFilter
 * @uses FilterInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class DuplicateKey extends AbstractFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter(Collection $collection)
    {
        $collection->setData('initial_count', count($collection));

        $duplicateKeys = array();

        foreach ($collection as $key => $action) {
            if ($action->hasDuplicateKey()) {
                $currentKey      = $action->getDuplicateKey();
                $currentPriority = $action->getDuplicatePriority();

                if (array_key_exists($currentKey, $duplicateKeys)) {
                    //actual entry has bigger priority
                    if ($currentPriority > $duplicateKeys[$currentKey]['priority']) {
                        $keyToDelete = $duplicateKeys[$currentKey]['key'];

                        $duplicateKeys[$currentKey]['key']      = $key;
                        $duplicateKeys[$currentKey]['priority'] = $currentPriority;
                    } else {
                        $keyToDelete = $key;
                    }

                    $duplicateKeys[$currentKey]['duplicated'] = true;
                    unset($actions[$keyToDelete]);
                } else {
                    $duplicateKeys[$currentKey] = array(
                        'key'        => $key,
                        'priority'   => $currentPriority,
                        'duplicated' => false,
                    );
                }
            }
        }

        foreach ($duplicateKeys as $key => $values) {
            if ($values['duplicated']) {
                $collection[$values['key']]->setIsDuplicated(true);
            }
        }

        return $collection;
    }
}
