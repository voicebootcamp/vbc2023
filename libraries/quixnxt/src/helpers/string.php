<?php


if ( ! function_exists('qxStringStartsWith')) {
    /**
     * Starts with.
     *
     * @param $haystack
     * @param $needle
     *
     * @return bool
     * @since 3.0.0
     */
    function qxStringStartsWith($haystack, $needle)
    {
        # search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }
}

if ( ! function_exists("qxStringEchobig")) {
    function qxStringEchobig($string, $bufferSize = 1000)
    {
        $splitString = str_split($string, $bufferSize);

        foreach ($splitString as $chunk) {
            echo $chunk;
        }
    }
}

if ( ! function_exists('qxStringEndsWith')) {
    /**
     * Ends with.
     *
     * @param $haystack
     * @param $needle
     *
     * @return bool
     * @since 3.0.0
     */
    function qxStringEndsWith($haystack, $needle)
    {
        # search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle,
                    $temp) !== false);
    }
}

if ( ! function_exists('quix_trailingslashit')) {
    /**
     * @param $string
     *
     * @return string
     * @since 3.0.0
     */
    function quix_trailingslashit($string)
    {
        return quix_untrailingslashit($string).'/';
    }
}

if ( ! function_exists('quix_untrailingslashit')) {
    /**
     * @param $string
     *
     * @return string
     * @since 3.0.0
     */
    function quix_untrailingslashit($string)
    {
        return rtrim($string, '/\\');
    }
}

if ( ! function_exists('classNames')) {
    /**
     * Get class names.
     *
     * @return string
     * @since 3.0.0
     */
    function classNames()
    {
        $args = func_get_args();

        $classes = array_map(static function ($arg) {
            if (is_array($arg)) {
                return implode(" ", array_filter(array_map(static function ($expression, $class) {
                    return $expression ? $class : false;
                }, $arg, array_keys($arg))));
            }

            return $arg;
        }, $args);

        return implode(" ", array_filter($classes));
    }
}

if ( ! function_exists('visibilityClasses')) {

    /**
     * Get the class visibility from the given visibility.
     *
     * @param $visibility
     *
     * @return string
     * @since 3.0.0
     */
    function visibilityClasses($visibility)
    {
        return classNames([
            'qx-hidden-lg' => ! $visibility['lg'],
            'qx-hidden-md' => ! $visibility['md'],
            'qx-hidden-sm' => ! $visibility['sm'],
            'qx-hidden-xs' => ! $visibility['xs'],
        ]);
    }
}

if ( ! function_exists('startsWith')) {

    /**
     * Function to check string starting
     * with given substring
     *
     * @param $string
     * @param $startString
     *
     * @return string
     * @since 3.0.0
     */
    function startsWith($string, $startString)
    {
        $len = strlen($startString);

        return (substr($string, 0, $len) === $startString);
    }
}

if ( ! function_exists('startsWith')) {

    /**
     * Function to check the string is ends
     * with given substring or not
     *
     * @param $string
     * @param $endString
     *
     * @return string
     * @since 3.0.0
     */
    function endsWith($string, $endString)
    {
        $len = strlen($endString);
        if ($len == 0) {
            return true;
        }

        return (substr($string, -$len) === $endString);
    }
}
