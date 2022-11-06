<?php

namespace QuixNxt\FormEngine;

use QuixNxt\FormEngine\Contracts\ControlsTransformer;

class FormEngine
{
  /**
   * Instance of controls transformer.
   *
   * @var ControlsTransformer
   *
   * @since 3.0.0
   */
    protected $controlsTransformer;

  /**
   * Builder type
   *
   * @var string
   *
   * @since 3.0.0
   */
    public $builder;

    /**
     * @var \QuixNxt\FormEngine\FormEngine
     *
     * @since 3.0.0
     */
    private static $instances = [];

  /**
   * Create a new instance of form engine.
   *
   * @param   string  $builder
   *
   * @since 3.0.0
   */
    public function __construct(string $builder)
    {
        $this->builder             = $builder;
        $this->controlsTransformer = $this->_getControlsTransformer();
    }

    /**
     * @param  string  $builder
     *
     * @return static
     *
     * @since 3.0.0
     */
    public static function getInstance(string $builder = 'frontend'): self
    {
        if(!array_key_exists($builder, self::$instances)) {
            self::$instances[$builder] = new static($builder);
        }

        return self::$instances[$builder];
    }

  /**
   * @since 3.0.0
   */
    private function _getControlsTransformer(): ControlsTransformer
    {
        if ($this->builder === 'frontend') {
            return new VisualBuilderControlsTransformer();
        }

        return new LegacyControlsTransformer();
    }

  /**
   * Transform the given form.
   *
   * @param $form
   * @param $path
   *
   * @return mixed
   *
   * @throws \ReflectionException
   * @since 3.0.0
   */
    public function transform($form, $path)
    {
        foreach ($form as &$controls) {
            $controls = $this->controlsTransformer->transform($controls, $path);
        }

        return $form;
    }
}
