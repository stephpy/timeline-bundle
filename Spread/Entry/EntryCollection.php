<?php

namespace Spy\TimelineBundle\Spread\Entry;

use Spy\Timeline\Driver\ActionManagerInterface;

/**
 * A collection of entry
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class EntryCollection implements \IteratorAggregate
{
    /**
     * @var ActionManagerInterface
     */
    protected $actionManager;

    /**
     * @var \ArrayIterator
     */
    protected $coll;

    /**
     * @var boolean
     */
    protected $duplicateOnGlobal = true;

    /**
     * @var integer
     */
    protected $batchSize;

    /**
     * @param boolean $duplicateOnGlobal Each timeline action are automatically pushed on Global context
     * @param integer $batchSize         batch size
     */
    public function __construct($duplicateOnGlobal = true, $batchSize = 50)
    {
        $this->coll              = new \ArrayIterator();
        $this->duplicateOnGlobal = $duplicateOnGlobal;
        $this->batchSize         = (int) $batchSize;
    }

    /**
     * @param ActionManagerInterface $actionManager actionManager
     */
    public function setActionManager(ActionManagerInterface $actionManager)
    {
        $this->actionManager = $actionManager;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->coll;
    }

    /**
     * @param EntryInterface $entry   entry you want to push
     * @param string         $context context where you want to push
     */
    public function add(EntryInterface $entry, $context = 'GLOBAL')
    {
        if (!isset($this->coll[$context])) {
            $this->coll[$context] = array();
        }

        $this->coll[$context][$entry->getIdent()] = $entry;

        if ($this->duplicateOnGlobal && $context !== 'GLOBAL') {
            $this->add($entry);
        }
    }

    /**
     * Load unaware entries, instead of having 1 call by entry to fetch component
     * you can add unaware entries. Component will be created or exception
     * will be throwed if unexists
     *
     *
     * @return void
     */
    public function loadUnawareEntries()
    {
        if (!$this->actionManager) {
            return;
        }

        $unawareEntries = array();

        foreach ($this->coll as $context => $entries) {
            foreach ($entries as $entry) {
                if ($entry instanceof EntryUnaware) {
                    $unawareEntries[$entry->getIdent()] = $entry->getIdent();
                }
            }
        }

        if (empty($unawareEntries)) {
            return;
        }

        $components = $this->actionManager->findComponents($unawareEntries);
        $componentsIndexedByIdent = array();
        foreach ($components as $component) {
            $componentsIndexedByIdent[$component->getHash()] = $component;
        }

        unset($components);

        $nbComponentCreated = 0;
        foreach ($this->coll as $context => $entries) {
            foreach ($entries as $entry) {
                if ($entry instanceof EntryUnaware) {
                    $ident = $entry->getIdent();
                    // component fetched from database.
                    if (array_key_exists($ident, $componentsIndexedByIdent)) {
                        $entry->setSubject($componentsIndexedByIdent[$ident]);
                    } else {
                        if ($entry->isStrict()) {
                            throw new \Exception(sprintf('Component with ident "%s" is unknown', $entry->getIdent()));
                        }

                        // thrid argument make component not flushed directly.
                        $component = $this->actionManager->createComponent($entry->getSubjectModel(), $entry->getSubjectId(), false);

                        $nbComponentCreated++;

                        if (($nbComponentCreated % $this->batchSize) == 0) {
                            $this->actionManager->flushComponents();
                        }

                        if (null === $component) {
                            throw new \Exception(sprintf('Component with ident "%s" cannot be created', $entry->getIdent()));
                        }

                        $entry->setSubject($component);
                        $componentsIndexedByIdent[$component->getHash()] = $component;
                    }
                }
            }
        }

        if ($nbComponentCreated > 0) {
            $this->actionManager->flushComponents();
        }
    }

    /**
     * @return \ArrayIterator
     */
    public function getEntries()
    {
        return $this->coll;
    }

    /**
     * Clear entries
     */
    public function clear()
    {
        $this->coll = new \ArrayIterator();
    }
}
