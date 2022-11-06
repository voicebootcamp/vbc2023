<?php

namespace QuixNxt\Engine\Foundation;

use QuixNxt\Engine\Contracts\AssetManager as BaseAssetManager;
use QuixNxt\Engine\Contracts\RenderEngine;
use QuixNxt\Engine\Contracts\Renderer;
use QuixNxt\Engine\Support\QuixNode;

abstract class QuixElement
{
  /**
   * The name of this element
   *
   * @var string
   * @since 3.0.0
   */
    protected static $name = 'Name';

  /**
   * The type of this Element
   *
   * @var string
   * @since 3.0.0
   */
    protected static $type = 'element';

  /**
   * The ID/loader/unique identifier of this element
   *
   * @var string
   * @since 3.0.0
   */
    protected static $slug = 'slug';

  /**
   * The groups this element belongs to
   *
   * @var array
   * @since 3.0.0
   */
    protected static $groups = [];

  /**
   * The children element nodes of this element
   *
   * @var array
   * @since 3.0.0
   */
    protected $children = [];

  /**
   * The schema for this element
   *
   * @var string|null
   * @since 3.0.0
   */
    protected static $schema = '';

  /**
   * Element data
   *
   * @var array
   * @since 3.0.0
   */
    protected $data = [];

  /**
   * @return \QuixNxt\Engine\Contracts\AssetManager
   *
   * @since 3.0.0
   */
    abstract public function getStyleManagerInstance(): BaseAssetManager;

  /**
   * @return \QuixNxt\Engine\Contracts\AssetManager
   *
   * @since 3.0.0
   */
    abstract public function getScriptManagerInstance(): BaseAssetManager;

  /**
   * @return string
   * @since 3.0.0
   */
    public function getName(): string
    {
        return static::$name;
    }

  /**
   * @return string
   * @since 3.0.0
   */
    public function getType(): string
    {
        return static::$type;
    }

  /**
   * @return string
   * @since 3.0.0
   */
    public function getSlug(): string
    {
        return static::$slug;
    }

  /**
   * @return array
   * @since 3.0.0
   */
    public function getGroups(): array
    {
        return static::$groups;
    }

  /**
   * @return bool
   * @since 3.0.0
   */
    public function hasGlobalStylesheet(): bool
    {
        return false;
    }

  /**
   * @return array
   * @since 3.0.0
   */
    public function getGlobalStylesheets(): array
    {
        return [];
    }

  /**
   * @return bool
   * @since 3.0.0
   */
    public function hasGlobalScript(): bool
    {
        return false;
    }

  /**
   * @return array
   * @since 3.0.0
   */
    public function getGlobalScripts(): array
    {
        return [];
    }

  /**
   * @return string|null
   * @since 3.0.0
   */
    public function getSchema(): ?string
    {
        return static::$schema;
    }

  /**
   * Determines if this element can have children
   *
   * @param  string|null  $type
   *
   * @return bool
   * @since 3.0.0
   */
    abstract public function canHaveChildren(string $type = null): bool;

  /**
   * Determines if this element has HTML
   *
   * @return bool
   * @since 3.0.0
   */
    public function hasHtml(): bool
    {
        return false;
    }

  /**
   * Render the HTML
   *
   * @param  \QuixNxt\Engine\Contracts\RenderEngine  $engine
   *
   * @return string|null
   * @since 3.0.0
   */
    public function renderHtml(RenderEngine $engine): ?string
    {
        return null;
    }

  /**
   * Determines if this element has style
   *
   * @return bool
   * @since 3.0.0
   */
    public function hasStyle(): bool
    {
        return false;
    }

  /**
   * Render the style
   *
   * @param  \QuixNxt\Engine\Contracts\RenderEngine  $engine
   *
   * @return string|null
   * @since 3.0.0
   */
    public function renderStyle(RenderEngine $engine): ?string
    {
        return null;
    }

  /**
   * Determines if this element has script
   *
   * @return bool
   * @since 3.0.0
   */
    public function hasScript(): bool
    {
        return false;
    }

  /**
   * Render the script
   *
   * @param  \QuixNxt\Engine\Contracts\RenderEngine  $engine
   *
   * @return string|null
   * @since 3.0.0
   */
    public function renderScript(RenderEngine $engine): ?string
    {
        return null;
    }

  /**
   * sets the element data
   *
   * @param  array  $data
   *
   * @return $this
   * @since 3.0.0
   */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

  /**
   * Add a new child at the end
   *
   * @param  \QuixNxt\Engine\Foundation\QuixElement  $element
   *
   * @return $this
   * @since 3.0.0
   */
    public function addChild(QuixElement $element): self
    {
        $this->children[] = $element;

        return $this;
    }

  /**
   * @return array
   * @since 3.0.0
   */
    public function getData(): array
    {
        return $this->data;
    }

  /**
   * See if this element has children
   *
   * @return bool
   * @since 3.0.0
   */
    public function hasChildren(): bool
    {
        return count($this->children);
    }

  /**
   * @return array
   * @since 3.0.0
   */
    public function children(): array
    {
        return $this->children;
    }

  /**
   * Render this node
   *
   *
   * @param  \QuixNxt\Engine\Contracts\Renderer  $renderer
   *
   * @return \QuixNxt\Engine\Support\QuixNode
   * @since 3.0.0
   */
    public function render(Renderer $renderer): QuixNode
    {
        return $renderer->renderNode($this);
    }

  /**
   * Clear up any data that are not required anymore
   *
   * @since 3.0.0
   */
    public function clear(): void
    {
    }
}
