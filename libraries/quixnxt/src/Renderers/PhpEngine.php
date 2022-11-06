<?php

namespace QuixNxt\Renderers;

use QuixNxt\Engine\Foundation\RenderEngine;

class PhpEngine extends RenderEngine
{
    /**
     * @inheritDoc
     */
    public function render(string $template, array $data = []): string
    {
        return preg_replace_callback("/{{(.*?)}}/", static function ($matches) use ($data) {
            $key = trim($matches[1]);


            if (array_key_exists($key, $data)) {
                return $data[$key];
            }

            return $matches[0];
        }, $template);
    }
}
