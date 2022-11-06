<?php

namespace QuixNxt\Image\Config;

use QuixNxt\Image\ResponsiveFactory;

interface ResponsiveFactoryConfigurator
{
    /**
     * ResponsiveFactoryConfigurator constructor.
     *
     * @param array $config
     */
    public function __construct(array $config);

    /**
     * @param ResponsiveFactory $factory
     *
     * @return mixed
     */
    public function configure(ResponsiveFactory $factory);

    /**
     * @return array
     */
    public function getConfig();
}
