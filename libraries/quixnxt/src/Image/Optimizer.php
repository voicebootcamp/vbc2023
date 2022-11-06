<?php

namespace QuixNxt\Image;

use Exception;
use Intervention\Image\ImageManager as Image;
use Symfony\Component\Filesystem\Filesystem;
use QuixNxt\Image\Config\DefaultConfigurator;

class Optimizer
{
    /**
     * Default Configurations.
     *
     * @var array
     */
    protected $configs = [
        'driver' => 'gd',
        'engine' => 'gd',
        'sourcePath' => __DIR__,
        'publicPath' => __DIR__,
        'sizes' => [
            1920 => 500,
            1600 => 400,
            1366 => 300,
            1024 => 200,
            768 => 100,
        ],
        'optimize' => false,
        'enableCache' => false,
        'stepModifier' => 0.5,
        'scaler' => 'sizes',
        'quality' => 100,
        'includeSource' => false
    ];

    /**
     * Source image file apth.
     *
     * @var string
     */
    protected $path;

    /**
     * Instance of brendt responsive factory
     *
     * @var Brendt\Image\ResponsiveFactory
     */
    protected $responsiveFactory;

    /**
     * Instance of responsive image
     *
     * @var Brendt\Image\Config\DefaultConfigurator
     */
    protected $responsiveImage;

    /**
     * Instance of image optimizer.
     *
     * @var self
     */
    private static $instance = null;

    /**
     * Create a new instance of image optimizer.
     *
     * @param string $path source image file path
     * @param array $configs custom configurations
     */
    public function __construct(string $path, array $configs = [])
    {
        if (!function_exists('exec')) {
            error_reporting(E_ERROR | E_PARSE);
        }

        $this->fs = new Filesystem();

        $this->path = $path;

        $configs['sizes'] = $configs['sizes'] ?? $this->configs['sizes'];
        $configs['sourcePath'] = $configs['cache'];
        $configs['publicPath'] = $configs['cache'];
        arsort($configs['sizes']);

        $this->configs = array_merge($this->configs, $configs);

        $this->bootResponsiveFactory();

        $this->createResponsiveImageFactory();
    }

    /**
     * Creating instance of brendt responsive factory.
     *
     * @return void
     */
    protected function bootResponsiveFactory() : void
    {
        $this->responsiveFactory = new ResponsiveFactory(
            new DefaultConfigurator($this->configs)
        );
    }

    /**
     * Creating responsive image factory.
     *
     * @param string $path image path to generate responsive image
     * @return void
     */
    protected function createResponsiveImageFactory(? string $path = null) : void
    {
        if (is_null($path)) {
            $path = $this->path;
        } else {
            $this->path = $path;
        }

        [$imageExtension, $imageFilePath] = $this->getImageInfo($path);

        $mimetype = $this->getBrowserSupportedExtension() == 'webp' ? 'webp' : 'jpeg';

        // if (!$this->configs['enableCache']) {
        //     $this->setResponsiveImage($imageFilePath, $imageExtension, $mimetype);

        //     if (function_exists('imagewebp')) {
        //         $this->setResponsiveImage($imageFilePath, $imageExtension, $this->getBrowserSupportedExtension());
        //     }
        // } else {
        //     $this->setResponsiveImage($imageFilePath, $imageExtension, function_exists('imagewebp') ? $this->getBrowserSupportedExtension($imageExtension) : $mimetype);
        // }

        $this->setResponsiveImage($imageFilePath, $imageExtension, 'jpeg');

        if (function_exists('imagewebp')) {
            $this->setResponsiveImage($imageFilePath, $imageExtension, 'webp');
        }

        $this->responsiveImage->addSizes($this->getResponsiveImageSizes());
    }

    /**
     * Create a new responsive image.
     *
     * @param string $path image path to generate responsive image
     * @return self
     */
    public function create(string $path) : self
    {
        $this->createResponsiveImageFactory($path);

        return $this;
    }

    /**
     * Get image extension and file path.
     *
     * @param string $path image path
     * @return array
     */
    protected function getImageInfo(string $path) : array
    {
        $pattern = explode('.', $path);
        $imageExtension = array_pop($pattern);
        $imageFilePath = implode('', $pattern);

        return [$imageExtension, $imageFilePath];
    }

    /**
     * Set responsive image instence.
     *
     * @param string image file path
     * @param string image extension
     * @param string browser supported extension
     * @return void
     */
    protected function setResponsiveImage(string $imageFilePath, string $imageExtension, string $browerSupportedExtension) : void
    {
        $image = "{$imageFilePath}.{$browerSupportedExtension}";
        $imagePublicPath = $this->configs['publicPath'] . $image;
        $this->createCacheImage("{$imageFilePath}.{$imageExtension}", $imagePublicPath, $browerSupportedExtension);
        $this->responsiveImage = $this->responsiveFactory->create($image);
    }

    /**
     * Create cached image from origin image.
     *
     * @param string image source path
     * @param string image destination path
     * @param string image mime type
     * @return void
     */
    protected function createCacheImage(string $source, string $destination, string $mimetype) : void
    {
        if (!file_exists($destination) or !$this->configs['enableCache']) {
            $this->fs->dumpFile(
                $destination,
                $this->convertImageFormat("{$this->configs['source']}{$source}", $mimetype)
            );
        }
    }

    /**
     * Determines requested browser will accept WebP format.
     *
     * @return string
     */
    protected function getBrowserSupportedExtension(?string $originalExtension = null) : string
    {
        $extension = 'jpeg';

        $extension = ($originalExtension == 'jpg') ? 'jpg' : $extension;

        if (
            (strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') != false)
            and function_exists('imagewebp')
            and $this->configs['want_webp']
         ) {
            $extension = 'webp';
        }

        return $extension;
    }

    /**
     * Generating responsive image size queries from size configuration.
     *
     * @return array
     */
    protected function getResponsiveImageSizes() : array
    {
        if (!is_array($this->configs['sizes'])) {
            throw new Exception('Must be an array');
        }

        $sizes = [];
        $configSizes = $this->configs['sizes'];
        asort($configSizes);

        foreach ($configSizes as $maxWidth => $imageSize) {
            $imageSize = $imageSize / 2;
            $sizes["max-width: {$maxWidth}px"] = "{$imageSize}px";
        }

        return $sizes;
    }

    /**
     * Getting default image url.
     *
     * @return string
     */
    public function src() : string
    {
        return $this->responsiveImage->src();
    }

    /**
     * Getting response images set.
     *
     * @return string
     */
    public function srcset() : string
    {
        $imageFileName = explode('.', $this->path)[0];
        $imageUrl = $this->configs['base_url'] . $imageFileName;

        return $this->replace($imageFileName, $imageUrl, $this->responsiveImage->srcset());
    }

    /**
     * Replaceing content based on the given search and replace value.
     *
     * @param string $search
     * @param string $replace
     * @param string $content
     * @return string
     */
    protected function replace(string $search, string $replace, string $content) : string
    {
        return preg_replace("@($search)@", $replace, $content);
    }

    /**
     * Getting responsive image sizes.
     *
     * @return string
     */
    public function sizes() : string
    {
        return $this->responsiveImage->sizes();
    }

    /**
     * Converting the given image based on the given mimetype
     *
     * @param string $image the image you want to convert
     * @param string $mimetype the mimetype is the image format that you want to convert your given image
     * @return string
     */
    protected function convertImageFormat(string $image, string $mimetype) : string
    {
        [$width, $height] = getimagesize($image);

        // resized if image size greater then 4000px
        if ($width > 2500 or $height > 2500) {
            $width = $width > 2500 ? 2500 : $width;
            $height = $height > 2500 ? 2500 : $height;
            
            $imgManager = new Image();
            // $img = $imgManager->make($image)->resize($width, $height);
            // we are resizing the original image as its too much wide
            $img = $imgManager->make($image)->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            $img->save($image, 100);
            
            [$width, $height] = getimagesize($image);
        }

        [$extension] = $this->getImageInfo($image);

        $frame = imagecreatetruecolor($width, $height);

        // $extension = strtolower($extension);

        $extension = $extension == 'jpg' ? 'jpeg' : $extension;

        $imagecreatefrom = "imagecreatefrom{$extension}";

        if ($mimetype == 'png') {
            imagealphablending($frame, false);
            imagesavealpha($frame, true);
        }

        if (QUIXNXT_DEBUG) {
            ini_set('memory_limit', '-1');
            set_time_limit(0);
        }

        imagecopyresampled($frame, $imagecreatefrom($image), 0, 0, 0, 0, $width, $height, $width, $height);

        ob_start();

        switch ($mimetype) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($frame, null, $this->configs['quality']);
                break;
        //   case 'png':
        //     imagepng($frame, null, 9);
        //     break;
            case 'webp':
                if (function_exists('imagewebp')) {
                    imagewebp($frame, null, $this->configs['quality']);
                }
                break;
            default:
                throw new Exception('Unsupport imageX method!');
                break;
        }

        imagedestroy($image);

        return ob_get_clean();
    }

    /**
     * Getting image optimizer instance.
     *
     * @param string|null $path image file path
     * @param array $configs image optimizer configurations
     * @return self
     * @since 3.0.0
     */
    public static function getInstance(? string $path = null, array $configs = []) : self
    {
        if (!is_null($path) and empty($configs) and !is_null(self::$instance)) {
            return self::$instance->create($path);
        }

        if (!is_null(self::$instance) and is_null($path)) {
            return self::$instance;
        }

        self::$instance = new self($path, $configs);
        return self::$instance;
    }
}
