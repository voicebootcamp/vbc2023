<?php

namespace QuixNxt\Elements;

class ElementBag extends \QuixNxt\Engine\Support\ElementBag
{
    private static $instance;
    /**
     * Store path bags.
     *
     * @var array
     * @since 3.0.0
     */
    private static $bagsPath = [];

    private function __construct()
    {
        self::register(QuixElement::QUIX_VISUAL_BUILDER_PATH);
        \JPluginHelper::importPlugin('quix');
        \JFactory::getApplication()->triggerEvent('onRegisterQuixElements');
    }

    /**
     * @return static
     *
     * @since 3.0.0
     */
    public static function getInstance(): self
    {
        if (! static::$instance) {
            static::$instance = new static();
            foreach (static::getElements() as $name => $path) {
                static::$instance->add(new QuixElement($name, $path));
            }
        }

        return static::$instance;
    }

    /**
     * @return array
     * @since 3.0.0
     */
    private static function getElements(): array
    {
        $elements = [];
        foreach (static::$bagsPath as $bagPath) {
            $_elements = glob($bagPath.'/*');
            foreach ($_elements as $element) {
                $elementName            = pathinfo($element, PATHINFO_BASENAME);
                $elements[$elementName] = $bagPath.'/'.$elementName;
            }
        }

        return $elements;
    }

    /**
     * Set path bags.
     *
     * @param           $path
     *
     * @since 3.0.0
     */
    public static function register($path): void
    {
        static::$bagsPath[] = $path;
    }
}
