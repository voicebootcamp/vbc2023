<?php

namespace QuixNxt\FormEngine\Transformers;

use QuixNxt\FormEngine\Contracts\ControlTransformer;

class DatePickerTransformer extends ControlTransformer
{
    /**
     * Get date picker type.
     *
     * @param        $config
     * @param string $type
     *
     * @return string
     *
     * @since 3.0.0
     */
    public function getType($config, $type = ""): string
    {
        return "date";
    }
}
