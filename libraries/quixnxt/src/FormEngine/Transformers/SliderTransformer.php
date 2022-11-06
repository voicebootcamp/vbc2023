<?php

namespace QuixNxt\FormEngine\Transformers;

use QuixNxt\FormEngine\Contracts\ControlTransformer;

class SliderTransformer extends ControlTransformer
{
  /**
   * Transform the slider.
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

    $c['responsive'] = $config['responsive'] ?? true;

    $c['suffix'] = $this->getSuffix($config);
    $c['units']  = $this->getUnits($config);
    $c['min']    = $this->getMin($config);
    $c['max']    = $this->getMax($config);
    $c['step']   = $this->getStep($config);

    if ($this->getDepends($config)) {
      $c['depends'] = $this->getDepends($config);
    }

    $c['default'] = $c['value'];

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
  public function getUnits($config): ?array
  {
    if ( ! isset($config['units']) || ! $config['units']) {
      return null;
    }

    $units = $this->get($config, "units", "px, %");

    if (is_array($units)) {
      return $units;
    }

    return array_map(static function ($value): string {
      return trim($value);
    }, explode(",", $units));
  }

  /**
   * Get max value.
   *
   * @param $config
   *
   * @return mixed|null
   *
   * @since 3.0.0
   */
  public function getMax($config)
  {
    return $this->get($config, 'max', 100);
  }

  /**
   * Get suffix.
   *
   * @param $config
   *
   * @return mixed|null
   *
   * @since 3.0.0
   */
  public function getSuffix($config)
  {
    return $this->get($config, 'suffix', $config['defaultUnit'] ?? 'px');
  }

  /**
   * Get min value.
   *
   * @param $config
   *
   * @return mixed|null
   *
   * @since 3.0.0
   */
  public function getMin($config)
  {
    return $this->get($config, 'min', 0);
  }

  /**
   * Get step.
   *
   * @param $config
   *
   * @return mixed|null
   *
   * @since 3.0.0
   */
  public function getStep($config)
  {
    return $this->get($config, 'step', 1);
  }

  /**
   * Get slider value.
   *
   * @param $config
   *
   * @return mixed|null|int
   *
   * @since 3.0.0
   */
  public function getValue($config)
  {
    $value = $this->get($config, 'value', $this->getMin($config));

    if ( ! $this->get($config, 'responsive', true)) {
      if ( ! $this->get($config, 'units', [])) {
        return $value;
      }

      if ( ! is_array($value)) {
        $c['value'] = [
          'value' => $value,
          'unit'  => $this->get($config, "defaultUnit", "px")
        ];
      } else {
        $c['value'] = $value;
      }

      $c['unit'] = $this->get($config, "defaultUnit", "px");
    } else {
      $c['value']['desktop']            = $value['desktop'] ?? $this->_getResponsiveValue($config);
      $c['value']["tablet"]             = $value['tablet'] ?? $this->_getResponsiveValue($config);
      $c['value']["phone"]              = $value['phone'] ?? $this->_getResponsiveValue($config);
      $c['value']["responsive_preview"] = true;
      $c['value']['unit']               = $this->getSuffix($config);
    }

    return $c['value'];
  }

  /**
   * @param $config
   *
   * @return int
   *
   * @since 3.0.0
   */
  private function _getResponsiveValue($config): int
  {
    return $this->get($config, 'value', $this->getMin($config));
  }
}
