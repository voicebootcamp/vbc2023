<?php


namespace QuixNxt\NodeCleaner;

use QuixNxt\Engine\Exceptions\RenderException;
use QuixNxt\Utils\Schema;

class NodeCleaner implements INodeCleaner
{
    /**
     * @var \QuixNxt\NodeCleaner\NodeCleaner
     *
     * @since 3.0.0
     */
    protected static $instance;

    /**
     * NodeCleaner constructor.
     *
     * @since 3.0.0
     */
    private function __construct()
    {
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
     * @param  array  $nodes
     *
     * @return array
     *
     * @since 3.0.0
     */
    public function cleanUpRecursive(array $nodes): array
    {
        return array_map(function (array $node) {
            unset($node['id'], $node['hash']);

            $defaultNode  = Schema::getDefaults($node['slug']);
            $node['form'] = $this->deepDiff($defaultNode['form'], $node['form']);

            $node['visibility'] = $this->deepDiff($defaultNode['visibility'], $node['visibility']);

            if ( ! $node['visibility']) {
                unset($node['visibility']);
            }

            if (count($node['children'])) {
                $node['children'] = $this->cleanUpRecursive($node['children']);
            }

            return $node;
        }, $nodes);
    }

    /**
     * @param  string  $slug
     * @param  array  $data
     *
     * @return array
     *
     * @since 3.0.0
     */
    public function cleanUp(string $slug, array $data): array
    {
        $defaultForm = Schema::getDefaults($slug);

        return $this->deepDiff($defaultForm['form'], $data);
    }

    /**
     * @param  mixed  $original
     * @param  mixed  $modified
     *
     * @return mixed
     *
     * @since 3.0.0
     */
    private function deepDiff($original, $modified)
    {
        if ($modified === null) {
            // styles can be null
            return null;
        }

        $undefined = 'undefined';

        if (is_array($original)) {
            if (is_string($modified) || is_int($modified)) {
                return $modified;
            }

            if ( ! Schema::_isAssoc($original)) {
                // PHP treats everything as an array, so an empty object can also become an array
                if (count($original) > 0 && ! Schema::_isAssoc($modified) && count($modified) > 0) {
                    // group repeater
                    return $modified;
                }

                return $modified;
            }

            $diff = [];
            foreach ($original as $key => $value) {
                if (array_key_exists($key, (array) $modified)) {
                    $differ = $this->deepDiff($value, $modified[$key]);
                    if ($differ !== $undefined) {
                        $diff[$key] = $differ;
                    }
                }
            }

            if (count($diff) > 0) {
                return $diff;
            }
        } else {
            return $original !== $modified ? $modified : $undefined;
        }

        return $modified;
    }

    /**
     * @param  array  $nodes
     *
     * @return array
     *
     * @updated 4.0.0-beta1
     * @since 3.0.0
     */
    public function mergeRecursive(array $nodes): array
    {
        $results = [];
        foreach ($nodes as $key => $node) {
            $node['children'] = $this->mergeRecursive($node['children'] ?? []);
            $node             = $this->merge($node['slug'], $node);
            if ($node) {
                $results[] = $node;
            }
        }

        return $results;

        /**
         * array map to foreach due to builder breaks for legacy/old missing elements
         * commented by ahba
         * @since 4.0.0-beta1
         */
        // return array_map(function (array $node) {
        //     $node['children'] = $this->mergeRecursive($node['children'] ?? []);
        //     $node = $this->merge($node['slug'], $node);
        //     return $node;
        // }, $nodes);
    }

    /**
     * @param  string  $slug
     * @param  array  $data
     *
     * @return array
     *
     * @since 3.0.0
     */
    public function merge(string $slug, array $data): array
    {
        $defaults = Schema::getDefaults($slug);

        if ( ! $defaults) {
            \JFactory::getDocument()->addScriptDeclaration("var qxAlerts = qxAlerts ?? [];qxAlerts.push('Sorry, Element is missing : <b>{$slug}</b>. If you save your page now, it will be lost forever.');");
            return [];
            // throw new RenderException("Could not load default schema values for {$slug}");
        }

        if ( ! array_key_exists('visibility', $data) || ! $data['visibility']) {
            $data['visibility'] = $defaults['visibility'];
        } else {
            $data['visibility'] = $this->mergeDeep($defaults['visibility'], $data['visibility']);
        }
        $data['form'] = $this->mergeDeep($defaults['form'], $data['form']);

        return $data;
    }

    /**
     * @param  array  $original
     * @param  array  $modified
     *
     * @return array
     *
     * @since 3.0.0
     */
    private function mergeDeep(array $original, array $modified): array
    {
        if ( ! Schema::_isAssoc($original) && ! Schema::_isAssoc($modified)) {
            if ((count($original) > 0 && is_array($original[0])) || (count($modified) > 0 && is_array($modified[0]))) {
                
                // only possible type is group-repeater
                $values = [];
                foreach ($modified as $key => $value) {
                    $values[$key] = $this->mergeDeep($original[$key] ?? $original[0], $value);
                }

                return $values;
            }
        }

        $node = [];
        foreach ($original as $key => $value) {
            if ( ! array_key_exists($key, $modified)) {
                $node[$key] = $value;
            } else {
                $modified_value = $modified[$key];
                if (is_array($modified_value)) {
                    if ( ! is_array($value)) {
                        $node[$key] = $modified_value;
                    }
                    
                    // If the array is sequential than it'll store here.
                    elseif( ! Schema::_isAssoc($value)){
                        $node[$key] = $modified_value;
                    }
                    
                    else {
                        $node[$key] = $this->mergeDeep($value, $modified_value);
                    }
                } else {
                    $node[$key] = $modified_value;
                }
            }
        }

        return $node;
    }

}
