<?php

namespace QuixNxt\Engine\Foundation;

use QuixNxt\Engine\Contracts\RenderEngine as BaseRenderEngine;
use QuixNxt\Engine\Support\QuixNode;

abstract class RenderEngine implements BaseRenderEngine
{
  /**
   * @inheritDoc
   *
   * @since 3.0.0
   */
  public function renderElement(QuixElement $element): QuixNode
  {
    $node = new QuixNode();

    if ($element->hasHtml()) {
      $node->html = $element->renderHtml($this);
    }

    if ($element->hasStyle()) {
      $node->styles = $element->renderStyle($this);
    }

    if ($element->hasScript()) {
      $node->scripts = $element->renderScript($this);
    }

    if ($element->hasGlobalScript()) {
      $node->js_files = $element->getGlobalScripts();
    }

    if ($element->hasGlobalStylesheet()) {
      $node->css_files = $element->getGlobalStylesheets();
    }

    $element->clear();

    return $node;
  }

  /**
   * Register a template with the renderer
   *
   * @param  string  $name
   * @param  string  $template
   *
   * @return void
   * @since 3.0.0
   */
  public function registerTemplate(string $name, string $template): void
  {
    // does nothing by itself
  }
}
