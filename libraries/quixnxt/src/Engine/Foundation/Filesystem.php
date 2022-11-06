<?php

namespace QuixNxt\Engine\Foundation;

abstract class Filesystem
{
    /**
     * Get a list of folders in the given directory
     *
     * @param  string  $path
     *
     * @return array
     *
     * @since 3.0.0
     */
    abstract public static function folders(string $path): array;

    /**
     * Get a list of files in the given path
     * Filter by extension if provided
     *
     * @param  string  $path
     * @param  string|null  $ext
     *
     * @return array
     *
     * @since 3.0.0
     */
    abstract public static function files(string $path, string $ext = null): array;

    /**
     * Ensure a file/path exists
     *
     * @param  string  $path
     *
     * @return bool
     *
     * @since 3.0.0
     */
    abstract public static function exists(string $path): bool;

    /**
     * Create folder if not exists
     *
     * @param  string  $path
     *
     * @return bool
     */
    abstract public static function mkdir(string $path): bool;

    /**
     * Remove directory if exists
     *
     * @param  string  $path
     *
     * @return bool
     */
    abstract public static function rmdir(string $path): bool;
}
