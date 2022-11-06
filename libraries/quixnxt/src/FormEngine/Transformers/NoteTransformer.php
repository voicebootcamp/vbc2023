<?php

namespace QuixNxt\FormEngine\Transformers;

use QuixNxt\FormEngine\Contracts\ControlTransformer;

class NoteTransformer extends ControlTransformer
{
    /**
     * Get code type.
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
        return "note";
    }
}
