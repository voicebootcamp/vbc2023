<?php

namespace QuixNxt\Engine\Builder;

use QuixNxt\Engine\Concerns\HasHooks;
use QuixNxt\Engine\Contracts\QuixData;
use QuixNxt\Engine\Contracts\Renderer;
use QuixNxt\Engine\Contracts\ViewEngine as BaseViewEngine;
use QuixNxt\Engine\Support\QuixNode;

class ViewEngine implements BaseViewEngine
{
  use HasHooks;

  /**
   * @var \QuixNxt\Engine\Contracts\Renderer
   * @since 3.0.0
   */
  private $renderer;

  public function __construct(Renderer $renderer)
  {
    $this->renderer = $renderer;
  }

  /**
   * Render the page
   *
   * @param  \QuixNxt\Engine\Contracts\QuixData  $data
   *
   * @return \QuixNxt\Engine\Support\QuixNode
   * @since 3.0.0
   */
  public function render(QuixData $data): QuixNode
  {
    $data = $this->runBeforeHooks($data);

    $result = $this->renderer->render($data->getElements());

    return $this->runAfterHooks($result);
  }
}
