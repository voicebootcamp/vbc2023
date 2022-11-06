<?php

namespace QuixNxt\FormEngine\Transformers;

use QuixNxt\FormEngine\Contracts\ControlTransformer;

class InputRepeaterTransformer extends ControlTransformer
{
    /**
     * Get input repeater type.
     *
     * @param        $config
     * @param string $type
     *
     * @return string
     *
     * @since 3.0.0
     */
    public function getType($config, $type = "text"): string
    {
        return "input-repeater";
    }
}
