<?php

namespace QuixNxt\FormEngine\Transformers;

use QuixNxt\FormEngine\Contracts\ControlsTransformer;
use QuixNxt\FormEngine\Contracts\ControlTransformer;

class FieldsGroupTransformer extends ControlTransformer
{
  /**
   * Instance of ControlsTransformer.
   *
   * @var \QuixNxt\FormEngine\Contracts\ControlsTransformer
   *
   * @since 3.0.0
   */
  protected $controlsTransformer;

  /**
   * Create a new instance of group repeater transformer.
   *
   * @param $controlsTransformer
   *
   * @since 3.0.0
   */
  public function __construct(ControlsTransformer $controlsTransformer)
  {
    $this->controlsTransformer = $controlsTransformer;
  }

  /**
   * Get fields group type.
   *
   * @param           $config
   * @param   string  $type
   *
   * @return string
   *
   * @since 3.0.0
   */
  public function getType($config, $type = "text"): string
  {
    return "fields-group";
  }

  /**
   * Transform the group repeater.
   *
   * @param   array        $config
   * @param   string|null  $path
   *
   * @return array
   *
   * @throws \ReflectionException
   * @since 3.0.0
   */
  public function transform(array $config, ?string $path): array
  {
    $c = parent::transform($config, $path);

    foreach ($c['schema'] as $key => $schema)
    {
      $c['schema'][$key] = $this->controlsTransformer->transformControl($schema, $path);
    }

    $c['value']  = $c['schema'];
    $c['status'] = $config['status'] ?? 'close';

    return $c;
  }
}
