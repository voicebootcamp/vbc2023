<?php

namespace QuixNxt\FormEngine\Transformers;

use QuixNxt\FormEngine\Contracts\ControlTransformer;

class MediaTransformer extends ControlTransformer
{
    /**
     * Get file manager type.
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
        return "media";
    }

    /**
     * Transform the choose.
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
        $c            = parent::transform($config, $path);
        $defaultValue = $this->getValue($config);
        $c['filters'] = $this->getFilters($config);
        $c['value']   = [
            'source'      => ! is_array($defaultValue) ? $defaultValue : $defaultValue['source'],
            'base_domain' => '',
            "properties"  => [ // svg property
                "size"  => 30,
                "color" => 'rgba(0,0,0,1)',
            ],
            'type'        => 'image',
            'dimension'   => [ // image dimension @since 3.0.0
                'width'  => '',
                'height' => '',
            ],
        ];

        // set hideStyle
        $c['showStyle'] = $this->get($config, "showstyle", false);

        return $c;
    }

    /**
     * Get filters
     *
     * @param $config
     *
     * @return mixed|null
     * @since 3.0.0
     */
    public function getFilters($config)
    {
        return $this->get($config, "filters", "image,icon,video,unsplash");
    }
}
