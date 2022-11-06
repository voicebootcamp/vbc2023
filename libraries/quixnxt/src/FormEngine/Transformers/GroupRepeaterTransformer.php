<?php

namespace QuixNxt\FormEngine\Transformers;

use QuixNxt\FormEngine\Contracts\ControlsTransformer;
use QuixNxt\FormEngine\Contracts\ControlTransformer;

class GroupRepeaterTransformer extends ControlTransformer
{
  /**
   * Instance of ControlsTransformer.
   *
   * @var \QuixNxt\FormEngine\Contracts\ControlsTransformer
   * @since 3.0.0
   */
  protected $controlsTransformer;

  protected $path;

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
   * Transform the given configuration for the group repeater.
   *
   * @param  array  $config
   * @param  string|null  $path
   *
   * @return array
   * @since 3.0.0
   */
  public function transform(array $config, ?string $path): array
  {
    $c          = parent::transform($config, $path);
    $this->path = $path;
    if (count($c['value']) === 0) {
      $c['value'][] = $c['schema'];
    }

    return $c;
  }

  /**
   * Get the group repeater schema.
   *
   * @param               $config
   * @param  array|null  $schema
   *
   * @return array
   * @throws \ReflectionException
   * @since 3.0.0
   */
  public function getSchema($config, ?array $schema = []): array
  {
    $schema = $this->get($config, 'schema', $schema);
    $schema = array_map(function ($control) {
      $control['depends'] = $this->getDepends($control);

      return $control;
    }, $schema);

    return $this->controlsTransformer->transform($schema, $this->path);
  }

  /**
   * Get the group repeater value.
   *
   * @param $config
   *
   * @return array
   * @throws \ReflectionException
   * @since 3.0.0
   */
  public function getValue($config): array
  {
    $schema = $this->getSchema($config);

    return array_map(function ($group) use ($schema): array {

      $controls = array_map(function ($control) use ($group, $schema) {
        $control['value'] = $this->get($group, $control['name'], $control['value']);

        return $control;
      }, $schema);

      return $this->controlsTransformer->transform($controls, $this->path);
    }, $this->get($config, 'value', []));
  }
}
