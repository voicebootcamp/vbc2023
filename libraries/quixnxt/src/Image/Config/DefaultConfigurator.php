<?php

namespace QuixNxt\Image\Config;

use QuixNxt\Image\Exception\InvalidConfigurationException;
use QuixNxt\Image\ResponsiveFactory;
use QuixNxt\Image\Scaler\AbstractScaler;
use QuixNxt\Image\Scaler\FileSizeScaler;
use QuixNxt\Image\Scaler\Scaler;
use QuixNxt\Image\Scaler\SizesScaler;
use QuixNxt\Image\Scaler\WidthScaler;

class DefaultConfigurator implements ResponsiveFactoryConfigurator
{
    /**
     * The default config
     *
     * @var array
     */
    protected $config = [
        'driver' => 'gd',
        'publicPath' => './',
        'sourcePath' => './',
        'rebase' => false,
        'enableCache' => false,
        'optimize' => false,
        'scaler' => 'filesize',
        'stepModifier' => 0.5,
        'minFileSize' => 5000,
        'maxFileSize' => null,
        'minWidth' => 300,
        'maxWidth' => null,
        'sizes' => [],
        'optimizerOptions' => [],
        'includeSource' => true,
    ];

    /**
     * ResponsiveFactoryConfigurator constructor.
     *
     * @param array $config
     *
     * @throws InvalidConfigurationException
     */
    public function __construct(array $config = [])
    {
        if (isset($config['driver']) && !in_array($config['driver'], ['gd', 'imagick'])) {
            throw new InvalidConfigurationException('Invalid driver. Possible drivers are `gd` and `imagick`');
        }

        $this->config = array_merge($this->config, $config);
    }

    /**
     * @param ResponsiveFactory $factory
     *
     * @return void
     */
    public function configure(ResponsiveFactory $factory)
    {
        /** @var AbstractScaler $scaler */
        switch ($this->config['scaler']) {
            case 'filesize':
                $scaler = new FileSizeScaler($this);
                break;
            case 'width':
                $scaler = new WidthScaler($this);
                break;
            case 'sizes':
            default:
                $scaler = new SizesScaler($this);
                $scaler->setSizes($this->config['sizes']);
                break;
        }

        $factory
            ->setDriver($this->config['driver'])
            ->setPublicPath($this->config['publicPath'])
            ->setSourcePath($this->config['sourcePath'])
            ->setRebase($this->config['rebase'])
            ->setEnableCache($this->config['enableCache'])
            ->setOptimize($this->config['optimize'])
            ->setOptimizerOptions($this->config['optimizerOptions'])
            ->setScaler($scaler);
    }

    /**
     * @param Scaler $scaler
     *
     * @return Scaler
     */
    public function configureScaler(Scaler $scaler)
    {
        $scaler
            ->setIncludeSource($this->config['includeSource'])
            ->setMinFileSize($this->config['minFileSize'])
            ->setMinWidth($this->config['minWidth'])
            ->setMaxFileSize($this->config['maxFileSize'])
            ->setMaxWidth($this->config['maxWidth'])
            ->setStepModifier($this->config['stepModifier']);

        return $scaler;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param $key
     *
     * @return mixed|null
     */
    public function get($key)
    {
        return isset($this->config[$key]) ? $this->config[$key] : null;
    }
}
