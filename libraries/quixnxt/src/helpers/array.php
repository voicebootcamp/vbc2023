<?php

if (!function_exists('array_find_by')) {
    /**
     * Array find by the given key.
     *
     * @param $collection
     * @param $key
     * @param $value
     *
     * @return array|null
     */
    function array_find_by($collection, $key, $value)
    {
        $foundKey = array_search($value, array_column($collection, $key));

        return !is_bool($foundKey) && array_key_exists($foundKey, $collection) ? $collection[$foundKey] : null;
    }
}

if (!function_exists('flatten_array')) {
    /**
     * Flatter array.
     *
     * @param $array
     * @param $times
     *
     * @return mixed
     */
    function flatten_array($array, $times = 1)
    {
        while($times--) {
            $array = call_user_func_array('array_merge', $array);
        }

        return $array;
    }
}

if (!function_exists('flatten_array_ref')) {
    /**
     * Flatter array.
     *
     * @param $array
     * @param $times
     *
     * @return mixed
     */
    function flatten_array_ref(&$array, $times = 1)
    {
        while($times--) {
            $array = call_user_func_array( 'array_merge_recursive', $array);
        }

        return $array;
    }
}

if (!function_exists('array_get')) {
    /**
     * Get array by the given key.
     *
     * @param      $array
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    function array_get($array, $key, $default = null)
    {
        if (!is_array($array)) {
            return $default;
        }

        if (is_null($key)) {
            return $array;
        }

        if (is_bool($key)) {
            return $default;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) ||
                !array_key_exists($segment, $array)
            ) {
                return value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }
}

if (!function_exists('obj_to_array')) {
    /**
     * Transform object to array.
     *
     * @param $obj
     * @param $oKey
     * @param $oValue
     *
     * @return array
     */
    function obj_to_array($obj, $oKey, $oValue)
    {
        $arr = [];

        array_walk($obj, function ($v, $k) use (&$arr, $oKey, $oValue) {
            $arr[$v->{$oKey}] = $v->{$oValue};
        });

        return $arr;
    }
}

if (!function_exists('array_pluck')) {
    /**
     * Array pluck.
     *
     * @param $toPluck
     * @param $arr
     *
     * @return array
     */
    function array_pluck($toPluck, $arr)
    {
        return array_map(function ($item) use ($toPluck) {
            return $item[$toPluck];
        }, $arr);
    }
}

if (!function_exists('array_pick')) {
    /**
     * Array pick.
     *
     * @param       $array
     * @param array $picks
     * @param bool  $exclusive
     *
     * @return array
     */
    function array_pick($array, array $picks, $exclusive = false)
    {
        $output = [];

        foreach ($picks as $pick) {
            if ($exclusive) {
                if (array_key_exists($pick, $array)) {
                    $output[$pick] = $array[$pick];
                }
            } else {
                $value = array_key_exists($pick, $array) ? $array[$pick] : null;
                $output[$pick] = $value;
            }
        }

        return $output;
    }
}

/**
 * Determine the given string is serialized or not.
 *
 * @param $str
 *
 * @return bool
 */
function isSerialized($str)
{
    return ($str == serialize(false) || @unserialize($str) !== false);
}


if ( ! function_exists( 'array_merge_recursive_distinct ' ) ) {
  function array_merge_recursive_distinct( array &$array1, array &$array2 ) {
    $merged = $array1;

    foreach ( $array2 as $key => &$value ) {
      if ( is_array( $value ) && isset ( $merged [$key] ) && is_array( $merged [$key] ) ) {
        $merged [$key] = array_merge_recursive_distinct( $merged [$key], $value );
      } else {
        $merged [$key] = $value;
      }
    }

    return $merged;
  }
}
