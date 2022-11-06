<?php

namespace QuixNxt\FormEngine\Transformers;

use QuixNxt\FormEngine\Contracts\ControlTransformer;

class SwitchTransformer extends ControlTransformer
{
  /**
   * Get switch value.
   *
   * @param $config
   *
   * @return mixed|null
   *
   * @since 3.0.0
   */
  public function getValue($config)
  {
    return $this->get($config, 'value', false);
  }
}
