<?php

namespace TBela\CSS\Property;

use TBela\CSS\Interfaces\RenderableInterface;
use TBela\CSS\Value;

/**
 * Comment property class
 * @package TBela\CSS\Property
 */
class Comment extends Property {

    /**
     * @var string
     * @ignore
     */
    protected $type = 'Comment';

    /**
     * PropertyComment constructor.
     * @param array | string $value
     */
    public function __construct($value)
    {

        $this->setValue($value);
    }

    public function getName($vendor = false) {

        return null;
    }

    /**
     * Set the value
     * @param array | string $value
     * @return $this
     */
    public function setValue($value) {

        $this->value = $value;
        return $this;
    }

    /**
     * Return the object value
     * @return string
     */
    public function getValue() {

        return $this->value;
    }

    /**
     * Converty this object to string
     * @param array $options
     * @return string
     */
    public function render (array $options = []) {

        if (!empty($options['remove_comments'])) {

            return '';
        }

        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function setTrailingComments(array $comments = null)
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTrailingComments()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function setLeadingComments(array $comments = null)
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLeadingComments()
    {
        return null;
    }
}