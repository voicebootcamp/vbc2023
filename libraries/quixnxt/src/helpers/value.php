<?php


if ( ! function_exists('value')) {
  /**
   * If the value is a closure then
   * returns the output of the closure by calling it or else
   * returns the value itself
   *
   * @param $value
   *
   * @return mixed
   *
   * @since 3.0.0
   */
  function value($value)
  {
    return $value instanceof Closure ? $value() : $value;
  }
}
