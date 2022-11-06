<?php

namespace QuixNxt\Engine\Support;

use QuixNxt\Engine\Foundation\QuixElement;

class ElementBag
{
    /**
     * Elements store
     *
     * @var array
     *
     * @since 3.0.0
     */
    private $elements = [];


    /**
     * @param  \QuixNxt\Engine\Foundation\QuixElement  $element
     *
     * @since 3.0.0
     */
    public function add(QuixElement $element): void
    {
        $this->elements[$element->getSlug()] = $element;
    }

    /**
     * get array of registered elements
     *
     * @return QuixElement[]
     *
     * @since 3.0.0
     */
    public function all(): array
    {
        return array_values($this->elements);
    }

    /**
     * Get an element by it's slug
     *
     * @param  string  $slug
     *
     * @return \QuixNxt\Engine\Foundation\QuixElement|null
     *
     * @since 3.0.0
     */
    public function get(string $slug): ?QuixElement
    {
        return $this->elements[$slug] ?? null;
    }
}
