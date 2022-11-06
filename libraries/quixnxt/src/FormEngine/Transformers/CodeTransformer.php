<?php

namespace QuixNxt\FormEngine\Transformers;

use QuixNxt\FormEngine\Contracts\ControlTransformer;

class CodeTransformer extends ControlTransformer
{
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
    return "code";
  }

  /**
   * Transform the choose.
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

    $c['value'] = [];

    if ($this->getDepends($config))
    {
      $c['depends'] = $this->getDepends($config);
    }

    $c['value'] = $this->getValue($config) === ""
      ? ["code" => "", "mode" => "css"]
      : $this->getValue($config);

    return $c;
  }
}
