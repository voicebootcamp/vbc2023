<?php

namespace QuixNxt\FormEngine\Transformers;

use QuixNxt\FormEngine\Contracts\ControlTransformer;

class TypographyTransformer extends ControlTransformer
{
    /**
     * Get type for the typography.
     *
     * @param        $config
     * @param string $type
     *
     * @return string
     *
     * @since 3.0.0
     */
    public function getType($config, $type = ""): string
    {
        return "typography";
    }

  /**
   * Transform the slider.
   *
   * @param   array        $config
   * @param   string|null  $path
   *
   * @return array
   *
   * @since 3.0.0
   */
    public function transform(array $config, ?string $path): array
    {
        $c = parent::transform($config, $path);

        $c['default'] = $this->getDefaultValue();
        $c['units'] = $this->getUnits($config);
        $c['popover'] = $this->get($config, 'popover', false);

        return $c;
    }

  /**
   * Get units.
   *
   * @param $config
   *
   * @return array|null
   * @since 3.0.0
   */
    public function getUnits(array $config): ?array
    {
        $units = $this->get($config, "units", "px, %");

        if(is_array($units))
        {
          return $units;
        }

        return array_map(static function($value): string {
            return trim($value);
        }, explode(",", $units));
    }

    /**
     * Get default value.
     *
     * @since 3.0.0
     */
    public function getDefaultValue(): array
    {
        return [
            "family" => "",
            "weight" => "",
            "size" => [
                'desktop' => 0,
                'tablet' => 0,
                'phone' => 0,
                'unit' => 'px'
            ],
            "transform" => '',
            "style" =>'',
            "decoration" =>'',
            "spacing" => [
                'desktop' => 0,
                'tablet' => 0,
                'phone' => 0,
                'unit' => 'px'
            ],
            "height" => [
                'desktop' => 0,
                'tablet' => 0,
                'phone' => 0,
                'unit' => 'em'
            ],
            "text_shadow" => [
                'color' => '',
                'blur' => 10,
                'horizontal' => 0,
                'vertical' => 0
            ],
        ];
    }

    /**
     * Get typography value.
     *
     * @param $config
     *
     * @return array|mixed|null
     *
     * @since 3.0.0
     */
    public function getValue($config): ?array
    {
        $value = $this->get($config, "value");

        if ($value === null) {
            return $this->getDefaultValue();
        }

      $value = (array)$value;

      $value = array_pick($value, [
            "family",
            "weight",
            "size",
            "transform",
            "style",
            "decoration",
            "spacing",
            "height",
            "text_shadow",
        ], true); //exclusive

        return $value;
    }

    /**
     * Determine element is by default responsive mode.
     *
     * @return bool
     *
     * @since 3.0.0
     */
    public function isResponsive(): bool
    {
        return true;
    }
}
