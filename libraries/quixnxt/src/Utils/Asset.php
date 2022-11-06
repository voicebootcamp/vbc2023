<?php

namespace QuixNxt\Utils;

class Asset
{
    public const QUIX_ASSET_REQUEST_KEY = 'quix-asset';
    private static $accepts_encoding;

    /**
     * @param  string  $encoding
     *
     * @return bool
     *
     * @since 3.0.0
     */
    private static function acceptsEncoding(string $encoding): bool
    {
        /* added to avoid php warning */
        if ( ! isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
            return true;
        }

        if ( ! isset(static::$accepts_encoding)) {
            static::$accepts_encoding = array_map('trim', explode(',', $_SERVER['HTTP_ACCEPT_ENCODING']));
        }

        return in_array($encoding, static::$accepts_encoding, true);
    }

    /**
     * @return string
     *
     * @since 3.0.0
     */
    public static function getVersion(): string
    {
        if ( ! QUIXNXT_DEBUG) {
            return QUIXNXT_VERSION;
        }

        return \JFactory::getDocument()->getMediaVersion() ?? QUIXNXT_VERSION;
    }

    /**
     * Define constant QUIXNXT_DEVELOPMENT on your joomla root to avoid caching...
     *
     * @param  string  $asset
     * @param  bool  $directUrl
     *
     * @return string
     *
     * @since 3.0.0
     */
    public static function getAssetUrl(string $asset, bool $directUrl = false): string
    {
        $key     = static::QUIX_ASSET_REQUEST_KEY;
        $version = static::getVersion();
        if ( ! $directUrl) {
            return \JUri::root()."index.php?{$key}={$asset}&ver={$version}";
        } else {
            return \JUri::root()."media/quixnxt{$asset}?ver={$version}";
        }
    }

    /**
     * @param  string  $asset
     *
     * @since 3.0.0
     */
    public static function load(string $asset): void
    {
        $config = \JComponentHelper::getParams('com_quix');
        $path   = \QuixAppHelper::getQuixMediaPath().'/'.ltrim($asset, '/');

        self::preventJailbreak(\QuixAppHelper::getQuixMediaPath(), $path);

        if ( ! file_exists($path)) {
            header('Status: 404 Not Found');
            exit(0);
        }

        $content_encoding = null;
        $content_type     = 'text/plain';
        $version          = static::getVersion();
        $ext              = pathinfo($asset, PATHINFO_EXTENSION);

        switch ($ext) {
            case 'css':
                $content_type = 'text/css';
                break;
            case 'js':
                $content_type = 'text/javascript';
                break;
        }

        /* added ti avoid conflict encoded css/js after testing on litespeed server */
        if ($config->get('safemode', '0') === '0') {
            if (static::acceptsEncoding('br') && file_exists($path.'.br')) {
                $path             .= '.br';
                $content_encoding = 'br';
            } elseif (static::acceptsEncoding('gzip') && file_exists($path.'.gz')) {
                $path             .= '.gz';
                $content_encoding = 'gzip';
            }
        }

        if ($content_encoding) {
            header("Content-Encoding: {$content_encoding}"); // for browser to decompress

            /* @bug found when enabled? what to do? */
            // header("Transfer-Encoding: {$content_encoding}"); // for browser to decompress
        }

        header("Content-Type: {$content_type}"); // for browser to parse
        header("Version: {$version}");
        header('Vary: Accept-Encoding, Content-Type, Version'); // for CDN to cache and invalidate
        header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (86400 * 30))); // 30 days
        if ( ! QUIXNXT_DEBUG) {
            header('Cache-Control: public, max-age: 31526000');
        }

        readfile($path);

        exit(0);
    }

    public static function preventJailbreak($root, $destination)
    {
        if (strpos($destination, $root) !== 0) {
            die('LFI detected');
        }
    }
}
