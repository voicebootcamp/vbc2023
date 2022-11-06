<?php

namespace QuixNxt\Engine\Foundation;

use QuixNxt\Engine\Support\ElementBag;

abstract class QuixData implements \QuixNxt\Engine\Contracts\QuixData
{
    /**
     * @var array
     * @since 3.0.0
     */
    protected $elements = [];


    /**
     * @var array
     * @since 3.0.0
     */
    protected $missing = [];

    /**
     * @var \QuixNxt\Engine\Support\ElementBag
     *
     * @since 3.0.0
     */
    private $element_bag;

    /**
     * QuixData constructor.
     *
     * @param  \QuixNxt\Engine\Support\ElementBag  $element_bag
     *
     * @since 3.0.0
     */
    public function __construct(ElementBag $element_bag)
    {
        $this->element_bag = $element_bag;
    }

    /**
     * @param  array  $data
     *
     * @return $this
     *
     * @since 3.0.0
     */
    public function page(array $data): self
    {
        $this->elements = $this->prepare($data);

        return $this;
    }

    /**
     * Returns the array of data as is
     *
     * @return array
     *
     * @since 3.0.0
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @return bool
     * @since 3.0.0
     */
    public function hasMissingElement(): bool
    {
        return count($this->missing) > 0;
    }

    /**
     * @return array
     * @since 3.0.0
     */
    public function getMissingElements(): array
    {
        return array_map(static function (array $node) {
            return $node['slug'];
        }, $this->missing);
    }

    /**
     * Prepare elements array
     *
     * @param  array  $data
     *
     * @return array
     *
     * @since 3.0.0
     */
    private function prepare(array $data): array
    {
        $elements = [];
        foreach ($data as $node) {
            $slug = $node['slug'];

            $element = $this->element_bag->get($slug);

            if ($element) {
                $element = clone $element;

                $element->setData($node);

                if (isset($node['children']) && count($node['children']) > 0) {
                    foreach ($this->prepare($node['children']) as $child) {
                        $element->addChild($child);
                    }
                }

                $elements[] = $element;

            } else {
                $this->missing[] = $node;
            }
        }

        return $elements;
    }
}
