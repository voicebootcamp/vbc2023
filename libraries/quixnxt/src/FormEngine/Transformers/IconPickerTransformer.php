<?php

namespace QuixNxt\FormEngine\Transformers;

class IconPickerTransformer extends TextTransformer
{
    /**
     * Get icon picker type.
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
        return "icon";
    }
}
