<?php

namespace QuixNxt\Elements;

use ReflectionException;
use Symfony\Component\Yaml\Yaml;
use QuixNxt\AssetManagers\ScriptManager;
use QuixNxt\AssetManagers\StyleManager;
use QuixNxt\Concerns\CanProvideTemplate;
use QuixNxt\Config\FinalTransformer;
use QuixNxt\Config\Transformer;
use QuixNxt\Engine\Contracts\AssetManager;
use QuixNxt\Engine\Contracts\RenderEngine;
use QuixNxt\Engine\Contracts\Renderer;
use QuixNxt\Engine\Support\QuixNode;
use QuixNxt\Utils\Schema;

class QuixElement extends \QuixNxt\Engine\Foundation\QuixElement
{
    use CanProvideTemplate;

    private const LOADED = 'LOADED';

    private static $_html = [];
    private static $_style = [];
    private static $_script = [];
    private static $_globals = [];
    /**
     * @var Renderer
     *
     * @since 3.0.0
     */
    private static $renderer;

    /**
     * @var string
     * @since 3.0.0
     */
    protected $_slug;
    protected $_path;

    public const QUIX_VISUAL_BUILDER_PATH = JPATH_LIBRARIES.'/quixnxt/visual-builder/elements';

    public function __construct(string $slug, string $path)
    {
        $this->_slug = $slug;
        $this->_path = $path;
    }

    public function getSlug(): string
    {
        return $this->_slug;
    }

    /**
     * @return array
     *
     * @throws ReflectionException
     * @since 3.0.0
     */
    public function getConfig(): array
    {
        $element_path = $this->getElementPath();
        $url          = \JUri::root().substr(dirname($element_path), strlen(JPATH_SITE) + 1);

        $config = null;
        if ($this->fileExists('config.yml')) {
            $config         = Yaml::parse($this->readFile('config.yml'));
            $config['file'] = $element_path.'/config.yml';
        } elseif ($this->fileExists('config.php')) {
            $config         = require $element_path.'/config.php';
            $config['file'] = $element_path.'/config.php';
        }

        $config['path'] = $element_path;
        $config['url']  = $url.'/'.$this->getSlug();
        $config['slug'] = $this->getSlug();

        $config = Transformer::getInstance()->transform($config, $element_path);
        // dd('QuixElement after config', $config);
        $configArray = FinalTransformer::getInstance()->transform([$config]);

        return $configArray[0];
    }

    /**
     * @param  array  $data
     *
     *
     * @return \QuixNxt\Engine\Foundation\QuixElement
     * @since 3.0.0
     */
    public function setData(array $data): \QuixNxt\Engine\Foundation\QuixElement
    {
        $this->data = [
            'form'       => $data['form'] ?? null,
            'visibility' => $data['visibility'] ?? null,
        ];

        return $this;
    }

    /**
     * @return array
     *
     * @since 3.0.0
     */
    public function getData(): array
    {
        if ($this->data['merged'] ?? false) {
            return $this->data;
        }

        return $this->withDefaults();
    }

    /**
     * @return array
     *
     * @since 3.0.0
     */
    public function withDefaults(): array
    {
        $cleaner = Schema::getCleaner();

        $this->data = $cleaner->merge($this->getSlug(), $this->data);

        $this->data['elementUrl'] = $this->getElementUrl();
        $this->data['merged']     = true;

        return $this->data;
    }

    /**
     * @return string|null
     * @since 3.0.0
     */
    public function getSchema(): ?string
    {
        return Schema::getSchema($this->getSlug());
    }

    /**
     * @return string|null
     *
     * @since 3.0.0
     */
    public function getDefaultNode(): ?string
    {
        return Schema::getDefaultNode($this->getSlug());
    }

    /**
     * @return bool
     *
     * @since 3.0.0
     */
    public function hasHtml(): bool
    {
        if ( ! array_key_exists($this->getSlug(), self::$_html)) {
            self::$_html[$this->getSlug()] = $this->fileExists('partials/html.twig');
        }

        return (bool) self::$_html[$this->getSlug()];
    }

    /**
     * @param  RenderEngine  $engine
     *
     * @return string|null
     *
     * @since 3.0.0
     */
    public function renderHtml(RenderEngine $engine): ?string
    {
        $data = array_merge($this->getData(), [
            'children'  => $this->hasChildren() ? $this->children() : [],
            'renderer'  => static::$renderer,
            'isDynamic' => strpos($this->getSlug(), 'joomla-') !== false || strpos($this->getSlug(), 'form') !== false,
        ]);

        $this->_maybeRequireGlobal();

        return $engine->render("{$this->getSlug()}/partials/html.twig", $data);
    }

    /**
     * @return bool
     *
     * @since 3.0.0
     */
    public function hasStyle(): bool
    {
        if ( ! array_key_exists($this->getSlug(), self::$_style)) {
            self::$_style[$this->getSlug()] = $this->fileExists('partials/style.twig');
        }

        return (bool) self::$_style[$this->getSlug()];
    }

    /**
     * @param  RenderEngine  $engine
     *
     * @return string|null
     *
     * @since 3.0.0
     */
    public function renderStyle(RenderEngine $engine): ?string
    {
        $styleManager = $this->getStyleManagerInstance();

        $data = array_merge($this->getData(), [
            'style'    => $styleManager,
            'renderer' => static::$renderer,
        ]);

        $style = $engine->render("{$this->getSlug()}/partials/style.twig", $data);

        $raw = trim($style);
        if ($raw !== '') {
            $styleManager->add($raw);
        }

        return null;
    }

    /**
     * @return bool
     *
     * @since 3.0.0
     */
    public function hasScript(): bool
    {
        if ( ! array_key_exists($this->getSlug(), self::$_script)) {
            self::$_script[$this->getSlug()] = $this->fileExists('partials/script.twig');
        }

        return (bool) self::$_script[$this->getSlug()];
    }

    /**
     * @param  RenderEngine  $engine
     *
     * @return string|null
     *
     * @since 3.0.0
     */
    public function renderScript(RenderEngine $engine): ?string
    {
        $script = $engine->render("{$this->getSlug()}/partials/script.twig", $this->getData());

        $script = trim($script);
        if ($script !== '') {
            $this->getScriptManagerInstance()->add($script);
        }

        return null;
    }

    /**
     * @since 3.0.0
     */
    private function _maybeRequireGlobal(): void
    {
        if (isset(static::$_globals[$this->getSlug()])) {
            return;
        }

        static::$_globals[$this->getSlug()] = self::LOADED;

        if ($this->fileExists('global.php')) {
            include $this->getElementPath().'/global.php';
        }
    }

    /**
     *
     * @param  string|null  $type
     *
     * @return bool
     * @since 3.0.0
     */
    public function canHaveChildren(string $type = null): bool
    {
        return in_array(self::$slug, ['row', 'section', 'column']);
    }

    /**
     * @param  string|null  $path
     *
     * @return string
     * @since 3.0.0
     */
    private function getElementPath(?string $path = null): string
    {
        return $this->_path.($path ? '/'.ltrim($path, '/') : null);
    }

    /**
     * @return string
     *
     * @since 3.0.0
     */
    private function getElementUrl(): string
    {
        return \JUri::root().substr($this->getElementPath(), strlen(JPATH_SITE));
    }

    /**
     * @return string/bool
     * @since 3.0.0
     */
    public function getElementHelper(): ?string
    {
        if ($this->fileExists('helper.php')) {
            return $this->getElementPath('helper.php');
        }

        return false;
    }

    /**
     * @param  string  $file
     *
     * @return string|null
     *
     * @since 3.0.0
     */
    private function readFile(string $file): ?string
    {
        $path = $this->getElementPath($file);

        return file_get_contents($path);
    }

    /**
     * @param  string  $file
     *
     * @return bool
     *
     * @since 3.0.0
     */
    private function fileExists(string $file): bool
    {
        $path = $this->getElementPath($file);

        return file_exists($path);
    }

    /**
     * @return \QuixNxt\AssetManagers\ScriptManager
     * @since 3.0.0
     */
    public function getScriptManagerInstance(): AssetManager
    {
        return ScriptManager::getInstance();
    }

    /**
     * @return \QuixNxt\AssetManagers\StyleManager
     * @since 3.0.0
     */
    public function getStyleManagerInstance(): AssetManager
    {
        return StyleManager::getInstance();
    }

    /**
     * @param  Renderer  $renderer
     *
     * @return \QuixNxt\Engine\Support\QuixNode
     *
     * @since 3.0.0
     */
    public function render(Renderer $renderer): QuixNode
    {
        static::$renderer = $renderer;

        return $renderer->renderNode($this);
    }
}
