<?php

namespace QuixNxt\FormEngine\Transformers;

class TimePickerTransformer extends TextTransformer
{
    /**
     * Get time picker type.
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
        return "time";
    }
}
