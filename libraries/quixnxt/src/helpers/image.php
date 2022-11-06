<?php

use QuixNxt\Image\Optimizer;

if (!function_exists('image')) {
    function image(string $path = null, array $configs = [])
    {
        if (is_null($path)) {
            return Optimizer::getInstance();
        }

        return Optimizer::getInstance($path, $configs);
    }
}
