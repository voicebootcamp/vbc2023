<?php

namespace QuixNxt\Engine\Contracts;

interface QuixData
{
    /**
     * Get the data as array as is
     *
     * @return \QuixNxt\Engine\Foundation\QuixElement[]
     *
     * @since 3.0.0
     */
    public function getElements(): array;
}
