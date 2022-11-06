<?php

namespace QuixNxt\Config;

use Symfony\Component\Yaml\Yaml;
use QuixNxt\Config\Contracts\TransformerInterface;
use QuixNxt\FormEngine\FormEngine;

class Transformer implements TransformerInterface
{
    /**
     * Instance of form engine.
     *
     * @var \QuixNxt\FormEngine\FormEngine
     *
     * @since 3.0.0
     */
    protected $formEngine;

    /**
     * Append form.
     *
     * @var array
     *
     * @since 3.0.0
     */
    protected $appendForm = [];

    /**
     * @var array
     *
     * @since 3.0.0
     */
    private static $instances = [];

    /**
     * @var string
     *
     * @since 4.0.0
     */
    private static $elementIconPath = JPATH_SITE . '/media/quixnxt/images/elements';

    /**
     * Create a new instance of Transformer.
     *
     * @param  \QuixNxt\FormEngine\FormEngine|null  $formEngine
     *
     * @since 3.0.0
     */
    public function __construct(?FormEngine $formEngine = null)
    {
        $this->formEngine = $formEngine ?? FormEngine::getInstance();
    }

    /**
     * @param  string|null  $builder
     *
     * @return static
     *
     * @since 3.0.0
     */
    public static function getInstance(?string $builder = 'frontend'): self
    {
        if ( ! array_key_exists($builder, self::$instances)) {
            self::$instances[$builder] = new static(FormEngine::getInstance($builder));
        }

        return self::$instances[$builder];
    }

    /**
     * Transform element configuration.
     *
     * @param                $config
     * @param  string|null  $path
     *
     * @return mixed
     *
     * @throws \ReflectionException
     * @since 3.0.0
     */
    public function transform($config, string $path = null)
    {
        $config['element_path']       = $path;
        $config['view_file']          = $this->getView($config);
        $config['thumb_file']         = $this->getThumbnail($config);
        $config['css_file']           = $this->getCss($config);
        $config['dynamic_style_file'] = $this->getStyle($config);
        $config['groups']             = $this->getGroups($config);
        $config['form']               = $this->getForm($config);
        $config['visibility']         = $this->getVisibility($config);
        $config['template_file']      = $this->getTemplate($config);

        return $config;
    }

    /**
     * Getting form.
     *
     * @param $config
     *
     * @return mixed
     *
     * @throws \ReflectionException
     * @since 3.0.0
     */
    public function getForm($config)
    {
        $form = $this->prepareFormForControlTransformation($config);
        $path = array_get($config, 'element_path', []);

        return $this->formEngine->transform($form, $path);
    }

    /**
     * Append form schema from YML configuration file
     *
     * @param  array  $config
     *
     * @return array
     *
     * @since 3.0.0
     */
    private function prepareFormForControlTransformation(array $config): array
    {
        $form = array_get($config, 'form', []);

        // override element yml form data
        if ($config['slug'] === 'section') {
            $overridableYmlPath = \QuixAppHelper::getQuixPath().'/visual-builder/shared/section.yml';
        } elseif ($config['slug'] === 'row') {
            $overridableYmlPath = \QuixAppHelper::getQuixPath().'/visual-builder/shared/row.yml';
        } else {
            $overridableYmlPath = \QuixAppHelper::getQuixPath().'/visual-builder/shared/element.yml';
        }

        if (file_exists($overridableYmlPath)) {
            $parsedElementYml = Yaml::parse(file_get_contents($overridableYmlPath));

            if ( ! is_null($parsedElementYml)) {
                $this->appendForm = array_merge($this->appendForm, $parsedElementYml);
            }
        }

        // Local copy
        $appendForm = $this->appendForm;

        // If there is something in the config file then add it to the append file
        foreach ($appendForm as $tab => $controls) {
            if (array_key_exists($tab, $form)) {
                if (is_array($form[$tab])) {
                    $appendForm[$tab] = array_merge($controls, $form[$tab]);
                } else {
                    $appendForm[$tab] = array_merge($controls, []);
                }
            }
        }

        // Merge all
        return array_merge($form, $appendForm);
    }

    /**
     * Get view.
     *
     * @param $config
     *
     * @return string
     *
     * @since 3.0.0
     */
    protected function getView($config): string
    {
        if (array_get($config, 'view')) {
            return $config['path'].'/'.$config['view'];
        }

        return $config['path'].'/view.php';
    }

    /**
     * Get Template.
     *
     * @param $config
     *
     * @return string
     *
     * @since 3.0.0
     */
    protected function getTemplate($config): string
    {
        if (array_get($config, 'template_file')) {
            return $config['path'].'/'.$config['template_file'];
        }

        return $config['path'].'/element.php';
    }

    /**
     * Get thumbnail.
     *
     * @param $config
     *
     * @return string
     *
     * @since 3.0.0
     */
    protected function getThumbnail($config): string
    {
        if (file_exists( self::$elementIconPath .'/' . $config['slug'] . '.svg')) {
            return \JUri::root() .'media/quixnxt/images/elements/' . $config['slug'] . '.svg';
        }

        if (array_get($config, 'thumb')) {
            return $config['url'].'/'.$config['thumb'];
        }

        if (file_exists($config['path'].'/element.svg')) {
            return $config['url'].'/element.svg';
        }

        if (file_exists($config['path'].'/element.png')) {
            return $config['url'].'/element.png';
        }

        return QUIXNXT_DEFAULT_ELEMENT_IMAGE;
    }

    /**
     * Get style.
     *
     * @param $config
     *
     * @return string
     *
     * @since 3.0.0
     */
    protected function getStyle($config): string
    {
        if (array_get($config, 'style')) {
            return $config['path'].'/'.$config['style'];
        }

        return $config['path'].'/style.php';
    }

    /**
     * Get css.
     *
     * @param $config
     *
     * @return string
     *
     * @since 3.0.0
     */
    protected function getCss($config): string
    {
        if (array_get($config, 'css')) {
            return $config['url'].'/'.$config['css'];
        }

        return $config['url'].'/element.css';
    }

    /**
     * Get groups.
     *
     * @param $config
     *
     * @return array
     *
     * @since 3.0.0
     */
    protected function getGroups($config): array
    {
        return (array) array_get($config, 'groups', []);
    }

    /**
     * Get visibility.
     *
     * @param $config
     *
     * @return array
     *
     * @since 3.0.0
     */
    protected function getVisibility($config): array
    {
        return ['lg' => true, 'md' => true, 'sm' => true, 'xs' => true];
    }

    /**
     * get the builder type
     *
     * @return string
     *
     * @since 3.0.0
     */
    public function getBuilder(): string
    {
        return $this->formEngine->builder;
    }
}
