<?php

namespace QuixNxt\Engine\Builder;

use QuixNxt\Engine\Contracts\RenderEngine;
use QuixNxt\Engine\Contracts\Renderer as BaseRenderer;
use QuixNxt\Engine\Foundation\QuixElement;
use QuixNxt\Engine\Support\QuixNode;

class Renderer implements BaseRenderer
{
  /**
   * @var \QuixNxt\Engine\Contracts\RenderEngine
   * @since 3.0.0
   */
  private $engine;

  /**
   * Renderer constructor.
   *
   * @param  \QuixNxt\Engine\Contracts\RenderEngine  $engine
   *
   * @since 3.0.0
   */
  public function __construct(RenderEngine $engine)
  {
    $this->engine = $engine;
  }

  /**
   * @return \QuixNxt\Engine\Contracts\RenderEngine
   *
   * @since 3.0.0
   */
  public function getEngine(): RenderEngine
  {
    return $this->engine;
  }

  /**
   * @inheritDoc
   *
   * @param  \QuixNxt\Engine\Contracts\QuixData  $data
   *
   * @since 3.0.0
   */
  public function render(array $elements): QuixNode
  {
    $node = new QuixNode();

    foreach ($elements as $element) {
      $node->append($element->render($this));
    }

    return $node;
  }

  /**
   * Render a single element
   *
   * @param  \QuixNxt\Engine\Foundation\QuixElement  $element
   *
   * @return \QuixNxt\Engine\Support\QuixNode
   * @since 3.0.0
   */
  public function renderNode(QuixElement $element): QuixNode
  {
    return $this->engine->renderElement($element);
  }
}
