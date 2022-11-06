<?php

namespace QuixNxt\FormEngine\Transformers;

use QuixNxt\FormEngine\Contracts\ControlTransformer;

class BorderTransformer extends ControlTransformer
{
  /**
   * Transform the given configuration for the group repeater.
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

    $c['popover'] = $this->get($config, 'popover', false);

    return $c;
  }

  /**
   * Get code type.
   *
   * @param           $config
   * @param   string  $type
   *
   * @return string
   *
   * @since 3.0.0
   */
  public function getType($config, $type = ""): string
  {
    return "border";
  }

  /**
   * Get the border value.
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
    if (is_null($value))
    {
      $value['state'] = [
        'normal' => $this->defaultProperties(),

        'hover' => $this->defaultProperties()
      ];
    }

    return $value;
  }

  /**
   * Get default properties.
   *
   * @since 3.0.0
   */
  protected function defaultProperties(): array
  {
    return [
      "properties" => [
        "border_type"   => 'none',
        "border_radius" => [
          "top"    => "",
          "left"   => "",
          "bottom" => "",
          "right"  => "",
          "unit"   => "px"
        ],
        "border_width"  => [
          "top"    => "",
          "left"   => "",
          "bottom" => "",
          "right"  => "",
          "unit"   => "px"
        ],
        "box_shadow"    => [
          'color'      => '',
          'spread'     => 0,
          'blur'       => 10,
          'horizontal' => 0,
          'vertical'   => 0,
          'position'   => 'outline'
        ],
        "border_color"  => "",
        'transition'    => 0.3
      ]
    ];
  }
}
