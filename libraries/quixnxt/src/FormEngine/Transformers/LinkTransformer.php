<?php

namespace QuixNxt\FormEngine\Transformers;

use QuixNxt\FormEngine\Contracts\ControlTransformer;

class LinkTransformer extends ControlTransformer
{
    /**
     * Get the link value.
     *
     * @param $config
     *
     * @return array
     *
     * @since 3.0.0
     */
    public function getValue($config): array
    {
        $value = (array) $this->get($config, "value", []);

        if (count($value) === 0) {
            return [
                "url" => "",
                "target" => "",
                "nofollow" => false
            ];
        }

      # We do not need any of them that was not defined
        $value = array_pick($value, ["url", "target", "nofollow"], true); //exclusive

        return $value;
    }
}
