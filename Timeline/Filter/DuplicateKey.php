<?php

namespace Highco\TimelineBundle\Timeline\Filter;

/**
 * @uses FilterInterface
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class DuplicateKey implements FilterInterface
{
    /**
     * @param array $results
     *
     * @return array
     */
    public function filter($results)
    {
        if (!$results instanceof \Traversable AND !is_array($results)) {
            return;
        }

        $duplicateKeys = array();

        foreach ($results as $key => $result) {
            if ($result->hasDuplicateKey()) {
                $currentKey      = $result->getDuplicateKey();
                $currentPriority = $result->getDuplicatePriority();

                if (array_key_exists($currentKey, $duplicateKeys)) {
                    //actual entry has bigger priority
                    if ($currentPriority > $duplicateKeys[$currentKey]['priority']) {
                        $keyToDelete = $duplicateKeys[$currentKey]['key'];

                        $duplicateKeys[$currentKey]['key'] = $key;
                        $duplicateKeys[$currentKey]['priority'] = $currentPriority;
                        $duplicateKeys[$currentKey]['duplicated'] = true;
                    } else {
                        $keyToDelete = $key;
                        $duplicateKeys[$currentKey]['duplicated'] = true;
                    }

                    unset($results[$keyToDelete]);
                } else {
                    $duplicateKeys[$currentKey] = array(
                        'key' => $key,
                        'priority' => $currentPriority,
                        'duplicated' => false,
                    );
                }
            }
        }

        foreach ($duplicateKeys as $key => $values) {
            if ($values['duplicated']) {
                $results[$values['key']]->setIsDuplicated(true);
            }
        }

        return $results;
    }
}
