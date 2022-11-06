<?php

namespace QuixNxt\Utils;


use JFile;
use JFolder;

class  Cache
{
  /**
   * @since 3.0.0
   */
  private const CACHE_ROOT = JPATH_SITE.'/media/quixnxt/storage/json/';

  /**
   * CacheHelper constructor.
   *
   * @since 3.0.0
   */
  public function __construct()
  {
    JFolder::create(self::CACHE_ROOT);
  }

  /**
   * @param  string  $key
   *
   * @return bool
   *
   * @since 3.0.0
   */
  public function exists(string $key): bool
  {
    return JFile::exists($this->getPath($key));
  }

  /**
   * @param  string  $key
   * @param $default
   *
   * @return string
   *
   * @since 3.0.0
   */
  public function get(string $key, $default = null): ?string
  {
    if ( ! $this->exists($key)) {
      return $default;
    }

    return file_get_contents($this->getPath($key));
  }

  /**
   * @param  string  $key
   * @param  string  $value
   *
   * @since 3.0.0
   */
  public function put(string $key, string $value): void
  {
    file_put_contents($this->getPath($key), $value);
  }

  /**
   * @param  string  $key
   *
   * @since 3.0.0
   */
  public function forget(string $key): void
  {
    JFile::delete($this->getPath($key));
  }

  /**\
   * @param  string  $name
   *
   * @return string
   *
   * @since 3.0.0
   */
  private function getPath(string $name): string
  {
    return self::CACHE_ROOT.$name;
  }

  /**\
   *
   * @return void
   *
   * @since 3.0.0
   */
  public static function clear(): void
  {
    JFolder::delete(self::CACHE_ROOT);
  }
}
