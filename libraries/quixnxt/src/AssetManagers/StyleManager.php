<?php

namespace QuixNxt\AssetManagers;

use QuixNxt\Engine\Foundation\AssetManager;

class StyleManager extends AssetManager
{
    /**
     * Holds all the CSS
     *
     * @var array
     *
     * @since 3.0.0
     */
    private $assets = [
        'desktop' => [],
        'tablet'  => [],
        'phone'   => [],
        'raw'     => '',
    ];

    /**
     * Add CSS rule for particular device
     *
     * @param  string  $device
     * @param  string  $selector
     * @param  string  $rules
     *
     * @since 3.0.0
     */
    private function _responsiveCssRules(string $device, string $selector, string $rules): void
    {
        if ( ! isset($this->assets[$device][$selector])) {
            $this->assets[$device][$selector] = [];
        }
        $rules = trim($rules);
        if ($rules !== '') {
            $this->assets[$device][$selector][] = $rules;
        }
    }

    /**
     * @param  array|null  $field
     * @param  string|null  $unit
     * @param  string|null  $type
     *
     * @return string
     * @since 3.0.0
     */
    private function _cssForDimensions(?array $field, ?string $unit, ?string $type): string
    {
        if ($field === null) {
            return '';
        }

        $css  = '';
        $unit = ( ! $this->_isEmpty($unit)) ? $unit : 'px';
        if (isset($field['top'])) {
            $css .= $this->_prop($type.'-top', $field['top'].$unit);
        }
        if (isset($field['right'])) {
            $css .= $this->_prop($type.'-right', $field['right'].$unit);
        }
        if (isset($field['bottom'])) {
            $css .= $this->_prop($type.'-bottom', $field['bottom'].$unit);
        }
        if (isset($field['left'])) {
            $css .= $this->_prop($type.'-left', $field['left'].$unit);
        }

        return $css;
    }

    /**
     * @param  string  $selector
     * @param  string  $rules
     *
     * @since 3.0.0
     */
    public function desktop(string $selector, string $rules): void
    {
        $this->_responsiveCssRules('desktop', $selector, $rules);
    }

    /**
     * @param  string  $selector
     * @param  string  $rules
     *
     * @since 3.0.0
     */
    public function tablet(string $selector, string $rules): void
    {
        $this->_responsiveCssRules('tablet', $selector, $rules);
    }

    /**
     * @param  string  $selector
     * @param  string  $rules
     *
     * @since 3.0.0
     */
    public function phone(string $selector, string $rules): void
    {
        $this->_responsiveCssRules('phone', $selector, $rules);
    }

    /**
     * @param  string  $selector
     * @param  string  $property
     * @param  null  $value
     *
     * @since 3.0.0
     */
    public function css(string $selector, string $property, $value = null): void
    {
        if (is_array($value)) {
            if ( ! isset($value['value']) || $value['value'] === '') {
                return;
            }

            $this->desktop($selector, $this->_prop($property, $value['value']));
        } else {
            if ($value === null || $value === '') {
                return;
            }

            $this->desktop($selector, $this->_prop($property, $value));
        }
    }

    /**
     * Adds raw stylesheet
     *
     * @param  string  $rules
     *
     * @since 3.0.0
     */
    public function raw(string $rules): void
    {
        $this->assets['raw'] .= $rules;
    }

    /**
     * @param  string  $selector
     * @param  array|null  $field
     *
     * @param  string|null  $typeOverride
     *
     * @since 3.0.0
     */
    public function margin(string $selector, ?array $field, ?string $typeOverride = 'margin'): void
    {
        if ( ! $field) {
            return;
        }

        if ($field['responsive']) {
            $this->desktop($selector, $this->_cssForDimensions($field['desktop'], $field['unit'], $typeOverride));
            $this->tablet($selector, $this->_cssForDimensions($field['tablet'], $field['unit'], $typeOverride));
            $this->phone($selector, $this->_cssForDimensions($field['phone'], $field['unit'], $typeOverride));
        } else {
            $this->desktop($selector, $this->_cssForDimensions($field, $field['unit'], $typeOverride));
        }
    }

    /**
     * @param  string  $selector
     * @param  array|null  $field
     *
     * @since 3.0.0
     */
    public function padding(string $selector, ?array $field): void
    {
        $this->margin($selector, $field, 'padding');
    }

    /**
     * @param  string  $selector
     * @param  array|null  $field
     * @param  string|null  $property
     *
     * @param  string|null  $default_unit
     *
     * @since 3.0.0
     */
    private function _dimension(string $selector, ?array $field, ?string $property, ?string $default_unit = 'px'): void
    {
        if ( ! $field) {
            return;
        }
        $unit  = is_array($field) ? $field['unit'] ?? $default_unit : $field;
        $field = is_array($field) ? $field['value'] ?? $field : $field;

        $this->responsiveCss($selector, $field, $property, $unit);
    }

    /**
     * @param  string  $selector
     * @param  array|null  $field
     *
     * @since 3.0.0
     */
    public function width(string $selector, ?array $field): void
    {
        $this->_dimension($selector, $field, 'width', '%');
    }

    /**
     * @param  string  $selector
     * @param  array|null  $field
     *
     * @since 3.0.0
     */
    public function height(string $selector, ?array $field): void
    {
        $this->_dimension($selector, $field, 'height');
    }

    /**
     * @param  string  $selector
     * @param  array|null  $field
     *
     * @since 3.0.0
     */
    public function minHeight(string $selector, ?array $field): void
    {
        $this->_dimension($selector, $field, 'min-height');
    }

    /**
     * @param  string  $selector
     * @param  array|null  $field
     *
     * @since 3.0.0
     */
    public function typography(string $selector, ?array $field): void
    {
        if ( ! $field) {
            return;
        }

        // Family
        if ( ! $this->_isEmpty($field, 'family')) {

            // fallback font-weight
            $field['weight'] = ! $this->_isEmpty($field, 'weight') ? $field['weight'] : 400;
            $this->css($selector, 'font-family', $field['family']);

            ScriptManager::getInstance()->loadWebfont("{$field['family']}:{$field['weight']}");
        }

        // weight
        if ( ! $this->_isEmpty($field, 'weight')) {
            $this->fontWeight($selector, $field['weight']);
        }
        // Size
        if ( ! $this->_isEmpty($field, 'size')) {
            $this->fontSize($selector, $field['size']);
        }
        // Transform
        if ( ! $this->_isEmpty($field, 'transform')) {
            $this->css($selector, 'text-transform', $field['transform']);
        }
        // Style
        if ( ! $this->_isEmpty($field, 'style')) {
            $this->css($selector, 'font-style', $field['style']);
        }
        // Decoration
        if ( ! $this->_isEmpty($field, 'decoration')) {
            $this->css($selector, 'text-decoration', $field['decoration']);
        }
        // Line Height
        if ( ! $this->_isEmpty($field, 'height')) {
            $this->lineHeight($selector, $field['height']);
        }
        // Letter spacing
        if ( ! $this->_isEmpty($field, 'spacing')) {
            $this->letterSpacing($selector, $field['spacing']);
        }

        if (isset($field['text_shadow']) && ! $this->_isEmpty($field['text_shadow'], 'color')) {
            $this->css($selector, 'text-shadow', $field['text_shadow']['color']
                                                 .' '
                                                 .$field['text_shadow']['horizontal']
                                                 .'px '
                                                 .$field['text_shadow']['vertical']
                                                 .'px '
                                                 .$field['text_shadow']['blur']
                                                 .'px');
        }
    }

    /**
     * @param  string  $selector
     * @param           $weight
     *
     * @since 3.0.0
     */
    public function fontWeight(string $selector, $weight): void
    {
        if ($this->_isEmpty($weight)) {
            return;
        }

        $font_style = false;

        if ($weight === 'regular') {
            $weight = 400;
        } elseif (substr($weight, -6) === 'italic') {
            $font_style = true;
            $weight     = +substr($weight, 0, 3);
        } else {
            $weight = +$weight;
        }

        $this->css($selector, 'font-weight', $weight);

        if ($font_style) {
            $this->css($selector, 'font-style', 'italic');
        }
    }

    /**
     * @param  string  $selector
     * @param  array  $field
     *
     * @since 3.0.0
     */
    public function fontSize(string $selector, array $field): void
    {
        $this->_dimension($selector, $field, 'font-size');
    }

    /**
     * @param  string  $selector
     * @param  array  $field
     *
     * @since 3.0.0
     */
    public function lineHeight(string $selector, array $field): void
    {
        $this->_dimension($selector, $field, 'line-height', 'em');
    }

    /**
     * @param  string  $selector
     * @param  array  $field
     *
     * @since 3.0.0
     */
    public function letterSpacing(string $selector, array $field): void
    {
        $this->_dimension($selector, $field, 'letter-spacing');
    }

    /**
     * @param  string  $selector
     * @param  mixed  $field
     *
     * @since 3.0.0
     */
    public function alignment(string $selector, $field): void
    {
        if ($field === null) {
            return;
        }

        if ($this->_isEmpty($field)) {
            return;
        }

        $this->responsiveCss($selector, $field, 'text-align', '');

    }

    /**
     * @param  string  $selector
     * @param  array  $field
     *
     * @since 3.0.0
     */
    public function float(string $selector, array $field): void
    {
        if ($field === null) {
            return;
        }

        $this->responsiveCss($selector, $field, 'float', '');
    }

    /**
     * @param  string  $selector
     * @param  array|null  $field
     * @param  string|null  $hover_selector
     *
     * @since 3.0.0
     */
    public function background(string $selector, ?array $field, ?string $hover_selector = null): void
    {
        if ($field === null) {
            return;
        }

        // Get state
        $state = $field['state'] ?? null;

        if ( ! $state) {
            return;
        }

        // State : Normal
        // ---------------------------------------
        $normal = $state['normal'];
        $this->_applyBackground($selector, $normal);

        // State : Hover
        // -----------------------------------
        $hover = $state['hover'];
        // Only used on transition effect because selector var is replaced with :hover state
        $notHoverSelector = $selector;

        // Sometime we need to pass hover selector
        // Like background overlay
        if ($hover_selector) {
            $selector = $hover_selector.':hover '.$selector;
        } else {
            $selector .= ':hover';
        }

        $this->_applyBackground($selector, $hover, $notHoverSelector);
    }

    /**
     * @param  string  $selector
     * @param  array  $state
     * @param  string|null  $not_hover_selector
     *
     * @since 3.0.0
     */
    private function _applyBackground(string $selector, array $state, ?string $not_hover_selector = null): void
    {
        // Type = Gradient
        if ($state['type'] === 'gradient') {
            $this->_backgroundGradient($selector, $state);
        }
        // Type : Image.
        if ($state['type'] === 'classic') {
            $this->_backgroundClassic($selector, $state, $not_hover_selector);
        }
    }

    /**
     * @param  string  $selector
     * @param  array  $state
     * @param  string|null  $not_hover_selector
     *
     * @since 3.0.0
     */
    private function _backgroundGradient(string $selector, array $state, ?string $not_hover_selector = null): void
    {
        $shouldApplyOpacity = false;
        $color_1            = $state['properties']['color_1'];
        $color_2            = $state['properties']['color_2'];

        // Gradient Type
        $gradient_type = $state['properties']['type'];

        $directionValue = $state['properties']['direction'];// not an array
        // $direction = $state['properties']['direction'];// not an array
        if (is_array($directionValue)) {
            $direction = $directionValue['value'].($directionValue['unit'] ?? 'deg');
        } elseif ($gradient_type === 'linear') {
            $direction = $directionValue.'deg';
        } else {
            $direction = $directionValue;
        }

        // Suffix position with %
        $start_position = $state['properties']['start_position'];
        $start_position .= '%';
        $end_position   = $state['properties']['end_position'];
        $end_position   .= '%';

        $css = "{$color_1} {$start_position}, {$color_2} {$end_position}";

        if ($gradient_type === 'linear') {
            // $direction .= $directionValue['unit'] ?? 'deg'; /* Because linear direction does not have any units*/
            $css = "{$direction}, {$css}";

            $this->css($selector, 'background', "linear-gradient({$css})");
            $shouldApplyOpacity = true;
        }

        if ($gradient_type === 'radial') {
            $direction = "at {$direction}";
            $css       = "{$direction}, {$css}";

            $this->css($selector, 'background', "radial-gradient({$css})");
            $shouldApplyOpacity = true;
        }
        // Transition
        if ($not_hover_selector !== null && isset($state['required_transition']) && $state['required_transition'] === true) {
            $transitionValue = $state['transition']['value'] ?: $state['transition'];
            $unit            = 's';
            if (is_array($transitionValue)) {
                $unit            = $transitionValue['unit'] ?? $unit;
                $transitionValue = $transitionValue['value'];
            }
            $transition = "background {$transitionValue}{$unit}, opacity {$transitionValue}{$unit} ease-in";
            $transition = "{$transition}, border {$transitionValue}{$unit} ease-in, box-shadow {$transitionValue}{$unit} ease-in";
            $this->css($not_hover_selector, 'transition', $transition);
        }

        // Opacity
        if ($state['required_opacity'] && $shouldApplyOpacity) {
            $this->css($selector, 'opacity', $state['opacity']);
        }
    }

    /**
     * @param  string  $selector
     * @param  array  $state
     * @param  string|null  $not_hover_selector
     *
     * @since 3.0.0
     */
    private function _backgroundClassic(string $selector, array $state, ?string $not_hover_selector = null): void
    {
        $shouldApplyOpacity = false;

        if ( ! $this->_isEmpty($state['properties'], 'color')) {
            $this->css($selector, 'background-color', $state['properties']['color']);
            $shouldApplyOpacity = true;
        }
        if ( ! $this->_isEmpty($state['properties']['src'])) {

            $src = $state['properties']['src'];

            if (isset($src['source']) && $src['source'] && $src['type'] !== 'svg') {

                /**
                 * optimization for BG image was turnned off due to fade to clear loading time.
                 * it looks awful
                 */
                // $root = QUIXNXT_IMAGE_PATH;
                [$done, $src] = $this->getImageSrc($src['source']);

                // now force loading bg directly
                $this->css($selector, 'background-image', "url(\"{$src}\")");

                // if ( ! $done) {
                //     try {
                //         $optimizer = new \QuixNxt\Utils\Image\Optimizer($src);
                //         $lqi       = $optimizer->lqi(1, true);
                //
                //         $this->css("{$selector}", 'background-image', "url(\"{$lqi}\")");
                //     } catch (\Exception $e) {
                //         $this->css($selector, 'background-image', "url(\"{$src}\")");
                //     }
                // } else {
                //     $this->css($selector, 'background-image', "url(\"{$src}\")");
                // }

                $this->css($selector, 'background-size', $state['properties']['size']);
                $this->css($selector, 'background-position', $state['properties']['position']);
                $this->css($selector, 'background-repeat', $state['properties']['repeat']);

                if ($state['properties']['parallax'] && $state['properties']['parallax_method'] === 'css') {
                    $this->css($selector, 'background-attachment', 'fixed');
                }

                if ($state['properties']['blend'] !== 'normal') {
                    $this->css($selector, 'background-blend-mode', $state['properties']['blend']);
                }

                $shouldApplyOpacity = true;
            }
        }

        // Transition
        if ($not_hover_selector !== null && isset($state['required_transition']) && $state['required_transition'] === true && ( ! $this->_isEmpty($state['properties']['color']) || ! $this->_isEmpty($state['properties']['src']))) {
            $transitionValue = $state['transition'];
            $unit            = 's';
            if (is_array($transitionValue)) {
                $unit            = $transitionValue['unit'] ?? $unit;
                $transitionValue = $transitionValue['value'];
            }
            $transition = "background {$transitionValue}{$unit}, opacity {$transitionValue}{$unit} ease-in";
            $transition = "{$transition}, border {$transitionValue}{$unit} ease-in, box-shadow {$transitionValue}{$unit} ease-in";
            $this->css($not_hover_selector, 'transition', $transition);
        }

        // Opacity
        if ($state['required_opacity'] && $shouldApplyOpacity) {
            $this->css($selector, 'opacity', $state['opacity']);
        }
    }

    /**
     *
     * @param  string  $src
     *
     * @return array
     * @since 3.0.0
     */
    public function getImageSrc(string $src)
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

        if (strpos($src, '/libraries/', 0) !== false) {
            $src = JUri::root().'/'.$src;

            return [true, $src];
        }

        if (strpos($src, $imagePath, 0) !== false) {
            return [false, $src];
        }

        return [false, $imagePath.$src];
    }

    /**
     * border width checking if it has or not
     * return false if no value found even 0
     *
     * @param  array|null  $width
     *
     * @return bool
     * @since 4.0.0
     */
    public function borderWidthChecker(?array $width): bool
    {
        if ( ! $width) {
            return false;
        }

        if ($width['top'] !== '' || $width['bottom'] !== '' || $width['left'] !== '' || $width['right'] !== '') {
            return true;
        }

        return false;
    }

    /**
     * @param  string  $selector
     * @param  array|null  $field
     *
     * @since 3.0.0
     */
    public function border(string $selector, ?array $field): void
    {
        if ( ! $field) {
            return;
        }

        // Get state
        $state = $field['state'] ?? null;

        if ( ! $state) {
            return;
        }

        // State : Normal
        // ---------------------------------------
        $normal = $state['normal'];
        $this->_border($selector, $normal);


        // State : Hover
        // ---------------------------------------
        $hover    = $state['hover'];
        $selector = $selector.':hover';
        $this->_border($selector, $hover, true);
    }

    /**
     * @param  string  $selector
     * @param  array  $normal
     *
     * @param  bool  $isHover
     *
     * @since 3.0.0
     */
    private function _border(string $selector, array $normal, bool $isHover = false): void
    {
        // Border
        // if ($normal['properties']['border_type'] !== 'none' && $this->borderWidthChecker($normal['properties']['border_width'])) {
        // style
        if ($normal['properties']['border_type'] !== 'none') {
            $this->css($selector, 'border-style', $normal['properties']['border_type']);

            // width
            $this->desktop($selector, $this->_cssForBorderWidth($normal['properties']['border_width'], $normal['properties']['border_width']['unit']));

            // color
            $this->css($selector, 'border-color', $normal['properties']['border_color'] ?? null);
        }



        // radius
        $border_radius = $normal['properties']['border_radius'] ?? [];
        $this->desktop($selector, $this->_cssForBorderRadius($border_radius, $border_radius['unit'] ?? ''));


        if ($isHover) {
            // Transition
            $transitionValue = $normal['properties']['transition'];
            $unit            = 's';
            if (is_array($transitionValue)) {
                $unit            = $transitionValue['unit'] ?? $unit;
                $transitionValue = $transitionValue['value'];
            }

            $transition = "border {$transitionValue}{$unit} ease-in, box-shadow {$transitionValue}{$unit} ease-in";
            $transition = "{$transition}, background {$transitionValue}{$unit}, opacity {$transitionValue}{$unit} ease-in";
            $this->css($selector, 'transition', $transition);
        }
        // }

        // Box Shadow
        // ---------------------------------------
        $shadow = $normal['properties']['box_shadow'];

        if ($shadow['color']) {
            $position   = $shadow['position'] === 'inset' ? 'inset ' : '';
            $horizontal = $shadow['horizontal']['value'] ?? $shadow['horizontal'];
            $vertical   = $shadow['vertical']['value'] ?? $shadow['vertical'];
            $blur       = $shadow['blur']['value'] ?? $shadow['blur'];
            $spread     = $shadow['spread']['value'] ?? $shadow['spread'];

            $boxShadow = $position.
                         " {$horizontal}px".
                         " {$vertical}px".
                         " {$blur}px".
                         " {$spread}px".
                         " {$shadow['color']}";
            $this->css($selector, 'box-shadow', $boxShadow);
        }
    }

    /**
     * @param  string  $selector
     * @param  array|null  $field
     *
     * @since 3.0.0
     */
    public function borderWidth(string $selector, ?array $field): void
    {
        if ( ! $field) {
            return;
        }

        if (isset($field['responsive'])) {
            $this->desktop($selector, $this->_cssForBorderWidth($field['desktop']));
            $this->tablet($selector, $this->_cssForBorderWidth($field['tablet']));
            $this->phone($selector, $this->_cssForBorderWidth($field['phone']));
        } else {
            $this->desktop($selector, $this->_cssForBorderWidth($field));
        }
    }

    /**
     * @param  $field
     * @param  string  $unit
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function _cssForBorderWidth($field, ?string $unit = 'px'): string
    {
        $css = '';

        if ( ! $field || ! is_array($field)) {
            return $css;
        }

        // if ($this->_isEmpty($field, 'top') && $this->_isEmpty($field, 'left') && $this->_isEmpty($field, 'bottom') && $this->_isEmpty($field, 'right')) {
        //     $css .= $this->_prop('border-width', '0'.$unit);
        //
        //     return $css;
        // }


        if ( ! is_array($field['top'])) {
            $css .= ! $this->_isEmpty($field, 'top') ? $this->_prop('border-top-width', $field['top'].$unit) : '';
        }
        $css .= ! $this->_isEmpty($field, 'right') ? $this->_prop('border-right-width', $field['right'].$unit) : '';
        $css .= ! $this->_isEmpty($field, 'bottom') ? $this->_prop('border-bottom-width', $field['bottom'].$unit) : '';
        $css .= ! $this->_isEmpty($field, 'left') ? $this->_prop('border-left-width', $field['left'].$unit) : '';

        return $css;
    }

    /**
     * @param  string  $selector
     * @param  $field
     *
     * @since 3.0.0
     */
    public function borderRadius(string $selector, $field): void
    {
        if ( ! $field || ! is_array($field)) {
            return;
        }

        if (isset($field['responsive'])) {
            $this->desktop($selector, $this->_cssForBorderRadius($field['desktop']));
            $this->tablet($selector, $this->_cssForBorderRadius($field['tablet']));
            $this->phone($selector, $this->_cssForBorderRadius($field['phone']));
        } else {
            $this->desktop($selector, $this->_cssForBorderRadius($field));
        }
    }

    /**
     * @param  array|null  $field
     * @param  string  $unit
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function _cssForBorderRadius($field, ?string $unit = 'px'): string
    {
        if ( ! $field | ! is_array($field)) {
            return '';
        }

        $css = '';

        $css .= ! $this->_isEmpty($field, 'top') ? $this->_prop('border-top-left-radius', $field['top'].$unit) : '';
        $css .= ! $this->_isEmpty($field, 'right') ? $this->_prop('border-top-right-radius', $field['right'].$unit) : '';
        $css .= ! $this->_isEmpty($field, 'bottom') ? $this->_prop('border-bottom-right-radius', $field['bottom'].$unit) : '';
        $css .= ! $this->_isEmpty($field, 'left') ? $this->_prop('border-bottom-left-radius', $field['left'].$unit) : '';

        return $css;
    }

    /**
     * @param  string|null  $property
     * @param  string|null  $value
     *
     * @return string
     * @since 3.0.0
     */
    public function _prop(?string $property, ?string $value): string
    {
        $zeroAllowedProp = [
            'padding-bottom',
            'padding-top',
            'padding-right',
            'padding-left',
            'margin-bottom',
            'margin-top',
            'margin-right',
            'margin-left',
            'border-width',
            'border-top-width',
            'border-bottom-width',
            'border-right-width',
            'border-left-width',
        ];

        if (preg_match("/^\d+$/", $value)) {
            $value = intval($value, 10);
        }

        if (
            ! $value ||
            (is_numeric($value) && ($value >= 0)) ||
            ('0%' === $value) ||
            ('0px' === $value) ||
            ('0em' === $value) ||
            ('0rem' === $value) ||
            ('0vh' === $value) ||
            ('0vw' === $value) ||
            ($this->_isEmpty($value))
        ) {
            if ((is_numeric($value) && $value !== 0) || in_array($property, $zeroAllowedProp, true)) {
                return "{$property}: {$value};";
            }

            return '';
        }

        return $value === 'px' || $value === 'rem' || $value === 'em' || $value === 'vh' || $value === '%'
            ? ''
            : "{$property}: {$value};";
    }

    /**
     * @param  string  $selector
     * @param  mixed|null  $field
     * @param  string|null  $property
     * @param  string|null  $unit
     *
     * @since 3.0.0
     */
    public function responsiveCss(string $selector, $field, ?string $property, ?string $unit): void
    {
        if ($field === null) {
            return;
        }

        $unit = $unit ?? '';
        if ( ! is_array($field)) {
            $this->desktop($selector, $this->_prop($property, $field.$unit));

            return;
        }

        /**
         * we dont need this check, if value is responsive then apply it.
         * if (isset($field['responsive']) || isset($field['responsive_preview'])) {}
         *
         * @since 4.0.0 on 1st april
         */
        if (is_numeric($field['desktop']) || ! $this->_isEmpty($field['desktop'])) {
            $this->desktop($selector, $this->_prop($property, $field['desktop'].$unit));
        }

        if (is_numeric($field['tablet']) || ! $this->_isEmpty($field['tablet'])) {
            $this->tablet($selector, $this->_prop($property, $field['tablet'].$unit));
        }
        if (is_numeric($field['phone']) || ! $this->_isEmpty($field['phone'])) {
            if (is_array($field['phone'])) {
                $field['phone'] = $field['phone']['phone'] ?? $field['phone']['mobile'];
                if ($field['phone']) {
                    $this->phone($selector, $this->_prop($property, $field['phone'].$unit));
                }
            } else {
                $this->phone($selector, $this->_prop($property, $field['phone'].$unit));
            }
        }
    }

    /**
     * @param                $var
     *
     * @param  string|null  $field
     *
     * @return bool
     *
     * @since 3.0.0
     */
    private function _isEmpty($var, string $field = null): bool
    {
        if ($field && is_array($var)) {
            $var = $var[$field] ?? null;
        }

        if (is_array($var)) {
            return count($var) === 0;
        }

        if (is_string($var)) {
            return $var === '';
        }

        return false; // as 0 is a value, we can't return !0 as it's not empty.
        return ! (bool) $var;
    }

    public function _debug(): void
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/../debug.json', json_encode(func_get_args(), 128), FILE_APPEND);
    }

    /**
     * @param  string  $id
     *
     * @return string
     *
     * @since 3.0.0
     */
    public function load(string $id): string
    {
        return '';
    }

    /**
     * @return string
     *
     * @since 3.0.0
     */
    public function compile(): string
    {
        $tablet_media_query = '@media (min-width: 768px) and (max-width: 1024px)';
        $phone_media_query  = '@media (max-width: 767px)';

        $css = [];
        $raw = $this->assets['raw'].parent::compile();
        unset($this->assets['raw']);

        foreach ($this->assets as $device => $assets) {
            $css[$device] = '';
            foreach ($assets as $selector => $value) {
                $combined_css = implode(array_unique($value));
                if ($combined_css !== '') {
                    $css[$device] .= "{$selector} {{$combined_css}}";
                }
            }
        }

        $this->assets = [
            'desktop' => [],
            'tablet'  => [],
            'phone'   => [],
            'raw'     => '',
        ];

        $subject = "{$css['desktop']}{$raw} {$tablet_media_query}{{$css['tablet']}}{$phone_media_query}{{$css['phone']}}";

        return $this->compress($subject);
    }

    /**
     * @param  string  $subject
     *
     * @return string
     * @since 3.0.0
     */
    public function compress(string $subject): string
    {
        return preg_replace('/\s+/', ' ', $subject);
    }

    /**
     * @return string
     *
     * @since 3.0.0
     */
    public function __toString(): string
    {
        return $this->compile();
    }
}
