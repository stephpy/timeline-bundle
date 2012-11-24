<?php

namespace Spy\TimelineBundle\Filter;

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
     * @param array $options
     */
    public function initialize(array $options = array())
    {
        $this->setOptions($options);
    }

    /**
     * {@inheritdoc}
     * @param \Spy\TimelineBundle\Model\Collection $results
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
                    } else {
                        $keyToDelete = $key;
                    }

                    $duplicateKeys[$currentKey]['duplicated'] = true;
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
