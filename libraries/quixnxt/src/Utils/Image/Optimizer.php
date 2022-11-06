<?php

namespace QuixNxt\Utils\Image;

use Joomla\Filesystem\Exception\FilesystemException;
use RuntimeException;

class Optimizer
{
    private const RESPONSIVE_IMAGE_STORAGE_PATH = JPATH_ROOT.'/media/quixnxt/storage/images/';
    private const SOURCE_ROOT = JPATH_ROOT.'/';

    private $source;
    private $format;

    private $enhance = false;
    private $quality = 75;
    private $compress = false;

    private $max_width = 3840;
    private $min_width = 320;

    private $min_file_size = 1024 * 40; // 40kb

    private $time;

    public static $enabled = true;
    public static $webp_enabled = true;

    /**
     * Optimizer constructor.
     *
     * @param  string  $source
     *
     * @since 3.0.0
     */
    public function __construct(string $source)
    {
        $this->source = $source;
        $this->_findSource();

        if (preg_match('/^(https?:\/\/)/', $this->source)) {
            throw new RuntimeException('Remote images can not be optimized');
        }

        if ( ! file_exists($this->_getSourcePath())) {
            throw new RuntimeException("Could not find source image: {$this->_getSourcePath()}", 404);
        }

        //To create folders under optimizer root.
        $path_param = pathinfo($this->source, PATHINFO_DIRNAME);


        // if (\str_contains($path_param, 'images/')) { // require php8
        if (strpos($path_param, 'images/') !== false) {
            $path_param = str_replace('images/', '', $path_param);
        } else {
            $path_param = str_replace('images', '', $path_param);
        }

        $folders = explode("/", $path_param);
        $sub_dir = '';

        $folders[0] = str_replace('/', '', $folders[0]);

        foreach ($folders as $f) {
            if ($f != $folders[0]) {
                $sub_dir .= '/';
            }
            $sub_dir .= $f;

            $RESPONSIVE_IMAGE_STORAGE_PATH = self::RESPONSIVE_IMAGE_STORAGE_PATH . $sub_dir;

            try {
                \JFolder::create($RESPONSIVE_IMAGE_STORAGE_PATH);
            }
            catch (FilesystemException $e) {
                \JLog::add(sprintf('Failed to create image folder path %s, Error: %s', $RESPONSIVE_IMAGE_STORAGE_PATH, $e->getMessage()), \JLog::ERROR);
                throw new RuntimeException(sprintf('Failed to create image folder path %s', $RESPONSIVE_IMAGE_STORAGE_PATH), 500);
            }

            $this->time = microtime(true);
        }
    }

    /**
     * @throws \Exception
     * @since 3.0.0
     */
    private function _findSource(): void
    {
        $path         = $this->_getSourcePath();
        $this->format = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $withoutExtension = substr($path, 0, -(strlen($this->format) + 1));

        $files = glob($withoutExtension.'.*');

        if (count($files) > 0) {
            $this->source = substr($files[0], strlen(self::SOURCE_ROOT));
        } else {
            $this->_findSourceWithDirs($path, $withoutExtension);
        }

    }

    /**
     * Find the image path with query params dirs
     *
     * @param $path
     * @param $withoutExtension
     *
     * @throws \Exception
     * @since 3.0.0
     */
    private function _findSourceWithDirs($path, $withoutExtension)
    {
        // we didnt find the image, lets check again
        $dir = \JFactory::getApplication()->input->get('dirs', '', 'string');
        if ($dir) {
            $onlyPath = pathinfo($path, PATHINFO_DIRNAME).'/'.$dir;
            $newPath  = str_replace(JPATH_SITE.'/'.\QuixAppHelper::getJoomlaImagePath(), $onlyPath, $withoutExtension);
            $newFiles = glob($newPath.'*');

            if (count($newFiles) > 0) {
                $this->source = substr($newFiles[0], strlen(self::SOURCE_ROOT));
            }
        }
    }

    /**
     * @return string
     *
     * @since 3.0.0
     */
    private function _getSourcePath(): string
    {

        return self::SOURCE_ROOT.$this->source;
    }

    /**
     * @param  string|null  $suffix
     *
     * @param  string|null  $format
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function _getDestination(string $suffix = null, string $format = null): string
    {
        $destination = pathinfo($this->source, PATHINFO_BASENAME);
        $ext         = pathinfo($this->source, PATHINFO_EXTENSION);
        if ($suffix) {
            $destination = substr($destination, 0, -(strlen($ext) + 1))."_{$suffix}.{$ext}";
        }

        $destination = substr($destination, 0, -(strlen($ext))).($format ?: $this->format);

        return $this->_prepareDirParams($destination);
    }

    /**
     * Add additional directory params to image url
     *
     * @param  string|null  $destination
     *
     * @return string
     * @since 3.0.0
     */
    private function _prepareDirParams(string $destination = null)
    {
        $dirs           = pathinfo($this->source, PATHINFO_DIRNAME);
        $joomlaImageDir = \QuixAppHelper::getJoomlaImagePath();

        if ($dirs !== $joomlaImageDir) {
            $dirs = substr($dirs, strlen($joomlaImageDir) + 1);

            /**
             * based on these docs, raw url encode implemented to path
             * https://www.gyrocode.com/articles/php-urlencode-vs-rawurlencode/
             *
             * @since 4.0.0-beta2
             */
            $destination = rawurlencode($dirs).'/'.$destination;

            return $destination.'&dirs='.rawurlencode($dirs);
        }

        // if it matches the name, we dont need to pass it.
        return $destination;

    }

    /**
     * @param  string|null  $suffix
     *
     * @param  string|null  $format
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function _getDestinationPath(string $suffix = null, string $format = null): string
    {
        $mod_destination = rawurldecode($this->_getDestination($suffix, $format));

        return self::RESPONSIVE_IMAGE_STORAGE_PATH.$mod_destination;
    }

    /**
     * @param  string|null  $size
     *
     * @param  int|null  $quality
     *
     * @return void
     *
     * @throws \ImagickException
     * @since 3.0.0
     */
    public function generate(string $size = null, ?int $quality = null): void
    {
        $path = $this->_getAbsoluteDestinationPath($size);

        if (file_exists($path)) {
            $this->flush($path);

            return;
        }

        if ($size === 'lqi') {
            $this->lqi();
            $this->flush($path);

            return;
        }

        if ($this->isSupported()) {
            $image = (new Image())->read($this->_getAbsoluteSourcePath());

            if ($this->format === 'jpg' || $this->format === 'jpeg') {
                $image->interlace(true);
            }
            $image->compress();
            $image->resize($size);
            $image->write($quality ?: $this->quality, $path);

            $this->flush($path);

            return;
        }

        throw new RuntimeException('Unsupported image');
    }

    private function _getAbsoluteDestinationPath($size): string
    {
        $path = $this->_getDestinationPath($size);
        $path = explode('&', $path);

        return $path[0];
    }

    private function _getAbsoluteSourcePath(): string
    {
        $path = $this->_getSourcePath();
        $path = explode('&', $path);

        return $path[0];
    }

    /**
     * @param  string  $path
     *
     * @since 3.0.0
     */
    private function flush(string $path): void
    {
        $mime = mime_content_type($path);
        header("Content-Type: {$mime}");
        readfile($path);
    }

    /**
     * @param  int  $quality
     *
     * @param  bool  $base64
     *
     * @return string
     * @throws \ImagickException
     * @since 3.0.0
     */
    public function lqi(int $quality = 1, bool $base64 = false): string
    {
        $lqiFile = $this->_getAbsoluteDestinationPath('lqi');
        if ( ! file_exists($lqiFile)) {
            $image = (new Image())->read($this->_getAbsoluteSourcePath());
            $image->resize(min(800, $image->getWidth()));
            $image->blur(50);
            $image->write($quality, $lqiFile);
        }
        if ($base64) {
            $mime = mime_content_type($lqiFile);
            $b64  = base64_encode(file_get_contents($lqiFile));

            return "data:{$mime};base64,{$b64}";
        }

        return explode(' ', $this->getSrc('lqi'))[0];
    }

    /**
     * @param  bool  $isSrcset
     *
     * @param  bool  $is_webp
     *
     * @return array
     *
     * @since 3.0.0
     */
    public function sizes(bool $isSrcset = false, bool $is_webp = false): array
    {
        $sizes = Image::calculateSizes($this->_getSourcePath(), $this->max_width);
        if ($isSrcset) {
            $srcset = [];

            foreach ($sizes as $size) {
                if ( ! $size) {
                    continue;
                }
                $srcset[] = $this->getSrc($size, $is_webp);
            }

            return $srcset;
        }

        return $sizes;
    }

    /**
     * @param  string  $suffix
     *
     * @param  bool  $is_webp
     *
     * @return string
     *
     * @since 3.0.0
     */
    public function getSrc(string $suffix, bool $is_webp = false): string
    {
        $imageSrc = substr($this->_getDestinationPath($suffix, $is_webp ? 'webp' : null), strlen(JPATH_SITE) + 1);
        $imageSrc = $this->_getApiPrefix().$imageSrc;

        return "{$imageSrc} {$suffix}w";
    }

    /**
     * @return string
     *
     * @since 3.0.0
     */
    private function _getApiPrefix(): string
    {
        return \JUri::root().'index.php?quix-image=';
    }

    /**
     * @return string
     *
     * @since 3.0.0
     */
    private function _getCaptureParams(): string
    {
        return '?option=com_quix&task=image';
    }

    /**
     * @return string
     *
     * @throws \ImagickException
     * @since 3.0.0
     */
    public function generatePlaceholder(): string
    {
        $this->lqi();

        $lqiFile = $this->_getAbsoluteDestinationPath('lqi');
        $mime    = mime_content_type($lqiFile);
        $b64     = base64_encode(file_get_contents($lqiFile));

        return "data:{$mime};base64,{$b64}";
    }

    /**
     * @param $width
     * @param $height
     *
     * @return string
     *
     * @since 3.0.0
     */
    public static function placeholder(int $width, int $height): string
    {
        $svg = '<svg viewBox="0 0 '.$width.' '.$height.'" xmlns="http://www.w3.org/2000/svg"><rect fill-opacity="0" /></svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    /**
     * @return array
     *
     * @since 3.0.0
     */
    public function calculateSize(): array
    {
        [$width, $height] = @getimagesize($this->_getSourcePath());

        if ($this->max_width) {
            $height = ceil(($this->max_width / $width) * $height);
            $width  = $this->max_width;
        }

        return [$width, $height];
    }

    /**
     * @return bool
     * @since 3.0.0
     */
    public function isSupported(): bool
    {
        return Image::isSupported($this->format) && ! Image::isAnimated($this->_getSourcePath());
    }

    /**
     * @return bool
     *
     * @since 3.0.0
     */
    public function shouldOptimize(): bool
    {
        return filesize($this->_getSourcePath()) >= $this->min_file_size;
    }

    /**
     * @param  string|null  $format
     *
     * @return bool
     *
     * @since 3.0.0
     */
    public function supported(string $format = null): bool
    {
        return Image::isSupported($format ?: $this->format);
    }
}
