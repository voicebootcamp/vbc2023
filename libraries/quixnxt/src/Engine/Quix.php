<?php

namespace QuixNxt\Engine;

use QuixNxt\Engine\Contracts\QuixData;
use QuixNxt\Engine\Contracts\ViewEngine;
use QuixNxt\Engine\Support\QuixNode;

class Quix
{
    /**
     * @var \QuixNxt\Engine\Contracts\ViewEngine
     */
    private $engine;

    public function __construct(ViewEngine $engine)
    {
        $this->engine = $engine;
    }

    /**
     * Render a given data array into HTML
     *
     *
     * @param  \QuixNxt\Engine\Contracts\QuixData  $data
     *
     * @return \QuixNxt\Engine\Support\QuixNode
     */
    public function render(QuixData $data): QuixNode
    {
        return $this->engine->render($data);
    }
}
