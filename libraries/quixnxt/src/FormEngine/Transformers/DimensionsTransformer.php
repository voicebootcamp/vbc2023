<?php

namespace QuixNxt\FormEngine\Transformers;

use QuixNxt\FormEngine\Contracts\ControlTransformer;

class DimensionsTransformer extends ControlTransformer
{
  /**
   * Transform text.
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
    $c = parent::transform($config, $path);

    $c['units'] = $this->getUnits($config);

    return $c;
  }

  /**
   * Get units.
   *
   * @param $config
   *
   * @return array
   * @since 3.0.0
   */
  public function getUnits($config): array
  {
    $units = $this->get($config, "units", "px, %");

    return array_map(static function ($value): string {
      return trim($value);
    }, explode(",", $units));
  }

  /**
   * Get the dimensions type.
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
    return "dimensions";
  }

  /**
   * Get the dimensions value.
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

    if ( ! isset($config['responsive'])) {
      $config['responsive'] = false;
    }

    $responsive = true;

    if ($value === null) {
      return [
        "responsive"         => $responsive,
        "responsive_preview" => false,
        "desktop"            => [
          "top"    => "",
          "left"   => "",
          "bottom" => "",
          "right"  => ""
        ],
        "tablet"             => [
          "top"    => "",
          "left"   => "",
          "bottom" => "",
          "right"  => ""
        ],
        "phone"              => [
          "top"    => "",
          "left"   => "",
          "bottom" => "",
          "right"  => ""
        ],
        "unit"               => "px"
      ];
    }

    $defaultValue = (array) $value;

    if ( ! isset($value["responsive_preview"])) {
      $value["responsive_preview"] = false;
      $value["responsive"]         = $responsive;

      $value["tablet"] = [
        "top"    => "",
        "left"   => "",
        "bottom" => "",
        "right"  => ""
      ];

      $value["phone"] = [
        "top"    => "",
        "left"   => "",
        "bottom" => "",
        "right"  => ""
      ];

      $value["unit"] = "px";
    }

    $value = array_merge($value, $defaultValue);

    $value = array_pick($value,
      ["top", "left", "bottom", "right", "desktop", "phone", "tablet", "responsive_preview", "responsive", "unit"],
      true); //exclusive

    return $value;
  }
}
