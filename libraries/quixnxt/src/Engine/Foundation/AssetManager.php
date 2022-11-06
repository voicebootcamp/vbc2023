<?php

namespace QuixNxt\Engine\Foundation;

use QuixNxt\Engine\Contracts\AssetManager as BaseAssetManager;

abstract class AssetManager implements BaseAssetManager
{
    /**
     * @var array
     *
     * @since 3.0.0
     */
    protected $stack = [];

    /**
     * @var array
     *
     * @since 3.0.0
     */
    protected $files = [];

    /**
     * @var \QuixNxt\Engine\Foundation\AssetManager
     *
     * @since 3.0.0
     */
    private static $_instances = [];

    private function __construct()
    {
    }

    /**
     * @return \QuixNxt\Engine\Contracts\AssetManager
     *
     * @since 3.0.0
     */
    public static function getInstance(): BaseAssetManager
    {
        if ( ! isset(self::$_instances[static::class])) {
            self::$_instances[static::class] = new static();
        }

        return self::$_instances[static::class];
    }

    /**
     * @param  string  $str
     *
     * @return $this|\QuixNxt\Engine\Contracts\AssetManager
     *
     * @since 3.0.0
     */
    public function add(string $str): BaseAssetManager
    {
        $this->stack[] = $str;

        return $this;
    }

    /**
     * @param  string  $url
     *
     * @return $this|\QuixNxt\Engine\Contracts\AssetManager
     *
     * @since 3.0.0
     */
    public function addUrl(string $url): BaseAssetManager
    {
        $this->files[] = $url;

        return $this;
    }

    /**
     * @return array
     *
     * @since 3.0.0
     */
    public function getUrls(): array
    {
        $files       = $this->files;
        $this->files = [];

        return array_unique($files);
    }

    /**
     * @return string
     *
     * @since 3.0.0
     */
    public function compile(): string
    {
        $stack       = $this->stack;
        $this->stack = [];

        return implode($stack);
    }
}
