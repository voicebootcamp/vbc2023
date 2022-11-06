<?php

if ( ! function_exists('dd')) {
  /**
   * Die and dump.
   *
   * @since 1.0.0
   */
  function dd()
  {
    echo "<pre>";

    var_dump(func_get_args());

    echo "</pre>";
    exit();
  }
}

if ( ! function_exists('pd')) {
  /**
   * Pre and dump.
   *
   * @param           $var
   * @param  bool|true  $kill
   */
  function pd($var, $kill = true)
  {
    echo "<pre>";
    print_r($var);
    echo ! $kill ? "</pre>" : die();
  }

}


if ( ! function_exists('xception')) {
  /**
   * Printing exception and die.
   *
   * @param      $message
   * @param  bool  $kill
   */
  function xception($message, $kill = true)
  {
    echo $message;
    if ($kill) {
      die();
    }
  }
}
