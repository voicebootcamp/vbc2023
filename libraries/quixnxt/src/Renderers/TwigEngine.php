<?php

namespace QuixNxt\Renderers;

use Exception;
use JComponentHelper;
use JHtml;
use Joomla\CMS\Crypt\Crypt;
use JPluginHelper;
use JRegistry;
use JRoute;
use JUri;
use QuixNxt\AssetManagers\ScriptManager;
use QuixNxt\AssetManagers\StyleManager;
use QuixNxt\Engine\Foundation\RenderEngine;
use QuixNxt\Utils\Icon;
use QuixNxt\Utils\Image\Optimizer;
use Twig\Environment;
use Twig\Markup;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigEngine extends RenderEngine
{
    /**
     * @var \Twig\Environment
     * @since 3.0.0
     */
    private $twig;

    /**
     * @var array
     * @since 3.0.0
     */
    private $form;

    /**
     * TwigEngine constructor.
     *
     * @param  \Twig\Environment  $twig
     *
     * @since 3.0.0
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
        $this->prepareTwig();
    }

    /**
     * Access the internal twig instance
     *
     * @return \Twig\Environment
     * @since 3.0.0
     */
    public function getTwig(): Environment
    {
        return $this->twig;
    }

    /**
     * @param  string  $name
     * @param  string  $template
     *
     * @since 3.0.0
     */
    public function registerTemplate(string $name, string $template): void
    {
        // currently, no use
    }


    /**
     * @param  string|null  $acl
     *
     * @return bool
     * @since 4.1
     */
    public function hasAclJPermission(?string $acl = ''): bool
    {
        /**
         * first check ACL
         * If no access filter is set, the layout takes some responsibility for display of limited information.
         */
        $user   = \JFactory::getUser();
        $groups = $user->getAuthorisedViewLevels();
        if ( ! empty($acl) and ! in_array($acl, $user->groups) and ! in_array($acl, $groups)) {
            return false;
        }

        return true;
    }

    /**
     * @param  string  $template
     * @param  array  $data
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @since 3.0.0
     */
    public function render(string $template, array $data = []): string
    {
        $this->form = $data['form'] ?? [];

        if ( ! $this->hasAclJPermission($this->form['advanced']['identifier']['acl'])) {
            return '';
        }

        $data = [
            'general'        => $this->form['general'] ?? [],
            'advanced'       => $this->form['advanced'] ?? [],
            'styles'         => $this->form['styles'] ?? [],
            'visibility'     => $data['visibility'],
            'node'           => ['children' => $data['children'] ?? []],
            'tagName'        => $data['general']['layout_fields_group']['html_tag'] ?? 'div',
            'renderer'       => $data['renderer'] ?? null,
            'style'          => $data['style'] ?? null,
            'backgroundInfo' => $this->form['backgroundInfo'] ?? [],
            'isDynamic'      => $data['isDynamic'] ?? false,
            'mode'           => 'preview',
        ];

        foreach (array_keys($this->form) as $name) {
            if ( ! isset($data[$name])) {
                $data[$name] = $this->form[$name] ?? [];
            }
            continue;
        }

        return $this->twig->render($template, $data);
    }

    /**
     *
     * @since 3.0.0
     */
    private function prepareTwig(): void
    {
        $this->twig->addFunction($this->getFieldFunction());
        $this->twig->addFunction($this->getWrapperFunction());
        $this->twig->addFunction($this->getFormFooterFunction());
        $this->twig->addFunction($this->getImageFunction());
        $this->twig->addFunction($this->getLazyBackgroundFunction());
        $this->twig->addFunction($this->getIfElementHasBackgroundFunction());
        $this->twig->addFunction($this->getIconFunction());
        $this->twig->addFunction($this->getRootUrlFunction());
        $this->twig->addFunction($this->getPrepareSvgSizeValueFunction());
        $this->twig->addFunction($this->getPrepareResponsiveValueFunction());
        $this->twig->addFunction($this->getPrepareWidthValueFunction());
        $this->twig->addFunction($this->getClassNamesFunction());
        $this->twig->addFunction($this->getVisibilityClassFunction());
        $this->twig->addFunction($this->getVisibilityClassNodeFunction());
        $this->twig->addFunction($this->getRawFunction());
        $this->twig->addFunction($this->getMediaFileFunction());
        $this->twig->addFunction($this->getFileContentFunction());
        $this->twig->addFunction($this->getStartsWithFunction());
        $this->twig->addFunction($this->getJoomlaModuleFunction());
        $this->twig->addFunction($this->getQuixTemplateFunction());
        $this->twig->addFunction($this->getElementApiCallFunction());
        $this->twig->addFunction($this->getGetOpacityFunction());
        $this->twig->addFunction($this->getFieldsGroupFunction());
        $this->twig->addFunction($this->getStartTagFunction());
        $this->twig->addFunction($this->getLoadSvgFunction());
        $this->twig->addFunction($this->getImageUrlFunction());
        $this->twig->addFunction($this->getGetQuixElementPathFunction());
        $this->twig->addFunction($this->getAllFieldFunction());
        $this->twig->addFunction($this->getPrepareContentFunction());
        $this->twig->addFunction($this->getValidateJoomlaCaptchaFunction());
        $this->twig->addFunction($this->getLessThanFunction());
        $this->twig->addFunction($this->getGreaterThanFunction());
        $this->twig->addFunction($this->getGreaterThanSignFunction());
        $this->twig->addFunction($this->getVideoFunction());
        $this->twig->addFunction($this->getCaptchaPublicKeyFunction());
        $this->twig->addFunction($this->getLoadElementAssetFunction());
        $this->twig->addFunction($this->getAddIconStyle());
        $this->twig->addFunction($this->getReplaceAllFunction());
        $this->twig->addFunction($this->getDumpFunction());
        $this->twig->addFunction($this->getBlankFunction('inlineEditor'));

        // register with filter
        $this->twig->addFilter($this->getWrapFilter());
        $this->twig->addFilter($this->getLinkFilter());
        $this->twig->addFilter($this->getJsonDecodeFilter());
        $this->twig->addFilter($this->getRemoveLinesFilter());
    }

    private function getBlankFunction(string $name): TwigFunction
    {
        return new TwigFunction($name, function () {
            return '';
        });
    }

    /**
     * @return \Twig\TwigFunction
     *
     * @since 3.0.0
     */
    private function getDumpFunction(): TwigFunction
    {
        return new TwigFunction('dump', static function () {
            return print_r(func_get_args(), 1);
        });
    }

    /**
     * Get wrap filter.
     *
     * @return TwigFilter
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getWrapFilter(): TwigFilter
    {
        return new TwigFilter('wrap', function ($value, $tag) {
            return new Markup("<$tag> $value </$tag>", 'UTF-8');
        });
    }

    /**
     * @return \Twig\TwigFunction
     *
     * @since 3.0.0
     */
    protected function getReplaceAllFunction(): TwigFunction
    {
        return new TwigFunction('replaceAll', static function ($value, $replace, $with) {
            return str_replace($replace, $with, $value);
        });
    }

    /**
     * Remove lines filter.
     *
     * @return TwigFilter
     *
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getRemoveLinesFilter(): TwigFilter
    {
        return new TwigFilter('removeLines', function ($value) {
            $value = trim(preg_replace('/\s+/', ' ', $value));

            return new Markup($value, 'UTF-8');
        });
    }

    /**
     * Get image source link
     *
     * @param  string  $src
     *
     * @return string
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getSrcLink(string $src): string
    {
        $config    = JComponentHelper::getParams('com_media');
        $imagePath = $config->get('image_path', 'images');

        if (preg_match('/^(https?:\/\/)|(http?:\/\/)|(\/\/)|([a-z\d-].)+(:[\d]+)(\/.*)?$/', $src)) {
            return $src;
        }

        return JURI::root().$imagePath.'/'.$src;
    }

    /**
     * Get json decode.
     *
     * @return TwigFilter
     *
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getJsonDecodeFilter(): TwigFilter
    {
        return new TwigFilter('json_decode', function ($value) {
            return json_decode($value, true);
        });
    }

    /**
     * Get prepareSvgSizeValue
     *
     * @return \Twig\TwigFunction
     *
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getPrepareSvgSizeValueFunction(): TwigFunction
    {
        return new TwigFunction('prepareSvgSizeValue', function ($size) {
            if (is_array($size)) {
                return $size;
            }

            return [
                'value' => $size,
                'unit'  => 'px'
            ];
        });
    }

    /**
     * Get prepareResponsiveValue
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getPrepareResponsiveValueFunction(): TwigFunction
    {
        return new TwigFunction('prepareResponsiveValue', static function ($responsive): array {
            if (isset($responsive['desktop'])) {
                $responsive['unit'] = empty($responsive['unit']) ? 'px' : $responsive['unit'];

                return $responsive;
            }

            if ( ! isset($responsive['value'])) {
                $newResponsive            = [];
                $newResponsive['desktop'] = '';
                $newResponsive['tablet']  = '';
                $newResponsive['phone']   = '';
                $newResponsive['unit']    = 'px';

                return $newResponsive;
            }

            $newResponsive         = $responsive['value'];
            $newResponsive['unit'] = empty($responsive['unit']) ? 'px' : $responsive['unit'];

            return $newResponsive;
        });
    }

    /**
     * Get prepareWidthValue
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getPrepareWidthValueFunction(): TwigFunction
    {
        return new TwigFunction('prepareWidthValue', function ($width) {
            if (isset($width['unit'])) {
                return $width;
            }

            return [
                'unit'  => '%',
                'value' => $width
            ];
        });
    }

    /**
     * Get image function.
     *
     * @return TwigFunction
     * @params $source was added on quix 4. image need implementation
     * @since 3.0.0
     */
    protected function getImageFunction(): TwigFunction
    {
        return new TwigFunction('image', function ($src, $alt = '', $cls = '', $attr = '', $source = [], $optimize = true) {
            if ( ! $src) {
                return '';
            }

            $input = \JFactory::getApplication()->input;

            /**
             * Since the Title text is being used to fill the alt="" tag also, when you use contractions (We're instead of We are, or haven't instead of have not), the code gets messed up causing the image not to load.
             *
             * @see   https://www.themexpert.com/forum/issue-with-slider-pro
             * @see   https://www.useloom.com/share/637cf48c69584e39a29b45e3a9eac49e
             * @since 3.0.0
             */
            if ( ! is_array($source)) {
                $source = (array) $source;
            }

            $alt                  = htmlentities($alt);
            $dimension            = "";
            $sourceDimensionWidth = $source['dimension']['width'] ?? null;
            if ($source && $sourceDimensionWidth) {
                $dimension .= ' width="'.$sourceDimensionWidth.'" data-optimumx="1.5"';
            }

            $sourceDimensionHeight = $source['dimension']['height'] ?? null;
            if ($source && $sourceDimensionHeight) {
                $dimension .= ' height="'.$sourceDimensionHeight.'" data-optimumx="1.5"';
            }
            if ( ! $dimension) {
                $dimension = " data-width='100' data-height='100' data-optimumx='1.5'";
            }

            if ($input->get('format', 'html') === 'amp') {
                return new Markup("<img$dimension  data-src='{$src}' data-qx-img/>", 'UTF-8');
            }

            [$done, $src] = $this->getImageSrc($src);
            if ($done) {
                goto done;
            }

            if ( ! $optimize) {
                goto done;
            }

            if (Optimizer::$enabled && ! $this->inValidImageName($src)) {
                // optimize stuff

                $hqi      = JUri::root().$src;
                $noScript = "<noscript><img$dimension src='{$hqi}' alt='{$alt}' class='{$cls}' {$attr}/></noscript>";

                try {
                    $optimizer = new Optimizer($src);
                } catch (Exception $e) {
                    goto done;
                }

                if ( ! $optimizer->isSupported() || ! $optimizer->shouldOptimize()) {
                    $src = JUri::root().$src;
                    goto done;
                }

                try {
                    $placeholder = $optimizer->generatePlaceholder();
                    $srcset = implode(',', $optimizer->sizes(true));
                } catch (Exception $e) {
                    goto done;
                }

                if ($optimizer->supported('webp')) {
                    $markup      = "<img$dimension data-srcset='{$srcset}' data-sizes='auto' alt='{$alt}' class='{$cls} lazyload blur-up' {$attr} data-lowsrc='{$placeholder}'/>";
                    $webp_srcset = implode(',', $optimizer->sizes(true, true));
                    $markup      = "<picture><source data-srcset='{$webp_srcset}' data-sizes='auto' alt='{$alt}' class='{$cls} lazyload blur-up' {$attr} type='image/webp'/>{$markup}</picture>";
                } else {
                    $markup = "<img$dimension data-srcset='{$srcset}' data-lowsrc='{$placeholder}' data-sizes='auto' alt='{$alt}' class='{$cls} lazyload blur-up' data-sizes='auto' {$attr}/>";
                }

                return new Markup($noScript.$markup, 'UTF-8');
            }

            done:


            // make relative url absolute.
            $src = $this->makeAbsoluteYrl($src);

            return new Markup("<img$dimension data-src='{$src}' alt='{$alt}' class='{$cls} lazyload blur-up' data-sizes='auto' {$attr}/>",
                'UTF-8');
        });
    }

    public function makeAbsoluteYrl($src)
    {
        if (substr($src, 0, strlen('images/')) === 'images/') {
            $src = JUri::root().$src;
        }

        return $src;
    }

    public function inValidImageName(string $src): bool
    {
        return strpos($src, '&') !== false || strpos($src, ' ') !== false;
    }

    /**
     * @return \Twig\TwigFunction
     *
     * @since 3.0.0
     */
    protected function getLazyBackgroundFunction(): TwigFunction
    {
        return new TwigFunction('lazyBackground', function (?array $media, string $src = '') {

            if ($src === '') {
                if ( ! isset($media['state']['normal']['properties']['src']['source'])) {
                    return null;
                }

                $src = $media['state']['normal']['properties']['src']['source'];
                if ( ! $src) {
                    return '';
                }
            }

            [$done, $src] = $this->getImageSrc($src);

            if ($done) {
                goto done;
            }

            try {
                $optimizer = new Optimizer($src);
                $srcset    = implode(', ', $optimizer->sizes(true));

                return "data-bgset='$srcset' data-sizes='auto'";
            } catch (Exception $e) {
                goto done;
            }

            done:

            return "data-bg={$src}";
        });
    }

    /**
     * get If Element has background to show
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getIfElementHasBackgroundFunction(): TwigFunction
    {
        return new TwigFunction('ifElementHasBackground', function ($media) {
            if ( ! isset($media['state']['normal']['properties']['src']['source'])) {
                return false;
            }

            return true;
        });
    }

    /**
     * @param  string  $src
     *
     * @format sample.png, /sample.png, images.png,/images.png and more
     * @return array
     *
     * @since  3.0.0
     */
    private function getImageSrc(string $src): array
    {
        $imagePath = QUIXNXT_IMAGE_PATH;

        if (strpos($src, 'data:', 0) === 0 || strpos($src, '//', 0) === 0 || strpos($src, 'http://', 0) === 0 || strpos($src, 'https://', 0) === 0) {

            /*
             * If getquix url, replace with cdn link
             * @since 3.0.0
             */
            // return strpos($src, 'https://getquix.net', 0);
            if (strpos($src, 'https://getquix.net', 0) !== false || strpos($src, 'http://getquix.net', 0) !== false) {
                $src = str_replace('https://getquix.net', 'https://quix.b-cdn.net', $src);
                $src = str_replace('http://getquix.net', 'https://quix.b-cdn.net', $src);
            }

            return [true, $src];
        }

        if (strpos($src, 'libraries/', 0) !== false || strpos($src, 'media/', 0) !== false) {
            $src = JUri::root().$src;

            return [true, $src];
        }

        // new implementation
        /**
         * new checker for local image
         */
        if (substr($src, 0, 6) === 'images') {
            return [false, $src];
        } else {
            $src = $imagePath.$src;

            return [false, $src];
        }

        /* old checker =========*/
        // if (strpos($src, 'images/', 0) !== false) {
        //     // $src = JUri::root(true).'/'.$src;
        //     // $src = JUri::root(true).'/'.$src;
        //     return [false, $src];
        // }
        //
        // if (strpos($src, $imagePath, 0) !== false) {
        //     return [false, $src];
        // }
        //
        // $src = JUri::root().$imagePath.$src;
        // return [false, $src];
        /* // ===========old checker ends */
    }

    /**
     * @return \Twig\TwigFunction
     *
     * @since 3.0.0
     */
    protected function getIconFunction(): TwigFunction
    {
        return new TwigFunction('icon', static function (string $icon) {
            return new Icon($icon);
        });
    }

    /**
     * getRootUrlFunction.
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getRootUrlFunction(): TwigFunction
    {
        return new TwigFunction('rootUrl', function () {
            return JUri::root();
        });
    }

    protected function checkIfReadyToGoResponsive($images, $src)
    {
        $index = '/'.$src;
        if ( ! isset($images[$index])) {
            return false;
        }

        $hasValue = false;
        $imgSet   = $images['/'.$src];

        foreach ($imgSet as $key => $set) {
            $hasValue = count($set);

            if ($hasValue) {
                break;
            }
        }

        return $hasValue;
    }

    /**
     * @param  string  $src
     *
     * @return bool
     *
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function optimizedImagesExists(string $src): bool
    {
        $srcSplit = explode('.', $src);

        $srcWithoutExtension = implode('.', $srcSplit);

        $hasWebpImage = true;

        if (function_exists('imagewebp')) {
            $hasWebpImage = count(glob(JPATH_ROOT.'/media/quix/cache/images/'.($srcWithoutExtension).'-*.webp'));
            if ( ! $hasWebpImage) {
                $hasWebpImage = file_exists(JPATH_ROOT.'/media/quix/cache/images/'.($srcWithoutExtension).'.webp');
            }
        }

        $hasOriginalImageFormat = count(glob(JPATH_ROOT.'/media/quix/cache/images/'.($srcWithoutExtension).'-*.jpeg')); // 5 means, we only support 5 types of device.
        if ( ! $hasOriginalImageFormat) {
            $hasOriginalImageFormat = file_exists(JPATH_ROOT.'/media/quix/cache/images/'.($srcWithoutExtension).'.jpeg');
        }

        return $hasWebpImage && $hasOriginalImageFormat;
    }

    protected function responsiveImage(string $src): array
    {
        // check start with /
        $baseURL = JUri::base().'media/quix/cache/images/';

        $breakPoints = ['mini', 'mobile', 'tablet', 'desktop', 'large_desktop'];
        global $responsiveImagesMapper;

        $sets = $responsiveImagesMapper['/'.$src];

        $config           = JComponentHelper::getParams('com_quix');
        $responsive_image = (array) $config->get('responsive_image');

        $jpegSrcSet = '';

        $breakPointsInNumber = [];

        $miniImage = $baseURL.ltrim($sets['jpeg'][0], '/');
        foreach ($sets['jpeg'] as $key => $set) {
            $b          = $responsive_image[$breakPoints[$key]];
            $jpegSrcSet .= $baseURL.ltrim($set, '/')." {$b}w, ";

            $breakPointsInNumber[] = $b;
        }

        $sizes = '';
        sort($breakPointsInNumber);
        foreach ($breakPointsInNumber as $point) {
            // max pixel value is used for loading proper image for the proper device
            // you have to use css rules [ width: 100% ] to make your image full width
            // you uploaded image won't full width automatically.
            $sizes .= "(max-width: {$point}px) {$point}px, ";
        }

        if ($sets['webp']) {
            $miniImage = $baseURL.ltrim($sets['webp'][0], '/');
        }
        $webpSrcSet = '';
        foreach ($sets['webp'] as $key => $set) {
            $b = $responsive_image[$breakPoints[$key]];

            $webpSrcSet .= $baseURL.ltrim($set, '/')." {$b}w, ";
        }

        $srcset = $jpegSrcSet;

        global $userWantWebp;

        if (function_exists('imagewebp') && (strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false) && filter_var($userWantWebp, FILTER_VALIDATE_BOOLEAN)) {
            $srcset = $webpSrcSet;
        }

        $src = str_replace(['JPG', 'JPEG'], ['jpeg', 'jpeg'], $src);

        return [$baseURL.$src, $srcset, $sizes, $miniImage];
    }

    /**
     * Get video function.
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getVideoFunction(): TwigFunction
    {
        return new TwigFunction('video', function ($id, $options, $attr = ' controls crossorigin playsinline settings ') {
            $linkType = $options['link_type'] ?? null;
            if ($linkType === 'youtube' || $linkType === 'vimeo') {

                $embedId = $linkType === 'youtube' ? $options['youtube_link'] : $options['vimeo_link'];
                $script  = \QuixAppHelper::getQuixUrl().'/visual-builder/elements/video/assets/plyr.js';
                $style   = \QuixAppHelper::getQuixUrl().'/visual-builder/elements/video/assets/plyr.css';

                return new Markup("<div class='lazyload' id=\"video-{$id}\" data-script=\"{$script}\" data-link=\"{$style}\" data-plyr-provider=\"{$linkType}\" data-plyr-embed-id=\"{$embedId}\"> Loading...</div>",
                    'UTF-8');
            }

            if ($linkType === 'custom' && isset($options['custom_video']['source'])) {
                $poster = $options['video_poster']['source'] ?? null;
                $src    = $options['custom_video']['source'];

                [$done, $source] = $this->getImageSrc($src);
                if ( ! $done) {
                    $source = JUri::root().$source;
                }

                [$done2, $poster] = $this->getImageSrc($poster);
                if ( ! $done2) {
                    $poster = JUri::root().$poster;
                }

                $extension = pathinfo($src, PATHINFO_EXTENSION);
                $type      = "video/{$extension}";

                return new Markup("<video id='video-{$id}' poster='{$poster}'{$attr}>
                <source type='{$type}' src='$source' />
                Your browser does not support the video tag.
            </video>", 'UTF-8');
            }

            return new Markup("<p class=\"qx-alert qx-alert-warning qx-m-0\">Please select video first!</p>", 'UTF-8');
        });
    }

    /**
     * Get video function.
     *
     * @return TwigFunction
     *
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getCaptchaPublicKeyFunction(): TwigFunction
    {
        return new TwigFunction('captchaPublicKey', function () {
            $captcha = \QuixAppHelper::getConfig()->get('captcha');
            if ($captcha !== '0' && $captcha !== '' && $captcha !== null) {
                $plugin = JPluginHelper::getPlugin('captcha', $captcha);
                if ($plugin) {
                    $params = new JRegistry($plugin->params);

                    return $params->get('public_key');
                }
            }

            return '';
        });
    }

    /**
     * Load assets
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getLoadElementAssetFunction(): TwigFunction
    {
        return new TwigFunction('loadelementasset', function ($type, $name, $url, $root = 'QUIXNXT_URL') {
            if ($root === 'QUIXNXT_URL') {
                $urlPrefix = QUIXNXT_URL;
            } else {
                $urlPrefix = QUIXNXT_TEMPLATE_URL;
            }

            switch ($type) {
                case 'css':
                    StyleManager::getInstance()->addUrl($urlPrefix.$url);
                    break;
                case 'js':
                    ScriptManager::getInstance()->addUrl($urlPrefix.$url);
                    break;
            }
        });
    }

    /**
     * Get startTag function.
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getStartTagFunction(): TwigFunction
    {
        return new TwigFunction('startTag', function ($tag, $attr) {
            return new Markup("<$tag $attr>", 'UTF-8');
        });
    }

    /**
     * Get LoadSvg function.
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getLoadSvgFunction(): TwigFunction
    {
        return new TwigFunction('loadSvg', function ($svg) {
            return new Icon($svg);
        });
    }

    /**
     * Get imageUrl
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getImageUrlFunction(): TwigFunction
    {
        return new TwigFunction('imageUrl', function ($src) {
            if ( ! $src) {
                return '';
            }

            [$done, $src] = $this->getImageSrc($src);

            if ($done) {
                return $src;
            } else {
                return preg_replace('/([^:])(\/{2,})/', '$1/', $src);
            }
        });
    }

    /**
     * Get classNames function.
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getClassNamesFunction(): TwigFunction
    {
        return new TwigFunction('classNames', function () {
            return classNames(...func_get_args());
        });
    }

    /**
     * Get classNames function.
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getVisibilityClassFunction(): TwigFunction
    {
        return new TwigFunction('visibilityClass', function ($visibility) {
            $class = [];

            $visibility['xs'] = $visibility['sm'];
            foreach ($visibility as $key => $value) {
                if ( ! $value) {
                    $class[] = 'qx-d-'.$key.'-none';

                    foreach ($visibility as $key2 => $sub_value) {
                        if ($sub_value) {
                            $class[] = 'qx-d-'.$key2.'-block';
                        }
                    }
                }
            }

            // handle the xs value
            if ( ! $visibility['xs']) {
                $class[] = 'qx-d-none';
            } elseif (count($class)) {
                $class[] = 'qx-d-block';
            }

            $class = array_unique($class);

            return implode(' ', $class);
        });
    }

    /**
     * Get classNames function.
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getVisibilityClassNodeFunction(): TwigFunction
    {
        return new TwigFunction('visibilityClassNode', function ($visibility) {
            $class = [];

            $visibility['xs'] = $visibility['sm'];
            foreach ($visibility as $key => $value) {
                if ( ! $value) {
                    $class[] = 'qx-d-'.$key.'-none';

                    foreach ($visibility as $key2 => $subValue) {
                        if ($subValue) {
                            $class[] = 'qx-d-'.$key2.'-flex';
                        }
                    }
                }
            }

            // handle the xs value
            if ( ! $visibility['xs']) {
                $class[] = 'qx-d-none';
            } elseif (count($class)) {
                $class[] = 'qx-d-flex';
            }

            $class = array_unique($class);

            return implode(' ', $class);
        });
    }

    /**
     * Get raw function.
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getRawFunction(): TwigFunction
    {
        return new TwigFunction('raw', function ($source) {
            return file_get_contents(QUIXNXT_PATH.$source);
        });
    }

    /**
     * Get raw function.
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getMediaFileFunction(): TwigFunction
    {
        return new TwigFunction('mediaFile', function ($source) {
            if (file_exists(\QuixAppHelper::getQuixMediaPath().$source)) {
                return file_get_contents(\QuixAppHelper::getQuixMediaPath().$source);
            } else {
                return 'Media file missing...';
            }
        });
    }

    /**
     * Get getFileContent
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getFileContentFunction(): TwigFunction
    {
        return new TwigFunction('getFileContent', function ($element, $path, $ext) {
            return file_get_contents(QUIXNXT_PATH.$path.'.'.$ext);
        });
    }

    /**
     * Get lessThan function.
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getLessThanFunction(): TwigFunction
    {
        return new TwigFunction('lessThan', function ($number1, $number2) {
            return $number1 < $number2;
        });
    }

    /**
     * Get greater than function.
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getGreaterThanFunction(): TwigFunction
    {
        return new TwigFunction('greaterThan', function ($number1, $number2) {
            return $number1 > $number2;
        });
    }

    /**
     * Get greater than function.
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getGreaterThanSignFunction(): TwigFunction
    {
        return new TwigFunction('greaterThanSign', function () {
            return '>';
        });
    }

    /**
     * Get quix element path function.
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getGetQuixElementPathFunction(): TwigFunction
    {
        return new TwigFunction('getQuixElementPath', function ($source) {
            return QUIXNXT_ELEMENTS_PATH;
        });
    }

    /**
     * Get starts with function.
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getStartsWithFunction(): TwigFunction
    {
        return new TwigFunction('qxStringStartsWith', function ($str, $subStr) {
            return strpos($str, $subStr) === 0;
        });
    }

    /**
     * Get link filter.
     *
     * @return TwigFilter
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getLinkFilter(): TwigFilter
    {
        return new TwigFilter('link', function () {
            $args = func_get_args();

            $value   = $args[0] ?? null;
            $options = $args[1] ?? [];
            $classes = $args[2] ?? null;
            $attrs   = $args[3] ?? null;

            $url = empty($options['url']) ? null : $options['url'];
            // -- start
            if ( ! preg_match('/^(https?:\/\/)|(http?:\/\/)|(\/\/)|([a-z\d-].)+(:[\d]+)(\/.*)?$/', $url)
            ) {
                // JRoute only if option=com_component passed
                if (strpos($url, 'option=com_') !== false) {
                    $url = "href='".JRoute::_($url)."'";
                } elseif ($url) {
                    $url = "href='".$url."'";
                }
            } elseif ($url) {
                $url = "href='".$url."'";
            }

            // -- end;

            $class  = null;
            $target = '';
            $rel    = '';
            $attr   = null;

            if (isset($classes)) {
                $class = "class='{$classes}'";
            }

            if (isset($attrs)) {
                $attr = $attrs;
            }

            if (isset($options['target']) && $options['target']) {
                $target = "target='_blank'";
            }

            if (isset($options['nofollow']) && $options['nofollow']) {
                $rel = "rel='nofollow'";
            }

            if ( ! is_null($url)) {
                $value = "<a $class $url $target $rel $attr>$value</a>";
            } else {
                $value = (string) $value;
            }

            return new Markup($value, 'UTF-8');
        });
    }

    /**
     * Get field function.
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getFieldFunction(): TwigFunction
    {
        return new TwigFunction('field', function ($field) {
            return $this->getFieldData($field, []); // FIXME: passed data here
        });
    }

    /**
     * @param $field
     * @param  array  $data
     *
     * @return string|null
     * @since 3.0.0
     */
    protected function getFieldData($field, $data = []): ?string
    {
        return '!Migrate to new standard!';
    }

    /**
     * Get field function.
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getAllFieldFunction(): TwigFunction
    {
        return new TwigFunction('allfield', function () {
            return $this->form;
        });
    }

    /**
     * Get content parse joomla event
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getPrepareContentFunction(): TwigFunction
    {
        return new TwigFunction('prepareContent', function ($text, $prepare = false) {
            return ($prepare ? JHtml::_('content.prepare', $text) : $text);
        });
    }

    /**
     * Validate Joomla Captcha
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getValidateJoomlaCaptchaFunction(): TwigFunction
    {
        return new TwigFunction('validateJoomlaCaptcha', function ($value, $recaptchaId) {
            $joomla_captcha = \QuixAppHelper::getConfig()->get('captcha');
            if ($joomla_captcha !== '0' && $joomla_captcha !== '' && $joomla_captcha !== null && $joomla_captcha === $value) {
                $plugin = JPluginHelper::getPlugin('captcha', $value);
                if ( ! $plugin) {
                    return false;
                }

                // lead the script directly
                \QuixAppHelper::getCurrentDocument()->addScript('https://www.google.com/recaptcha/api.js', [], ['defer' => 'defer']);

                return true;
            }

            return false;
        });
    }

    /**
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getWrapperFunction(): TwigFunction
    {
        return new TwigFunction(
            'wrapper',
            function ($wrapper, $tag, $multipart = false, $end = false) {
                if ($end) {
                    return new Markup("</$tag>", 'UTF-8');
                }

                if ($tag === 'form') {
                    $url = JRoute::_('index.php?option=com_quix');

                    return new Markup(
                        "<$tag method='post' name='quixform' action='".$url."' ".($multipart ? "enctype='multipart/form-data'" : '').'>',
                        'UTF-8'
                    );
                }

                return new Markup("<$wrapper>", 'UTF-8');
            }
        );
    }

    /**
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getFormFooterFunction(): TwigFunction
    {
        $session = \QuixAppHelper::getSession();
        if ($session->get('quix_form_secret')) {
            $key = $session->get('quix_form_secret');
        } else {
            $encCrypt = new Crypt(null, null);
            $key      = $encCrypt->generateKey();
            $session->set('quix_form_secret', $key);
        }
        $enc = new Crypt(null, $key);

        return new TwigFunction('formFooter', function ($element, $config = []) use ($enc) {
            return new Markup('
            <input type="hidden" name="option" value="com_quix" />
            <input type="hidden" name="task" value="ajax" />
            <input type="hidden" name="element" value="'.$element.'" />
            <input type="hidden" name="builder" value="frontend" />
            <input type="hidden" name="jform[info]" value="'.$enc->encrypt(json_encode($config)).'" />', 'UTF-8');
        });
    }

    /**
     * Get fields group function.
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getFieldsGroupFunction(): TwigFunction
    {
        return new TwigFunction('fieldsGroup', function ($fieldsGroup, $index) {
            $data    = $fieldsGroup[$index];
            $results = [];

            foreach ($data as $key => $i) {
                // for supporting Quix version < 2.1.0-beta1
                if (isset($i['name'])) {
                    $results[$i['name']] = $i;
                } // for supporting Quix latest version
                else {
                    $results[$key] = $i;
                }
            }

            return $results;
        });
    }

    /**
     * Get opacity from background overlay.
     *
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getGetOpacityFunction(): TwigFunction
    {
        return new TwigFunction('getOpacity', function ($background, $type) {
            return $background['state'][$type]['opacity']['value'] ?? $background['state'][$type]['opacity'];
        });
    }

    /**
     * Get ajaxQuix
     *
     * @return TwigFunction|null
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getElementApiCallFunction(): TwigFunction
    {
        return new TwigFunction('ElementApiCall', function ($element, $info) {
            $className = str_replace('-', ' ', $element);

            $className = ucwords($className);

            $className = str_replace(' ', '', $className);

            $elementClassName = "Quix{$className}Element";

            // Get the method name
            $method = 'getAjax';

            return call_user_func($elementClassName.'::'.$method, $info);
        });
    }

    /**
     * Get Joomla Module
     *
     * @return TwigFunction|null
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getJoomlaModuleFunction(): ?TwigFunction
    {
        return new TwigFunction('getJoomlaModule', function ($id, $style = 'raw') {
            if (empty($id)) {
                return null;
            }

            return \QuixAppHelper::qxModuleById($id, $style);
        });
    }

    /**
     * Get Quix Template
     *
     * @return TwigFunction|null
     * @since 3.0.0
     */
    protected function getQuixTemplateFunction(): ?TwigFunction
    {
        return new TwigFunction('getQuixTemplate', function ($id) {
            if (empty($id)) {
                return null;
            }

            $collection = \QuixAppHelper::renderQuixTemplate($id);

            return $collection->text;
        });
    }

    /**
     * Get bootstrap grid
     *
     * @param  array  $node
     *
     * @return string
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getGrid(array $node): string
    {
        return implode(' ', array_map(static function ($device, $size) {
            $class = null;
            switch ($device) {
                case 'xs':
                    $class = 'qx-col-';
                    break;
                case 'sm':
                    $class = 'qx-col-sm-';
                    break;
                case 'md':
                    $class = 'qx-col-md-';
                    break;
                case 'lg':
                    $class = 'qx-col-lg-';
            }

            return $class.ceil($size * 12);
        }, array_keys($node['size']), $node['size']));
    }

    /**
     * get path
     *
     * @param  string  $path
     *
     * @return string|null
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getPath(string $path): ?string
    {
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        $splitPath = explode(DIRECTORY_SEPARATOR, $path);

        if (in_array('script.twig', $splitPath, true)) {
            $fileName = $splitPath[count($splitPath) - 1];
        } else {
            $fileName = $splitPath[count($splitPath) - 1];
        }

        if ($fileName === 'view.php') {
            $fileName = 'html.twig';
        }

        array_pop($splitPath);

        $subDir = $splitPath[count($splitPath) - 1];

        array_pop($splitPath);

        $dir = $splitPath[count($splitPath) - 1];

        array_pop($splitPath);

        if ($splitPath[count($splitPath) - 3] === 'libraries' && $splitPath[count($splitPath) - 4] === 'libraries' && $splitPath[count($splitPath) - 5] === 'libraries') {
            $path = str_replace(['nodes', 'frontend', 'elements'], '', implode(DIRECTORY_SEPARATOR, $splitPath));

            if ($fileName === 'style.php') {
                $path .= DIRECTORY_SEPARATOR.'frontend'.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$subDir.DIRECTORY_SEPARATOR.'partials'.DIRECTORY_SEPARATOR.'style.twig';
            } elseif ($fileName === 'script.twig') {
                $path .= DIRECTORY_SEPARATOR.'frontend'.DIRECTORY_SEPARATOR.$splitPath[count($splitPath) - 1].DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.'partials'.DIRECTORY_SEPARATOR.'script.twig';
            } else {
                $path .= DIRECTORY_SEPARATOR.'frontend'.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$subDir.DIRECTORY_SEPARATOR.'partials'.DIRECTORY_SEPARATOR.$fileName;
            }
        } else {
            $path = str_replace(['view.php', 'style.php'], '', $path);

            if ($fileName === 'style.php') {
                $path .= 'partials'.DIRECTORY_SEPARATOR.'style.twig';
            } elseif ($fileName === 'script.twig') {
                $path .= 'partials'.DIRECTORY_SEPARATOR.'script.twig';
            } elseif (strpos($path, DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR) === false) {
                $path .= 'partials'.DIRECTORY_SEPARATOR.$fileName;
            }
        }

        return realpath($path);
    }

    /**
     * @return TwigFunction
     * @since 3.0.0
     * @since 3.0.0
     */
    protected function getAddIconStyle(): TwigFunction
    {
        return new TwigFunction('addIconStyle', function ($selector, $src) {
            $color = $src['properties']['color'];
            $size  = $src['properties']['size'];

            if (is_array($size)) {
                $sizeValue = $size['value'] ?? $size['desktop'];
            } elseif (is_object($size)) {
                $sizeValue = $size->value;
            } else {
                $sizeValue = $size;
            }

            return new Markup("<style type=\"text/css\">$selector i {color: $color;font-size: {$sizeValue}px;}$selector polygon,$selector path {fill: $color;}$selector svg {width: {$sizeValue}px;}</style>",
                'UTF-8');
        });
    }
}
