<?php

namespace Highco\TimelineBundle\Timeline\Filter;

/**
 * @uses InterfaceFilter
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class DupplicateKey implements InterfaceFilter
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

        $dupplicateKeys = array();

        foreach ($results as $key => $result) {
            if ($result->hasDupplicateKey()) {
                $currentKey      = $result->getDupplicateKey();
                $currentPriority = $result->getDupplicatePriority();

                if (array_key_exists($currentKey, $dupplicateKeys)) {
                    //actual entry has bigger priority
                    if ($currentPriority > $dupplicateKeys[$currentKey]['priority']) {
                        $keyToDelete = $dupplicateKeys[$currentKey]['key'];

                        $dupplicateKeys[$currentKey]['key'] = $key;
                        $dupplicateKeys[$currentKey]['priority'] = $currentPriority;
                        $dupplicateKeys[$currentKey]['dupplicated'] = true;
                    } else {
                        $keyToDelete = $key;
                        $dupplicateKeys[$currentKey]['dupplicated'] = true;
                    }

                    unset($results[$keyToDelete]);
                } else {
                    $dupplicateKeys[$currentKey] = array(
                        'key' => $key,
                        'priority' => $currentPriority,
                        'dupplicated' => false,
                    );
                }
            }
        }

        foreach ($dupplicateKeys as $key => $values) {
            if ($values['dupplicated']) {
                $results[$values['key']]->setIsDupplicated(true);
            }
        }

        return $results;
    }
}
