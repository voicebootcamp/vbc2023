<?php

namespace QuixNxt\Adapters;

abstract class Adapter implements IAdapter
{
  /**
   * @var \QuixNxt\Adapters\Adapter
   *
   * @since 3.0.0
   */
  protected static $instance;

  /**
   * Adapter constructor.
   *
   * @since 3.0.0
   */
  private function __construct() {}

  /**
   * @return static
   *
   * @since 3.0.0
   */
  public static function getInstance(): self
  {
    if(!static::$instance) {
      static::$instance = new static();
    }

    return static::$instance;
  }

  /**
   * @param $var
   *
   * @param  string|null  $field
   *
   * @return bool
   *
   * @since 3.0.0
   */
  protected function _isEmpty($var, string $field = null): bool
  {
    if ($field && is_array($var)) {
      $var = $var[$field] ?? null;
    }

    if (is_array($var)) {
      return count($var) === 0;
    }

    if (is_string($var)) {
      return $var === '';
    }

    return ! (bool) $var;
  }
}
