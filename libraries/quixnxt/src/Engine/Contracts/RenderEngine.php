<?php

namespace QuixNxt\Engine\Contracts;

use QuixNxt\Engine\Foundation\QuixElement;
use QuixNxt\Engine\Support\QuixNode;

interface RenderEngine
{
  /**
   * Render a provided element
   *
   * @param  \QuixNxt\Engine\Foundation\QuixElement  $element
   *
   * @return \QuixNxt\Engine\Support\QuixNode
   *
   * @since 3.0.0
   */
    public function renderElement(QuixElement $element): QuixNode;

    /**
     * Render a string template
     *
     * @param  string  $template
     *
     * @param  array  $data
     *
     * @return string
     *
     * @since 3.0.0
     */
    public function render(string $template, array $data = []): string;

    /**
     * Register a template with the renderer
     *
     * @param  string  $name
     * @param  string  $template
     *
     * @return void
     *
     * @since 3.0.0
     */
    public function registerTemplate(string $name, string $template): void;
}
