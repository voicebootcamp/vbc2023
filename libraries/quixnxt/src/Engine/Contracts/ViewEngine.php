<?php

namespace QuixNxt\Engine\Contracts;

use QuixNxt\Engine\Support\QuixNode;

interface ViewEngine
{
    /**
     * ViewEngine constructor.
     *
     * @param  \QuixNxt\Engine\Contracts\Renderer  $renderer
     */
    public function __construct(Renderer $renderer);

    /**
     * Render a single node
     *
     * @param  \QuixNxt\Engine\Contracts\QuixData  $data
     *
     * @return \QuixNxt\Engine\Support\QuixNode
     */
    public function render(QuixData $data): QuixNode;
}
