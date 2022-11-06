<?php

namespace QuixNxt\FormEngine\Contracts;

use ReflectionClass;

abstract class ControlsTransformer
{
  /**
   * @var \QuixNxt\FormEngine\Contracts\ControlTransformer[]
   *
   * @since 3.0.0
   */
    protected $transformers = [];
    protected $transformerMap = [];

  /**
   * Get a transformer instance from registry
   *
   * @param   string|null  $type
   *
   * @return \QuixNxt\FormEngine\Contracts\ControlTransformer|null
   *
   * @throws \ReflectionException
   * @since 3.0.0
   */
    protected function getTransformerFor(?string $type = null): ?ControlTransformer
    {
        $transformer = $this->transformerMap[$type] ?? null;

        if (! $transformer) {
            $default = $this->transformerMap['default'] ?? null;
            if (! $default) {
                return null;
            }
            $transformer = $default;
        }

        $instance = $this->transformers[$transformer];

        if (is_object($instance)) {
            return $instance;
        }

        $reflection = new ReflectionClass($instance);
        if (! $reflection->isInstantiable()) {
            return null;
        }

        $constructor = $reflection->getConstructor();
        $params      = [];
        if ($constructor) {
            $props = $constructor->getParameters();
            foreach ($props as $prop) {

                // Deprecated: Method ReflectionParameter::getClass
                // deprecation removed since quix4@rc1
                // $class = $prop->getClass();
                $class = $prop->getType() && !$prop->getType()->isBuiltin() ? new ReflectionClass($prop->getType()->getName()) : null;

                if ($class && $class->name === __CLASS__) {
                    $params[] = $this;
                }
            }
        }

      /** @var \QuixNxt\FormEngine\Contracts\ControlTransformer $instance */
        $instance                         = $reflection->newInstanceArgs($params);
        $this->transformers[$transformer] = $instance;

        return $instance;
    }

  /**
   * Add transformer to the map of transformers
   *
   * @param   string  $type
   * @param   string  $transformer
   *
   * @return $this
   *
   * @since 3.0.0
   */
    protected function add(string $type, string $transformer): self
    {
        $this->transformerMap[$type] = $transformer;

        if (! array_key_exists($transformer, $this->transformers)) {
            $this->transformers[$transformer] = $transformer;
        }

        return $this;
    }

  /**
   * Transform the given controls.
   *
   * @param                $controls
   * @param   string|null  $path
   *
   * @return array
   *
   * @throws \ReflectionException
   * @since 3.0.0
   */
    public function transform($controls, ?string $path): array
    {
        if (is_array($controls)) {
            return array_map(function ($control) use ($path): array {
                return $this->transformControl($control, $path);
            }, $controls);
        }

        return [];
    }

  /**
   * @param   array        $control
   * @param   string|null  $path
   *
   * @return array
   * @throws \ReflectionException
   * @since 3.0.0
   */
    public function transformControl(array $control, ?string $path): array
    {
        $transformer = $this->getTransformerFor($control['type'] ?? 'text');
        if (! $transformer) {
            throw new \Exception('Could not find transformer for '.($control['type'] ?? 'text'));
        }

        return $transformer->transform($control, $path);
    }
}
