<?php

namespace Spy\TimelineBundle\Driver\Doctrine\ValueObject;

/**
 * ResolvedComponentData
 *
 * This value object guards that the resolved model and identifier are valid.
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class ResolvedComponentData
{
    /**
     * The resolved model string.
     *
     * @var string
     */
    private $model;

    /**
     * The resolved identifier.
     *
     * @var mixed
     */
    private $identifier;

    /**
     * The resolved data.
     *
     * @var null|object
     */
    private $data;

    /**
     * @param string      $model      The resolved model
     * @param mixed       $identifier The resolved identifier
     * @param object|null $data       The resolved data
     */
    public function __construct($model, $identifier, $data = null)
    {
        $this->guardValidModel($model);
        $this->guardValidIdentifier($identifier);

        $this->model = $model;
        $this->identifier = $identifier;
        $this->data = $data;
    }

    /**
     * Gets the resolved model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Gets the resolved data.
     *
     * @return null|object
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Gets the resolved identifier.
     *
     * Because of serializing problems we always return scalars as strings
     *
     * @return array|string
     */
    public function getIdentifier()
    {
        if (is_scalar($this->identifier)) {
            return (string) $this->identifier;
        }

        return $this->identifier;
    }

    /**
     * Guard valid model.
     *
     * The model can not be empty and has to be a string.
     *
     * @param string $model
     *
     * @throws \InvalidArgumentException When the model is not a string.
     */
    private function guardValidModel($model)
    {
        if (empty($model)) {
            throw new \InvalidArgumentException('The resolved model can not be empty');
        }

        if (!is_string($model)) {
            throw new \InvalidArgumentException('The resolved model has to be a string');
        }
    }

    /**
     * Guard valid identifier.
     *
     * The identifier can not be empty (but can be zero) and has to be a scalar or array.
     *
     * @param string|array $identifier
     *
     * @throws \InvalidArgumentException
     */
    private function guardValidIdentifier($identifier)
    {
        if (null === $identifier || '' === $identifier) {
            throw new \InvalidArgumentException('No resolved identifier given');
        }

        if (!is_scalar($identifier) && !is_array($identifier)) {
            throw new \InvalidArgumentException('Identifier has to be a scalar or an array');
        }
    }
}
