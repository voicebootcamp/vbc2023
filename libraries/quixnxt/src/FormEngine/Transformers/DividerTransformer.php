<?php

namespace QuixNxt\FormEngine\Transformers;

use QuixNxt\FormEngine\Contracts\ControlTransformer;

class DividerTransformer extends ControlTransformer
{
  /**
   * Get icon picker type.
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
    return "divider";
  }

  /**
   * @inheritDoc
   *
   * @since 3.0.0
   */
  public function getDescription(array $config, string $default = null): ?string
  {
    return parent::getDescription($config, 'Note');
  }
}
