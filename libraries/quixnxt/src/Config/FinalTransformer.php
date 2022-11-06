<?php

namespace QuixNxt\Config;

class FinalTransformer
{
    private static $instance;

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

        return  static::$instance;
    }

    /**
     * Final transformation.
     *
     * @param $configs
     *
     * @return array
     *
     * @since 3.0.0
     */
    public function transform($configs): array
    {
        return array_values(array_reduce($configs, function ($carry, $config) {
            $slug = $config['slug'];

            if (array_key_exists($slug, $carry)) {
                $form = $this->form($carry[$slug]['form'], $config['form']);

                $carry[$slug]['form'] = $form;

                if (file_exists($config['view_file'])) {
                    $carry[$slug]['view_file'] = $config['view_file'];
                }

                if (file_exists($config['thumb_file'])) {
                    $carry[$slug]['thumb_file'] = $config['thumb_file'];
                }

                if (file_exists($config['css_file'])) {
                    $carry[$slug]['css_file'] = $config['css_file'];
                }

                if (file_exists($config['dynamic_style_file'])) {
                    $carry[$slug]['dynamic_style_file'] = $config['dynamic_style_file'];
                }
                if (file_exists($config['template_file'])) {
                    $carry[$slug]['template_file'] = $config['template_file'];
                }

                $carry[$slug]['type'] = (!isset($config['type']) or  is_null($config['type'])) ? 'all' : $config['type'];
            } else {
                $carry[$slug] = $config;
            }

            return $carry;
        }, []));
    }

    /**
     * Transform form.
     *
     * @param $form
     * @param $appendForm
     *
     * @return array
     *
     * @since 3.0.0
     */
    public function form($form, $appendForm): ?array
    {
        $formControls = flatten_array($form);

        // If there is something in the config file then add it to the append file
        foreach ($appendForm as $tab => $controls) {
            if (array_key_exists($tab, $form)) {
                $tabControls = $form[$tab];

                $controls = array_reduce($controls, static function ($carry, $control) use ($formControls) {
                    if (array_find_by($formControls, 'name', $control['name'])) {
                        return $carry;
                    }

                    $carry[] = $control;

                    return $carry;
                }, []);

                $appendForm[$tab] = array_merge($tabControls, $controls);
            }
        }

        // Merge all
        $form = array_merge($form, $appendForm);

        return $form;
    }
}
