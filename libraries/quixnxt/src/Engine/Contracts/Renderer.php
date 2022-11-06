<?php

namespace QuixNxt\Engine\Contracts;


use QuixNxt\Engine\Foundation\QuixElement;
use QuixNxt\Engine\Support\QuixNode;

interface Renderer
{
  /**
   * Renderer constructor.
   *
   * @param  \QuixNxt\Engine\Contracts\RenderEngine  $engine
   *
   * @since 3.0.0
   */
  public function __construct(RenderEngine $engine);

  /**
   * @return \QuixNxt\Engine\Contracts\RenderEngine
   *
   * @since 3.0.0
   */
  public function getEngine(): RenderEngine;

  /**
   * Render a page
   *
   * @param  QuixElement[]  $elements
   *
   * @return \QuixNxt\Engine\Support\QuixNode
   * @since 3.0.0
   */
  public function render(array $elements): QuixNode;

  /**
   * Renders a given array of formatted data and gives HTML
   *
   *
   * @param  \QuixNxt\Engine\Foundation\QuixElement  $element
   *
   * @return \QuixNxt\Engine\Support\QuixNode
   * @throws \QuixNxt\Engine\Exceptions\RenderException
   * @since 3.0.0
   */
  public function renderNode(QuixElement $element): QuixNode;
}
