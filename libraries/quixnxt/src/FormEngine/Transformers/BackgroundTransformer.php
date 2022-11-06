<?php

namespace QuixNxt\FormEngine\Transformers;

use QuixNxt\FormEngine\Contracts\ControlTransformer;

class BackgroundTransformer extends ControlTransformer
{
    /**
     * Background field supported types.
     *
     * @since 3.0.0
     */
    protected $types = [
        'classic',
        'gradient',
        'video'
    ];

    /**
     * Default background field type.
     *
     * @since 3.0.0
     */
    protected $type = 'classic';

    /**
     * array of config.
     *
     * @since 3.0.0
     */
    protected $config;

    /**
     * Transform the given configuration for the group repeater.
     *
     * @param  array  $config
     * @param  string|null  $path
     *
     * @return array
     * @since 3.0.0
     */
    public function transform(array $config, ?string $path): array
    {
        $c            = parent::transform($config, $path);
        $this->config = $config;

        $c['supportedTypes'] = [];
        $c['tab']            = $this->get($config, "tab", false);

        foreach ($this->types as $type) {
            if ($this->isRequireType($config, $type)) {
                $c['supportedTypes'][] = $type;
            }
        }

        $c['types']   = [
            'classic'  => $this->defaultProperties(),
            'gradient' => [
                'type'       => 'gradient',
                'properties' => [
                    'color_1'        => '',
                    'color_2'        => '#f36',
                    'type'           => 'linear',
                    'direction'      => 180,
                    'start_position' => 0,
                    'end_position'   => 100,
                    'overlay'        => false
                ]
            ],
            'video'    => [
                'type'       => 'video',
                'properties' => [
                    'url'    => '',
                    'width'  => '320',
                    'height' => '320',
                    'pause'  => true
                ]
            ]
        ];
        $c['popover'] = $this->get($config, 'popover', false);

        return $c;
    }

    /**
     * Get code type.
     *
     * @param           $config
     * @param  string  $type
     *
     * @return string
     * @since 3.0.0
     */
    public function getType($config, $type = ""): string
    {
        return "background";
    }

    /**
     * Get the background value.
     *
     * @param $config
     *
     * @return array|mixed|null
     * @since 3.0.0
     */
    public function getValue($config): ?array
    {
        $value = $this->get($config, "value");

        // if (is_null($value)) {
        $requiredOpacity    = $config['opacity'] ?? false;
        $requiredTransition = $config['transition'] ?? true;

        $value['state'] = [
            'normal' => array_merge([
                'required_opacity'    => $requiredOpacity,
                'opacity'             => 0.5,
                'required_transition' => $requiredTransition,
                'transition'          => 0
            ],
                $this->defaultProperties()),

            'hover' => array_merge([
                'required_opacity'    => $requiredOpacity,
                'opacity'             => 0.5,
                'required_transition' => $requiredTransition,
                'transition'          => 0
            ],
                $this->defaultProperties())
        ];

        // }

        return $value;
    }

    /**
     * Get default properties.
     *
     * @since 3.0.0
     */
    protected function defaultProperties(): array
    {
        $requiredParallax = isset($this->config['parallax']) ? $this->config['parallax'] : true;
        $jsParallax       = isset($this->config['jsparallax']) ? $this->config['jsparallax'] : true;

        return [
            "type"       => "classic",
            "properties" => [
                'color_1'           => '',
                'color_2'           => '#f36',
                'type'              => 'linear',
                'color'             => '',
                'url'               => '',
                'width'             => '',
                'height'            => '',
                'pause'             => '',
                'src'               => '',
                'size'              => 'cover',
                'position'          => 'center',
                'repeat'            => 'no-repeat',
                'blend'             => 'normal',
                'required_parallax' => $requiredParallax,
                'parallax'          => $requiredParallax,
                'parallax_method'   => 'css',
                'jsparallax'        => $jsParallax,
                "direction"         => 0,
                "start_position"    => 0,
                "end_position"      => 100,
                "overlay"           => false,
                'js_parallax_y'     => '',
                'js_parallax_x'     => '',

            ]
        ];
    }

    /**
     * Determine background required type.
     *
     * @param $config
     * @param $type
     *
     * @return bool
     * @since 3.0.0
     */
    protected function isRequireType($config, $type): bool
    {
        if (isset($config[$type]) && $config[$type] === true) {
            return true;
        }

        if (isset($config[$type]) && $config[$type] === false) {
            return false;
        }

        return "video" !== $type;
    }
}
