<?php

namespace QuixNxt\Engine\Support;

class QuixNode
{
  /**
   * The rendered HTML
   *
   * @var string
   * @since 3.0.0
   */
  public $html;

  /**
   * The rendered CSS stylesheet
   *
   * @var string
   * @since 3.0.0
   */
  public $styles;

  /**
   * The rendered JS
   *
   * @var string
   * @since 3.0.0
   */
  public $scripts;

  /**
   * Links to CSS files to include in page
   *
   * @var array
   * @since 3.0.0
   */
  public $css_files = [];

  /**
   * Links to JS files to include in page
   *
   * @var array
   * @since 3.0.0
   */
  public $js_files = [];

  /**
   * @param  \QuixNxt\Engine\Support\QuixNode  $node
   *
   * @return $this
   * @since 3.0.0
   */
  public function append(self $node): self
  {
    $this->html .= $node->html;

    return $this->appendWithoutHtml($node);
  }

  /**
   * @param  \QuixNxt\Engine\Support\QuixNode  $node
   *
   * @return $this
   * @since 3.0.0
   */
  public function appendWithoutHtml(self $node): self
  {
    $this->styles    .= $node->styles;
    $this->scripts   .= $node->scripts;
    $this->js_files  = array_merge($this->js_files, $node->js_files);
    $this->css_files = array_merge($this->css_files, $node->css_files);

    return $this;
  }

  /**
   * @return string
   * @since 3.0.0
   */
  public function __toString(): string
  {
    return $this->html ?? '';
  }
}
