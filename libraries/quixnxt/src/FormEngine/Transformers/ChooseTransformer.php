<?php

namespace QuixNxt\FormEngine\Transformers;

use QuixNxt\FormEngine\Contracts\ControlTransformer;

class ChooseTransformer extends ControlTransformer
{
  /**
   * @var string
   * @since 3.0.0
   */
  protected $defaultDesktopValue = '';

  /**
   * @var string
   * @since 3.0.0
   */
  protected $defaultTabletValue = '';

  /**
   * @var string
   * @since 3.0.0
   */
  protected $defaultPhoneValue = '';

  /**
   * Transform the choose.
   *
   * @param  array  $config
   * @param  string|null  $path
   *
   * @return array
   * @since 3.0.0
   */
  public function transform(array $config, ?string $path): array
  {
    $c = parent::transform($config, $path);

    $c['responsive'] = $config['responsive'] ?? true;

    $c['options'] = $config['options'] ?? [];

    if ($c['responsive']) {
      $value = $c['value'];

      $c['value']['desktop'] = $value['desktop'] ?? $this->defaultDesktopValue;
      $c['value']["tablet"]  = $value['tablet'] ?? $this->defaultTabletValue;
      $c['value']["phone"]   = $value['phone'] ?? $this->defaultPhoneValue;
    }

    $c['default'] = $c['value'];

    return $c;
  }

  /**
   * Get merged value
   *
   * @param $config
   * @param $key
   *
   * @return array|string|string[]
   * @since 3.0.0
   */
  public function getMergedValue($config, $key)
  {
    if (empty($key) || is_array($key)) {
      return $this->defaultDesktopValue;
    }

    return array_merge($config['options'][$key], ["value" => $key]);
  }

  /**
   * Get choose value.
   *
   * @param $config
   *
   * @return mixed|null
   * @since 3.0.0
   */
  public function getValue($config)
  {
    return $this->get($config, 'value', []);
  }
}
