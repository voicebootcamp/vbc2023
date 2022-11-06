<?php

namespace QuixNxt\FormEngine\Transformers;

use QuixNxt\FormEngine\Contracts\ControlTransformer;

class SelectTransformer extends ControlTransformer
{
    /**
     * Get the type for the select.
     *
     * @param           $config
     * @param  string  $type
     *
     * @return string
     *
     * @since 3.0.0
     */
    public function getType($config, $type = ""): string
    {
        return "select";
    }

    /**
     * Get options for the select.
     *
     * @param  array  $config
     *
     * @return mixed|null
     *
     * @since 3.0.0
     */
    public function getOptions(array $config)
    {
        return $this->get($config, 'options', []);
    }

    /**
     * Transform the select.
     *
     * @param  array  $config
     * @param  string|null  $path
     *
     * @return array
     *
     * @since 3.0.0
     */
    public function transform(array $config, ?string $path): array
    {
        $multiple = $this->get($config, 'multiple', false);

        $tags = $this->get($config, 'tags', false);

        $options = $this->getOptions($config);

        $options = array_map(static function ($value, $label): array {
            if (is_array($label) && array_key_exists('label', $label)) {
                return array_merge(['value' => $value], $label);
            }

            return compact("value", "label");
        }, array_keys($options), array_values($options));


        $modifiedConfig = parent::transform($config, $path);

        $modifiedConfig['options']  = $options;
        $modifiedConfig['multiple'] = $multiple;
        $modifiedConfig['tags']     = $tags;

        // set responsive mode,
        // if responsive mode is set as true, then slider responsive mode will be true
        // otherwise it'll be false
        if ( ! isset($config['responsive'])) {
            $modifiedConfig['responsive'] = false;
        } else {
            $modifiedConfig['responsive'] = $config['responsive'];
        }

        $value = [];

        if ($modifiedConfig['responsive'] && ! isset($value["responsive_preview"])) {
            $value["responsive_preview"] = false;

            $value["responsive"] = $modifiedConfig['responsive'];

            $value["desktop"] = $modifiedConfig['value']['desktop'] ?? "";

            $value["tablet"] = $modifiedConfig['value']['tablet'] ?? "";

            $value["phone"] = $modifiedConfig['value']['phone'] ?? "";

            $modifiedConfig['value'] = $value;
        }

        $modifiedConfig['select'] = $value;

        if (isset($config['image'])) {
            $modifiedConfig['select']['image'] = $config['image'];
        }


        $modifiedConfig['element_path'] = $path;

        return $modifiedConfig;
    }

    /**
     * Get the value for the select.
     *
     * @param $config
     *
     * @return array|string|null
     *
     * @since 3.0.0
     */
    public function getValue($config)
    {
        if ($this->get($config, 'multiple')) {
            $value = (array) $this->get($config, 'value', []);
        } else {
            $value = $this->get($config, 'value', '');
        }

        return $value;
    }
}
