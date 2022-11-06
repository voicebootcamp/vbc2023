<?php

namespace QuixNxt\Adapters;

use RuntimeException;
use QuixNxt\Utils\Schema;

class Quix2To3 extends Adapter
{
    /**
     * @param  array  $nodes
     *
     * @return array
     *
     * @throws \Exception
     * @since 3.0.0
     */
    public function transform(array $nodes): array
    {
        return array_map(function (array $node) {
            $children         = $this->transform($node['children'] ?? []);
            $node             = $this->mapOldQuixNodeData($this->transformNode($this->pullIndexUp($node)));
            $node['children'] = $children;

            return $node;
        }, $nodes);
    }

    /**
     * @param $data
     *
     * @return array|array[]|mixed
     *
     * @since 3.0.0
     */
    private function pullIndexUp($data)
    {
        if (is_array($data)) {
            if ( ! Schema::_isAssoc($data)) {
                if (isset($data[0]) && is_array($data[0]) && count($data[0]) > 0 && ! Schema::_isAssoc($data[0])) {
                    // group repeater
                    return array_map(static function (array $value) {
                        $values = [];

                        foreach ($value as $key => $item) {
                            foreach ($item as $sKey => $kValue) {
                                $values[$sKey] = $kValue;
                            }
                        }

                        return $values;
                    }, $data);
                }

                $values = [];

                foreach ($data as $item) {
                    if ( ! is_iterable($item)) {
                        $values = $data;
                        break;
                    }
                    foreach ($item as $sKey => $value) {
                        $values[$sKey] = $value;
                    }
                }

                $data = $values;
            }

            foreach ($data as $sKey => $value) {
                if ($sKey === 'children') {
                    continue;
                }

                if ($sKey === 'form_action_after_submit') {
                    $data[$sKey] = $value;
                } else {
                    if (isset($value[0][0]) && is_array($value[0][0]) && count($value[0][0]) === 0) {
                        unset($value[0]);
                        $value = array_values($value);
                    }
                    $data[$sKey] = $this->pullIndexUp($value);
                }
            }

            return $data;
        }

        return $data;
    }

    /**
     * @param  array  $node
     *
     * @return array
     * @throws \Exception
     *
     * @since 3.0.0
     */
    private function transformNode(array $node): array
    {
        if ( ! isset($node['form']['styles']) && isset($node['form']['style'])) {
            $node['form']['styles'] = $node['form']['style'];
        }

        return [
            'slug'       => $node['slug'],
            'visibility' => $node['visibility'],
            'id'         => $node['id'] ?? "qx-".$node['slug'].'-'.random_int(100000, 999999),
            'form'       => [
                'general'  => $this->transformProperties($node['form']['general']),
                'styles'   => isset($node['form']['styles']) ? $this->transformProperties($node['form']['styles']) : null,
                'advanced' => $this->transformProperties($node['form']['advanced']),
            ],
        ];
    }

    /**
     * @param  array  $props
     *
     * @return array
     *
     * @since 3.0.0
     */
    private function transformProperties(array $props): array
    {
        $_props = [];
        foreach ($props as $key => $value) {
            if (is_array($value)) {
                if ($value && array_key_exists('media', $value)) {
                    // media property
                    $v = [];
                    if ($value['media'] && is_array($value['media'])) {
                        $v['source'] = $value['media'][$value['media']['type']];
                        $v['type']   = $value['media']['type'];
                    } else {
                        $v['source'] = $value['src'] ?? $value['url'];
                        $arr         = explode('.', $v['source']);
                        $ext         = array_pop($arr);
                        switch ($ext) {
                            case 'svg':
                                $type = 'svg';
                                break;
                            case 'jpg':
                            case 'jpeg':
                            case 'png':
                            case 'webp':
                            case 'gif':
                            case 'bmp':
                            case 'ico':
                                $type = 'image';
                                break;
                            case 'mp4':
                            case 'mov':
                            case 'flv':
                            case 'mkv':
                            case 'movie':
                            case 'mpeg':
                            case 'mpg':
                                $type = 'video';
                                break;
                            default:
                                $type = 'unknown';
                        }
                        $v['type'] = $type;
                    }
                    $v['properties'] = $value['media']['properties'] ?? [];

                    $_props[$key] = $v;
                } elseif (count($value) === 0) {
                    $_props[$key] = $value;
                } elseif ( ! Schema::_isAssoc($value) && is_array($value[0])) {
                    // group-repeater values
                    $_props[$key] = array_map(function (array $_props) {
                        return $this->transformProperties($_props);
                    }, $value);
                } elseif ($key === 'zindex') {
                    $_props[$key] = $value['value'];
                } elseif ($key === 'layout_alignment') {
                    $_props[$key] = [
                        'desktop' => $value['value'],
                        'tablet'  => $value['value'],
                        'phone'   => $value['value'],
                    ];
                } elseif ($key === 'opacity') {
                    $_props[$key] = $value['value'] ?? $value;
                } elseif ($key === 'col_width' && array_key_exists('value', $value)) {
                    $value = $value['value'];
                    $unit  = $value['unit'] ?? '%';

                    $phone   = $value['phone'];
                    $tablet  = $value['tablet'];
                    $desktop = $value['desktop'];

                    $_props[$key] = [
                        'phone'   => $phone,
                        'desktop' => $desktop,
                        'tablet'  => $tablet,
                        'unit'    => $unit,
                    ];
                } else {
                    $_props[$key] = $this->pullValue($value);
                }
            } else {
                $_props[$key] = $value;
            }
        }

        return $_props;
    }

    /**
     * @param $value
     *
     * @return array|mixed
     *
     * @since 3.0.0
     */
    private function pullValue($value)
    {

        if ( ! $value || ! is_array($value)) {
            return $value;
        }

        // {label, icon, value}
        if (isset($value['label'], $value['value'])) {
            return $value['value'];
        }

        // tablet, phone
        if (isset($value['phone'], $value['tablet'])) {
            if ( ! array_key_exists('desktop', $value)) {
                // margin, padding
                if (array_key_exists('left', $value)) {
                    $phone  = $value['phone'];
                    $tablet = $value['tablet'];
                    $top    = $value['top'];
                    $right  = $value['right'];
                    $bottom = $value['bottom'];
                    $left   = $value['left'];
                    $unit   = $value['unit'] ?? '';

                    return [
                        'phone'   => $phone,
                        'tablet'  => $tablet,
                        'desktop' => [
                            'left'   => $left,
                            'right'  => $right,
                            'top'    => $top,
                            'bottom' => $bottom,
                        ],
                        'unit'    => $unit,
                    ];
                } else {
                    $value['desktop'] = '';

                    return $value;
                }

                // throw new RuntimeException('Desktop empty but no left value. '.json_encode($value));
            }

            foreach ($value as $key => $item) {
                $value[$key] = $this->pullValue($value[$key]);
            }

            return $value;
        }

        return $this->transformProperties($value);
    }

    /**
     * @param  array  $node
     *
     * @return array
     *
     * @since 3.0.0
     */
    private function mapOldQuixNodeData(array $node): array
    {
        $changeMap = [
            'form'                 => [
                'general.form_submit.submit_column' => 'styles.form_button.submit_column',
                'styles.form_body.column_gap.value' => 'styles.form_body.column_gap',
            ],
            'dual-button'          => [
                'styles.button_common_fields_group.choose_layout'               => 'general.layout_button_fields_group.choose_button_layout',
                'styles.button_common_fields_group.layout_alignment'            => 'general.layout_button_fields_group.button_layout_alignment',
                'styles.button_primary_fields_group.primary_icon_alignment'     => 'general.primary_button_fields_group.primary_button_icon_alignment',
                'styles.button_secondary_fields_group.secondary_icon_alignment' => 'general.secondary_button_fields_group.secondary_button_icon_alignment',
                // 'styles.button_primary_fields_group.icon_primary_size'          => 'general.primary_button_fields_group.primary_icon.properties.size',
                'styles.button_primary_fields_group.icon_primary_size'          => [
                    'path' => 'general.primary_button_fields_group.primary_icon.properties.size',
                    'map'  => static function ($data, $newData) {
                        if (is_array($data) && isset($data['value'])) {
                            return $data;
                        }

                        if (is_array($newData)) {
                            return ['unit' => $newData['unit'], 'value' => $newData['desktop'] ?? '30'];
                        }

                        return  ['unit' => 'px', 'value' => '30'];
                    }
                ],
                'styles.button_primary_fields_group.icon_primary_color'         => 'general.primary_button_fields_group.primary_icon.properties.color',
                'styles.button_secondary_fields_group.icon_secondary_size'      => 'general.secondary_button_fields_group.secondary_icon.properties.size',
                'styles.button_secondary_fields_group.icon_secondary_color'     => 'general.secondary_button_fields_group.secondary_icon.properties.color',
                'styles.button_connector_fields_group.icon_connector_size'      => 'general.connector_button_fields_group.connector_icon.properties.size',
            ],
            'flip-box'             => [
                'general.flipbox_front_settings.flipbox_front_background_media_enable' => 'styles.flipbox_front_style_settings.flipbox_front_background_image_enable',
                'general.flipbox_front_settings.flipbox_front_bg'                      => 'styles.flipbox_front_style_settings.flipbox_front_bg_image',
                'general.flipbox_back_settings.flipbox_back_background_media_enable'   => 'styles.flipbox_back_style_settings.flipbox_back_background_image_enable',
                'general.flipbox_back_settings.flipbox_back_bg'                        => 'styles.flipbox_back_style_settings.flipbox_back_bg_image',
                'general.flipbox_system_settings.enable_height'                        => 'styles.flipbox_common_style_settings.common_enable_height',
                'general.flipbox_system_settings.flipbox_height'                       => 'styles.flipbox_common_style_settings.commom_flipbox_height',
                'general.flipbox_system_settings.flexbox_border_width'                 => 'styles.flipbox_common_style_settings.flipbox_border',
                'general.flipbox_system_settings.flexbox_border_type'                  => 'styles.flipbox_common_style_settings.flipbox_border',
                'general.flipbox_system_settings.flexbox_border_color'                 => 'styles.flipbox_common_style_settings.flipbox_border',
                'general.flipbox_system_settings.flexbox_border_radius'                => 'styles.flipbox_common_style_settings.flipbox_border',
                'styles.flipbox_back_style_settings.flipbox_back_v_align'              => 'general.flipbox_back_settings.flipbox_back_vertical_align',
                'styles.flipbox_front_style_settings.flipbox_front_v_align'            => 'general.flipbox_front_settings.flipbox_front_vertical_align',
            ],
            'accordion'            => [
                'styles.accordion_fg_icon_style.icon_alignment' => 'general.accordion_fg_icon.icon_nalignment',
            ],
            'blurb'                => [
                'styles.blurb_fg_content.content_v_align'     => 'general.blurb_fg_layout.img_content_v_align',
                'styles.blurb_fg_image_style.image_alignment' => 'general.blurb_fg_layout.img_alignment',
                'styles.blurb_fg_image_style.image_position'  => 'general.blurb_fg_layout.img_position',
            ],
            'button'               => [
                'general.button_fields_group.alignment'                    => [
                    'path' => 'styles.button_spacing_fields_group.nalignment',
                    'map'  => static function ($data, $newData) {
                        if (is_array($data) && isset($data['desktop'])) {
                            return $data;
                        }

                        if (is_array($newData) && isset($newData['desktop'])) {
                            return $newData;
                        }

                        if ( ! is_array($newData)) {
                            $newData = array();
                        }

                        $newData['desktop'] = $data;

                        return $newData;
                    }
                ],
                // 'styles.button_border_fields_group.btn_border_type'        => 'styles.button_border_group.btn_border.state.normal.properties.border_type',
                // 'styles.button_border_fields_group.btn_border_width'       => 'styles.button_border_group.btn_border.state.normal.properties.border_width',
                // 'styles.button_border_fields_group.btn_border_color'       => 'styles.button_border_group.btn_border.state.normal.properties.border_color',
                // 'styles.button_border_fields_group.btn_hover_border_color' => 'styles.button_border_group.btn_border.state.hover.properties.border_color',
                // 'styles.button_border_fields_group.btn_border_radius'      => 'styles.button_border_group.btn_border.state.normal.properties.border_radius',
            ],
            'heading'              => [
                'general.heading_fields_group.alignment' => [
                    'path' => 'styles.opt_fields_group.nalignment',
                    'map'  => static function ($data, $newData) {

                        if (is_array($data) && isset($data['desktop'])) {
                            return $data;
                        }
                        if (is_array($newData) && isset($newData['desktop'])) {
                            return $newData;
                        }

                        if ( ! is_array($newData)) {
                            $newData = array();
                        }
                        $newData['desktop'] = $data;

                        return $newData;
                    }
                ],
            ],
            'icon-list'            => [
                'general.iconlist_fg_layout.layout'        => [
                    'path' => 'general.iconlist_fg_layout.choose_layout',
                    'map'  => static function ($data, $newData) {

                        if (is_array($data) && isset($data['desktop'])) {
                            return $data;
                        }
                        if (is_array($newData) && isset($newData['desktop'])) {
                            return $newData;
                        }

                        if ( ! is_array($newData)) {
                            $newData = array();
                        }

                        $data = $data === 'vr' ? 'vertical' : 'horizontal';

                        $newData['desktop'] = $data;

                        return $newData;
                    }
                ],
                'general.iconlist_fg_layout.alignment'     => [
                    'path' => 'general.iconlist_fg_layout.alignment',
                    'map'  => static function ($data, $newData) {

                        if (is_array($data) && isset($data['desktop'])) {
                            return $data;
                        }
                        if (is_array($newData) && isset($newData['desktop'])) {
                            return $newData;
                        }

                        if ( ! is_array($newData)) {
                            $newData = array();
                        }

                        if ( ! $data) {
                            $data = 'left';
                        }

                        $newData['desktop'] = $data;

                        return $newData;
                    }
                ],
                'styles.typography_setting.text_font'      => 'styles.spacing_setting.typo_for_text',
                'styles.border_setting.icon_border_width'  => 'styles.spacing_setting.border_for_icon.state.normal.properties.border_width',
                'styles.border_setting.icon_border_type'   => 'styles.spacing_setting.border_for_icon.state.normal.properties.border_type',
                'styles.border_setting.icon_border_color'  => 'styles.spacing_setting.border_for_icon.state.normal.properties.border_color',
                'styles.border_setting.icon_border_radius' => 'styles.spacing_setting.border_for_icon.state.normal.properties.border_radius'
            ],
            'image'                => [
                'styles.border_fields_group.img_border_radius'                                                  => 'styles.border_fields_group.img_border.state.normal.properties.border_radius',
                'styles.border_fields_group.img_border_color'                                                   => 'styles.border_fields_group.img_border.state.normal.properties.border_color',
                'styles.border_fields_group.img_border_type'                                                    => 'styles.border_fields_group.img_border.state.normal.properties.border_type',
                'styles.border_fields_group.img_border_width'                                                   => 'styles.border_fields_group.img_border.state.normal.properties.border_width',
                'styles.caption_field_group.caption_field_group_background_color.state.normal.properties.color' => 'styles.caption_field_group.caption_background_color',
                'styles.caption_field_group.caption_background_color.state.normal.properties.color'             => 'styles.caption_field_group.caption_background_color',
                'styles.icon_fields_group.icon_color'                                                           => 'general.image_fields_group.image.media.properties.color',
            ],
            'social-icon'          => [
                'general.basic_setting.alignment'            => [
                    'path' => 'general.basic_setting.alignment',
                    'map'  => static function ($data, $newData) {
                        if (is_array($data) && isset($data['desktop'])) {
                            return $data;
                        }
                        if (is_array($newData) && isset($newData['desktop'])) {
                            return $newData;
                        }

                        if ( ! is_array($newData)) {
                            $newData = array();
                        }

                        $data               = $data === '' ? 'left' : $data;
                        $newData['desktop'] = $data;

                        return $newData;
                    }
                ],
                'general.basic_setting.layout'               => [
                    'path' => 'general.basic_setting.choose_layout',
                    'map'  => static function ($data, $newData) {

                        if (is_array($data) && isset($data['desktop'])) {
                            return $data;
                        }
                        if (is_array($newData) && isset($newData['desktop'])) {
                            return $newData;
                        }

                        if ( ! is_array($newData)) {
                            $newData = array();
                        }

                        $data = $data === 'vr' ? 'vertical' : 'horizontal';

                        $newData['desktop'] = $data;

                        return $newData;
                    }
                ],
                'styles.border_setting.social_border_width'  => 'styles.spacing_setting.btn_border.state.normal.properties.border_width',
                'styles.border_setting.social_border_type'   => 'styles.spacing_setting.btn_border.state.normal.properties.border_type',
                'styles.border_setting.social_border_color'  => 'styles.spacing_setting.btn_border.state.normal.properties.border_color',
                'styles.border_setting.social_border_radius' => 'styles.spacing_setting.btn_border.state.normal.properties.border_radius'
            ],
            'alert'                => [
                'general.alert_fg_text.alignment' => 'styles.alert_fg_alignment_style.nalignment.desktop',
            ],
            'call-to-action'       => [
                'styles.call_to_action_fg_element_style.vertical_position'     => 'general.call_to_action_fg_additional.cta_vertical_position',
                'styles.call_to_action_button_style.button_border_width'       => 'styles.call_to_action_button_style.button_border_new.state.normal.properties.border_width',
                'styles.call_to_action_button_style.button_border_radius'      => 'styles.call_to_action_button_style.button_border_new.state.normal.properties.border_radius',
                'styles.call_to_action_button_style.button_border_color'       => 'styles.call_to_action_button_style.button_border_new.state.normal.properties.border_color',
                'styles.call_to_action_button_style.button_hover_border_color' => 'styles.call_to_action_button_style.button_border_new.state.hover.properties.border_color',
            ],
            'gallery'              => [
                // 'general.gallery_fg_columns.column'       => 'general.gallery_fg_columns.layout.desktop',
                'general.gallery_fg_columns.column'       => [
                    'path' => 'general.gallery_fg_columns.layout',
                    'map'  => static function ($data, $newData) {
                        $newArray = [];
                        if (is_array($data) && isset($data['desktop'])) {
                            $newArray = $data;
                        }
                        elseif (is_array($newData) && isset($newData['desktop'])) {
                            $newArray = $newData;
                        }
                        elseif ( ! is_array($newData)) {
                            $newArray = array();
                        }
                        if(!$newArray){
                            $newData['desktop'] = is_string($data) ? "qx-child-width-1-" . $data : "qx-child-width-1-3";
                        }

                        if(isset($newData['phone']) && is_array($newData['phone'])){
                            $newData['phone'] = $newData['phone']['value'] ?? $newData['phone'] ?? $newData['mobile'];
                        }

                        return $newData;
                    }
                ],
                'general.divider_border_fields_group.gap'    => [
                    'path' => 'styles.divider_style_option.border_gap',
                    'map'  => static function ($data, $newData) {
                        $newArray = [];
                        if (is_array($data) && isset($data['desktop'])) {
                            $newArray = $data;
                        }
                        elseif (is_array($newData) && isset($newData['desktop'])) {
                            $newArray = $newData;
                        }
                        elseif ( ! is_array($newData)) {
                            $newArray = array();
                        }
                        if(!$newArray){
                            $newData['desktop'] = $data;
                        }

                        if(isset($newData['phone']) && is_array($newData['phone'])){
                            $newData['phone'] = $newData['phone']['value'] ?? $newData['phone'] ?? $newData['mobile'];
                        }

                        return $newData;
                    }
                ],
                'general.gallery_fg_image.image_fit'      => 'styles.image_style.image_fit_style',
                'general.gallery_fg_image.image_position' => 'styles.image_style.image_position_style',
                'styles.filter_typo_style.filter_typo'    => 'styles.filter_style.filter_item_typo',
            ],
            'person'               => [
                'styles.person_fg_image_style.image_alignment' => 'general.person_fg_details.person_image_alignment',
                'styles.person_fg_panel.content_v_align'       => 'general.person_fg_details.person_content_v_align'
            ],
            'person-pro'           => [
                'styles.personpro_fg_image_style.image_alignment' => 'general.personpro_fg_details.personpro_image_alignment'
            ],
            'slider-pro'           => [
                'general.slider-pro_fg_height.height_custom'    => 'styles.slider-pro_fg_slider-style.slider_height_custom',
                'general.slider-pro_fg_navigation.thumb_width'  => 'styles.slider-pro_fg_dot-style.thumb_width',
                'general.slider-pro_fg_navigation.thumb_height' => 'styles.slider-pro_fg_dot-style.thumb_height'
            ],
            'smart-tab'            => [
                'styles.tab_fg_icon_style.icon_alignment'  => 'general.options.icon_alignment',
                'styles.tab_fg_style.text_alignment'       => 'general.options.text_alignment',
                'styles.tab_fg_icon_style.content_v_align' => 'general.options.content_v_align'
            ],
            'tab'                  => [
                'styles.tab_fg_title_style.title_alignment'         => 'general.tab_fg_Aditional.title_alignment',
                'styles.tab_fg_content_style.content_alignment'     => 'general.tab_fg_Aditional.content_alignment',
                'styles.tab_fg_content_image_style.media_position'  => 'general.tab_fg_Aditional.media_position',
                'styles.tab_fg_content_image_style.image_alignment' => 'general.tab_fg_Aditional.image_alignment'
            ],
            'testimonial'          => [
                'styles.image_fields_group.image_alignment'     => 'general.testimonial_fg_media.image_alignment',
                'styles.image_fields_group.image_border_radius' => 'styles.image_fields_group.image_border.state.normal.properties.border_radius',
            ],
            'testimonial-carousel' => [
                'general.testimonial-carousel_fg_layouts.layout_skin'      => 'general.testimonial-carousel_fg_layouts.layout_grid.desktop',
                'styles.testimonial-carousel_fg_image.image_border_radius' => 'styles.testimonial-carousel_fg_image.image_border.state.normal.properties.border_radius',
            ],
            'divider'              => [
                'general.divider_border_fields_group.style'  => 'styles.divider_style_option.border_style',
                'general.divider_border_fields_group.weight.value' => 'styles.divider_style_option.border_weight',
                'general.divider_border_fields_group.color'  => 'styles.divider_style_option.border_color',
                // 'general.divider_border_fields_group.gap'    => 'styles.divider_style_option.border_gap',
                'general.divider_border_fields_group.gap'    => [
                    'path' => 'styles.divider_style_option.border_gap',
                    'map'  => static function ($data, $newData) {
                        $newArray = [];
                        if (is_array($data) && isset($data['desktop'])) {
                            $newArray = $data;
                        }
                        elseif (is_array($newData) && isset($newData['desktop'])) {
                            $newArray = $newData;
                        }
                        elseif ( ! is_array($newData)) {
                            $newArray = array();
                        }
                        if(!$newArray){
                            $newData['desktop'] = $data;
                        }

                        if(isset($newData['phone']) && is_array($newData['phone'])){
                            $newData['phone'] = $newData['phone']['value'] ?? $newData['phone'] ?? $newData['mobile'];
                        }

                        return $newData;
                    }
                ],
                'general.divider_border_fields_group.width'  => 'styles.divider_style_option.border_width',
                'general.divider_fields_group.alignment'     => 'styles.divider_style_option.border_alignment'
            ],
            'joomla-article'       => [
                'general.article_core.image_size' => 'styles.articles_common.image_size',
            ],
            'joomla-articles'      => [
                'general.articles_option.alignment'   => 'styles.articles_common.nalignment',
                'options.articles_options.image_size' => 'styles.articles_common.image_size',
            ],
            'animated-headline'    => [
                'general.animated_fg_content.animate_alignment'    => [
                    'path' => 'styles.alert_fg_align_style.nanimate_alignment',
                    'map'  => static function ($data, $newData) {
                        if (is_array($data) && isset($data['desktop'])) {
                            return $data;
                        }
                        if (is_array($newData) && isset($newData['desktop'])) {
                            return $newData;
                        }
                        if ( ! is_array($newData)) {
                            $newData = array();
                        }

                        $newData['desktop'] = $data;

                        return $newData;
                    }
                ],
                'styles.animate_shape_setting.animate_shapeWeight' => [
                    'path' => 'styles.animate_shape_setting.animate_shapeWeight',
                    'map'  => static function ($data, $newData) {
                        if (isset($data['value'])) {
                            return $data['value'];
                        }

                        return $data;
                    }
                ],
            ],
        ];

        if (isset($changeMap[$node['slug']])) {
            return $this->mapOldValuesToNew($node, $changeMap[$node['slug']]);
        }

        return $node;
    }

    /**
     * @param  array  $node
     * @param  array  $map
     *
     * @return array
     *
     * @since 3.0.0
     */
    private function mapOldValuesToNew(array $node, array $map): array
    {
        foreach ($map as $from => $to) {
            $map = null;
            if (is_array($to)) {
                $map = $to['map'];
                $to  = $to['path'];
            }
            $targetPath    = array_merge(['form'], explode('.', $to));
            $sourcePath    = array_merge(['form'], explode('.', $from));
            $existingValue = $this->getIn($node, $targetPath);

            /* commented below code to migrate empty and string value to responsive */
            // if ($this->_isEmpty($existingValue)) {
            //     continue;
            // }
            // if ( ! $this->_isEmpty($existingValue)) {
            //     if ( ! is_array($existingValue) || ! isset($existingValue['desktop'])) {
            //         continue;
            //     }
            // }

            $value = $this->getIn($node, $sourcePath);
            if (is_array($map) && ! is_array($value) && array_key_exists($value, $map)) {
                $value = $map[$value];
            }
            if (is_callable($map)) {
                $value = $map($value, $existingValue);
            }

            $node = $this->setIn($node, $targetPath, $value);
        }

        return $node;
    }

    /**
     * @param $node
     * @param $path
     *
     * @return mixed|null
     *
     * @since 3.0.0
     */
    private function getIn($node, $path)
    {
        $_node = $node;
        foreach ($path as $index) {
            if ( ! isset($_node[$index])) {
                return null;
            }

            $_node = $_node[$index];
        }

        return $_node;
    }

    /**
     * @param $node
     * @param $path
     * @param $value
     *
     * @return mixed
     *
     * @since 3.0.0
     */
    private function setIn($node, $path, $value)
    {
        $fKey = array_shift($path);
        if (count($path) > 0) {
            if ( ! $node) {
                $node = [];
            }
            if ( ! array_key_exists($fKey, $node)) {
                $node[$fKey] = [];
            }
            $node[$fKey] = $this->setIn($node[$fKey], $path, $value);
        } else {
            $node[$fKey] = $value;
        }

        return $node;
    }
}
