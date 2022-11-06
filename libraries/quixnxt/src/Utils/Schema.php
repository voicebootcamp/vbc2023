<?php

namespace QuixNxt\Utils;

use JProfiler;
use QuixNxt\Adapters\IAdapter;
use QuixNxt\Adapters\Quix2To3;
use QuixNxt\Elements\ElementBag;
use QuixNxt\NodeCleaner\INodeCleaner;
use QuixNxt\NodeCleaner\NodeCleaner;

class Schema
{
    public const QUIX_V2 = '2';
    public const QUIX_V3 = '3';

    /**
     * @var \QuixNxt\Adapters\Adapter[][]
     *
     * @since 3.0.0
     */
    private static $adapters = [
        '2' => [
            '3' => Quix2To3::class,
            // '4' => Quix2To4::class,
        ],
        // '3' => [
        //   '4' => Quix3To4::class,
        // ]
    ];

    /**
     * @var \QuixNxt\Utils\Cache
     *
     * @since 3.0.0
     */
    private $cache;

    /**
     * @var array
     *
     * @since 3.0.0
     */
    private $schema;

    /**
     * @var array
     *
     * @since 3.0.0
     */
    private $form;

    /**
     * @var \QuixNxt\Utils\Schema
     *
     * @since 3.0.0
     */
    private static $instance;

    /**
     * SchemaHelper constructor.
     *
     * @throws \ReflectionException
     * @since 3.0.0
     */
    private function __construct()
    {
        $this->cache = new Cache();

        if (JDEBUG) {
            JProfiler::getInstance('Application')->mark("Before loading forms");
        }
        $this->_ensureFormCached();

        if (JDEBUG) {
            JProfiler::getInstance('Application')->mark("After loading forms");
        }
    }

    /**
     * @return bool
     *
     * @since 3.0.0
     */
    private function _isCached(): bool
    {
        return $this->cache->exists('visual-builder-defaults.json');
    }

    /**
     * @throws \ReflectionException
     * @since 3.0.0
     */
    private function _ensureFormCached(): void
    {
        if ( ! QUIXNXT_DEBUG && $this->_isCached()) {
            $this->form = $this->cache->get('visual-builder-defaults.json', null);

            if ($this->form || $this->form !== '') {
                $this->form = unserialize($this->form, [__CLASS__]);

                return;
            }
        }

        // $this->schema = quix()->getElements();
        $this->schema = [];
        //
        $elementBag = ElementBag::getInstance();

        /** @var \QuixNxt\Elements\QuixElement $element */
        foreach ($elementBag->all() as $element) {
            $this->schema[$element->getSlug()] = $element->getConfig();
        }

        $this->prepareForm();
        unset($this->schema);
    }

    /**
     * @return static
     *
     * @since 3.0.0
     */
    public static function getInstance(): self
    {
        if ( ! static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @return string
     *
     * @since 3.0.0
     */
    public static function getAvailableElements(): string
    {
        $instance = static::getInstance();

        return $instance->cache->get('elements.json');
    }

    /**
     * @param  string  $from
     * @param  string  $to
     *
     * @return \QuixNxt\Adapters\IAdapter
     *
     * @since 3.0.0
     */
    public static function getAdapter(string $from, string $to): IAdapter
    {
        if ( ! isset(static::$adapters[$from][$to])) {
            throw new \RuntimeException("Could not find a suitable adapter for transforming from {$from} to ${to}");
        }

        return static::$adapters[$from][$to]::getInstance();
    }

    /**
     * @return \QuixNxt\NodeCleaner\INodeCleaner
     *
     * @since 3.0.0
     */
    public static function getCleaner(): INodeCleaner
    {
        return NodeCleaner::getInstance();
    }

    /**
     * @param  string  $slug
     *
     * @return array|null
     *
     * @since 3.0.0
     */
    public static function getDefaults(string $slug): ?array
    {
        return static::getInstance()->form[$slug] ?? null;
    }

    /**
     * @param  string  $slug
     *
     * @return array|null
     *
     * @since 3.0.0
     */
    public static function getSchema(string $slug): ?string
    {
        if (self::getInstance()->_isCached()) {
            return static::_getFromCache("schema-{$slug}.json");
        }

        return null;
    }

    /**
     * @param  string  $slug
     *
     * @return array|null
     *
     * @since 3.0.0
     */
    public static function getDefaultNode(string $slug): ?string
    {
        if (self::getInstance()->_isCached()) {
            return static::_getFromCache("form-{$slug}.json");
        }

        return null;
    }

    /**
     * @param  string  $key
     *
     * @return array|null
     *
     * @since 3.0.0
     */
    private static function _getFromCache(string $key): ?string
    {
        $cache = static::getInstance()->cache;
        if ($cache->exists($key)) {
            return $cache->get($key, null);
        }

        return null;
    }

    /**
     * @since 3.0.0
     */
    private function prepareForm(): void
    {
        $elements = [];
        foreach ($this->schema as $schema) {
            $this->cache->put("schema-{$schema['slug']}.json", json_encode($schema));

            $node = [
                'slug'       => $schema['slug'],
                'name'       => $schema['name'] ?? 'name:' . $schema['slug'],
                'visibility' => $schema['visibility'],
                'groups'     => $schema['groups'],
                'form'       => $this->_prepareDefaultValueFromNodeSchema($schema),
            ];

            $this->form[$node['slug']] = $node;

            $this->cache->put("form-{$node['slug']}.json", json_encode($node));

            $elements[] = [
                'name'       => $schema['name'] ?? 'name:' . $schema['slug'],
                'slug'       => $schema['slug'],
                'groups'     => $schema['groups'],
                'thumb_file' => $schema['thumb_file'],
            ];
        }

        $this->cache->put('visual-builder-defaults.json', serialize($this->form));
        $this->cache->put('elements.json', json_encode($elements));
    }

    /**
     * @param  array  $nodeSchema
     *
     * @return array
     *
     * @since 3.0.0
     */
    private function _prepareDefaultValueFromNodeSchema(array $nodeSchema): array
    {
        $form = [];
        foreach ($nodeSchema['form'] as $key => $value) {
            $form[$key] = $this->_findAndPullValue($value);
        }

        return $form;
    }

    /**
     * @param $nodes
     *
     * @return mixed
     *
     * @since 3.0.0
     */
    private function _findAndPullValue($nodes)
    {
        if (isset($nodes['label']) && ( ! isset($nodes['schema']) || count($nodes['schema']) === 0)) {
            return $this->_pullValue($nodes);
        }

        $values = [];
        foreach ($nodes as $index => $node) {
            if ( ! isset($node['name'])) {
                if ( ! is_array($node) || self::_isAssoc($node)) {
                    $values[$index] = $node;
                } else {
                    $values[$index] = $this->_pullValue($node);
                }
            } else {
                $values[$node['name']] = $this->_pullValue($node);
            }
        }

        if ( ! self::_isAssoc($values)) {
            return array_values($values);
        }

        return $values;
    }

    /**
     * @param  array  $node
     *
     * @return array|mixed
     *
     * @since 3.0.0
     */
    private function _pullValue(array $node)
    {
        if (isset($node['type']) && $node['type'] === 'group-repeater') {
            return array_map(function ($value) {
                return $this->_findAndPullValue($value);
            }, $node['value']);
        }

        if ( ! $node['value']) {
            return $node['value'];
        }

        if (is_array($node['value'])) {
            if ( ! self::_isAssoc($node['value'])) {
                if (isset($node['units'], $node['suffix']) && $node['units'] !== false && in_array($node['suffix'], $node['units'], true)) {
                    $node['value']['unit'] = $node['value']['unit'] ?? $node['suffix'];
                }

                return $this->_findAndPullValue($node['value']);
            }

            if (/*count($node['value']) === 3 && */ isset(/*$node['icon'], */ $node['label'], $node['value']['value'])) {
                return $node['value']['value'];
            }
        }

        return $node['value'];
    }

    /**
     * @param  array  $arr
     *
     * @return bool
     *
     * @since 3.0.0
     */
    public static function _isAssoc(array $arr): bool
    {
        if (count($arr) === 0) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * @param  string  $slug
     * @param  array  $schema
     *
     * @since 3.0.0
     */
    private function _debug(string $slug, array $schema): void
    {
        $path = dirname($_SERVER['DOCUMENT_ROOT'])."/schema";
        if ( ! file_exists($path) && ! mkdir($path) && ! is_dir($path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }

        file_put_contents($path."/{$slug}.json", json_encode($schema, 128));
    }
}
