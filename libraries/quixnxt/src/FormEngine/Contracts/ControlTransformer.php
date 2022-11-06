<?php

namespace QuixNxt\FormEngine\Contracts;

abstract class ControlTransformer
{
  /**
   * Transformer.
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
        $output = [];

        $output['name']        = $this->getName($config);
        $output['label']       = $this->getLabel($config);
        $output['class']       = $this->getClass($config);
        $output['help']        = $this->getHelp($config);
        $output['value']       = $this->getValue($config);
        $output['default']     = $this->getValue($config);
        $output['schema']      = $this->getSchema($config);
        $output['placeholder'] = $this->getPlaceholder($config);
        $output['type']        = $this->getType($config);
        $output['advanced']    = $this->get($config, 'advanced', false);
        $output['depends']     = $this->getDepends($config);
        $output['reset']       = $this->get($config, 'reset', false);
        $output['description']      = $this->getDescription($config);

        return $output;
    }

  /**
   * Get value from the configuration file.
   *
   * @param $config
   *
   * @return mixed|null
   *
   * @since 3.0.0
   */
    public function getValue($config)
    {
        return $this->get($config, 'value', "");
    }

  /**
   * Get label from the configuration file.
   *
   * @param         $config
   * @param   null  $label
   *
   * @return mixed|null
   *
   * @since 3.0.0
   */
    public function getLabel($config, $label = null)
    {
        if (! $label) {
            $label = ucfirst(str_replace("_", " ", $config['name']));
        }

        return $this->get($config, 'label', $label);
    }

  /**
   * Get placeholder from the configuration file.
   *
   * @param $config
   *
   * @return mixed|null
   *
   * @since 3.0.0
   */
    public function getPlaceholder($config)
    {
        return $this->get($config, 'placeholder');
    }

  /**
   * Get help from the configuration file.
   *
   * @param $config
   *
   * @return mixed|null
   *
   * @since 3.0.0
   */
    public function getHelp($config)
    {
        return $this->get($config, 'help');
    }

  /**
   * Get class from the configuration file.
   *
   * @param         $config
   * @param   null  $klass
   *
   * @return string
   *
   * @since 3.0.0
   */
    public function getClass($config, $klass = null): string
    {
        if (! $klass) {
            $klass = "fe-control-".$this->getType($config)." fe-control-name-".$this->getName($config);
        }

        return $klass." ".$this->get($config, 'class', '');
    }

  /**
   * Get schema from the configuration file.
   *
   * @param $config
   *
   * @return mixed|null
   *
   * @since 3.0.0
   */
    public function getSchema($config)
    {
        return $this->get($config, 'schema', []);
    }

  /**
   * Get type from the configuration file.
   *
   * @param           $config
   * @param   string  $type
   *
   * @return mixed|null
   *
   * @since 3.0.0
   */
    public function getType($config, $type = "text")
    {
        return $this->get($config, 'type', $type);
    }

  /**
   * Get name from the configuration file.
   *
   * @param $config
   *
   * @return mixed|null
   *
   * @since 3.0.0
   */
    public function getName($config)
    {
        return $this->get($config, 'name');
    }

  /**
   * Get depends from the configuration file.
   *
   * @param $config
   *
   * @return array|mixed|null
   *
   * @since 3.0.0
   */
    public function getDepends($config): ?array
    {
        $depends = $this->get($config, 'depends', []);

        if (! is_array($depends)) {
            return [
            $depends => "*",
            ];
        }

        return $depends;
    }

  /**
   * Get description of the node
   *
   * @param   array        $config
   * @param   string|null  $default
   *
   * @return string|null
   *
   * @since 3.0.0
   */
    public function getDescription(array $config, string $default = null): ?string
    {
        return $this->get($config, 'description', $default);
    }

  /**
   * Get data from the configuration by the given key.
   *
   * @param         $config
   * @param         $key
   * @param   null  $default
   *
   * @return mixed|null
   *
   * @since 3.0.0
   */
    public function get($config, $key, $default = null)
    {
        return array_get($config, $key, $default);
    }
}
