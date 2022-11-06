<?php

if ( ! class_exists('QuixJoomlaMenuElement')) {
    include_once(__DIR__.'/helper.php');
}

return [
    'slug'   => 'joomla-menu',
    'name'   => 'Joomla Menu',
    'groups' => ['joomla'],
    'helpId' => '5d1ff02d04286369ad8d5f05',
    'form'   => [
        'general' => [
            [
                'name'   => 'menu_core',
                'label'  => 'Main Menu',
                'type'   => 'fields-group',
                'status' => 'open',
                'schema' => [
                    [
                        'name'  => 'menu_type',
                        'type'  => 'jmenu',
                        'label' => 'Select Menu Type'
                    ],
                    [
                        'name' => 'menu_divider',
                        'type' => 'divider',
                        'help' => 'Go to the <a target="_blank" href="administrator/index.php?option=com_menus&view=menus">Menu Settings</a> and Manage your menu.'
                    ],
                    [
                        'name'       => 'menu_layout',
                        'type'       => 'select',
                        'label'      => 'Layout',
                        'value'      => 'horizontal',
                        'responsive' => false,
                        'options'    => [
                            'horizontal' => 'Horizontal',
                            'vertical'   => 'Vertical'
                        ]
                    ],
                    [
                        'name'       => 'menu_alignment',
                        'type'       => 'choose',
                        'label'      => 'Alignment',
                        'value'      => 'left',
                        'responsive' => false,
                        'options'    => [
                            'left'   => ['label' => 'Left', 'icon' => 'qxuicon-align-left'],
                            'center' => ['label' => 'Center', 'icon' => 'qxuicon-align-center'],
                            'right'  => ['label' => 'Right', 'icon' => 'qxuicon-align-right']
                        ],
                        'depends'    => [
                            'menu_layout' => 'horizontal'
                        ]
                    ],
                    [
                        'name'    => 'menu_align_divider',
                        'type'    => 'divider',
                        'help'    => 'Align the navbar.',
                        'depends' => [
                            'menu_layout' => 'horizontal'
                        ]
                    ],
                    [
                        'name'       => 'menu_sub_indicator',
                        'label'      => 'Submenu Indicator',
                        'type'       => 'select',
                        'value'      => 'classic',
                        'responsive' => false,
                        'options'    => [
                            'none'    => 'None',
                            'classic' => 'Classic',
                            'chevron' => 'Chevron',
                            'angle'   => 'Angle'
                        ],
                        'depends'    => [
                            'menu_layout' => 'horizontal'
                        ]
                    ],
                    [
                        'name'    => 'menu_sub_indicator_divider',
                        'type'    => 'divider',
                        'help'    => 'Select the indicator that will display where have the sub-menu items.',
                        'depends' => [
                            'menu_layout' => 'horizontal'
                        ]
                    ]
                ]
            ],
            [
                'name'    => 'dropdown_menu_core',
                'label'   => 'Dropdown Menu',
                'type'    => 'fields-group',
                'status'  => 'close',
                'depends' => [
                    'menu_layout' => 'horizontal'
                ],
                'schema'  => [
                    [
                        'name'       => 'dropdown_menu_open',
                        'label'      => 'Open Mode',
                        'type'       => 'choose',
                        'responsive' => false,
                        'value'      => 'hover',
                        'options'    => [
                            'hover' => ['label' => 'Hover'],
                            'click' => ['label' => 'Click']
                        ]
                    ],
                    [
                        'name' => 'dropdown_mode_divider',
                        'type' => 'divider',
                        'help' => 'Select the mode that will open the dropdown menu.'
                    ],
                    [
                        'name'  => 'dropdown_dropbar',
                        'label' => 'Dropbar',
                        'type'  => 'switch',
                        'value' => false
                    ],
                    [
                        'name' => 'dropdown_dropbar_divider',
                        'type' => 'divider',
                        'help' => 'The dropdown full-width mode.'
                    ],
                    [
                        'name'       => 'dropdown_dropbar_mode',
                        'label'      => 'Animation',
                        'type'       => 'choose',
                        'value'      => 'slide',
                        'responsive' => false,
                        'depends'    => ['dropdown_dropbar' => true],
                        'options'    => [
                            'push'  => ['label' => 'Push'],
                            'slide' => ['label' => 'Slide']
                        ]
                    ]
                ]
            ],
            [
                'name'   => 'mobile_dropdown_menu_core',
                'label'  => 'Responsive Menu',
                'type'   => 'fields-group',
                'status' => 'close',
                'schema' => [
                    [
                        'name'       => 'mobile_dropdown_breakpoint',
                        'label'      => 'Breakpoint',
                        'type'       => 'choose',
                        'value'      => 'm',
                        'responsive' => false,
                        'options'    => [
                            'm'   => ['label' => 'Tablet'],
                            's  ' => ['label' => 'Mobile']
                        ]
                    ],
                    [
                        'name' => 'mobile_menu_breakpoint_divider',
                        'type' => 'divider',
                        'help' => 'Responsive menu breakpoint device. Off-canvas or dropdown menu will be shown.'
                    ],
                    [
                        'name'       => 'mobile_dropdown_type',
                        'label'      => 'Menu Type',
                        'type'       => 'choose',
                        'value'      => 'offcanvas',
                        'responsive' => false,
                        'options'    => [
                            'toggle'    => ['label' => 'Toggle'],
                            'offcanvas' => ['label' => 'Offcanvas']
                        ]
                    ],
                    [
                        'name' => 'mobile_dropdown_type_divider',
                        'type' => 'divider',
                        'help' => 'Choose offcanvas that will display the responsive mode from the selected device.'
                    ],
                    [
                        'name'       => 'mobile_dropdown_align',
                        'label'      => 'Alignment',
                        'type'       => 'choose',
                        'value'      => 'left',
                        'responsive' => false,
                        'options'    => [
                            'left'   => ['label' => 'Left', 'icon' => 'qxuicon-align-left'],
                            'center' => ['label' => 'Center', 'icon' => 'qxuicon-align-center'],
                            'right'  => ['label' => 'Right', 'icon' => 'qxuicon-align-right']
                        ]
                    ],
                    [
                        'name'       => 'mobile_offcanvas_right',
                        'label'      => 'Offcanvas Position',
                        'type'       => 'choose',
                        'value'      => 'false',
                        'responsive' => false,
                        'options'    => [
                            'false' => ['label' => 'Left'],
                            'true'  => ['label' => 'Right']
                        ],
                        'depends'    => [
                            'mobile_dropdown_type' => ['offcanvas']
                        ]
                    ],
                    [
                        'name'       => 'mobile_offcanvas_effect',
                        'label'      => 'Open Mode',
                        'type'       => 'choose',
                        'value'      => 'slide',
                        'responsive' => false,
                        'options'    => [
                            'slide'  => ['label' => 'Slide'],
                            'push'   => ['label' => 'Push'],
                            'reveal' => ['label' => 'Reveal']
                        ],
                        'depends'    => [
                            'mobile_dropdown_type' => ['offcanvas']
                        ]
                    ]
                ]
            ]
        ],
        'styles'  => [
            [
                'name'   => 'main_menu_options',
                'label'  => 'Main Menu',
                'type'   => 'fields-group',
                'status' => 'close',
                'schema' => [
                    [
                        'name'    => 'mainmenu_typo',
                        'type'    => 'typography',
                        'label'   => 'Typography',
                        'popover' => true
                    ],
                    [
                        'name'  => 'menu_colorsdiv',
                        'type'  => 'divider',
                        'label' => 'Item Text Color'
                    ],
                    [
                        'name'  => 'menu_text_color',
                        'type'  => 'color',
                        'label' => 'Color',
                        'value' => '#727272'
                    ],
                    [
                        'name'  => 'menu_text_hover_color',
                        'type'  => 'color',
                        'label' => 'Hover Color',
                        'value' => '#000000'
                    ],
                    [
                        'name'  => 'menu_text_active_color',
                        'type'  => 'color',
                        'label' => 'Active Color',
                        'value' => '#000000'
                    ],
                    [
                        'name'  => 'menu_bgcolorsdiv',
                        'type'  => 'divider',
                        'label' => 'Item Background Color'
                    ],
                    [
                        'name'  => 'menu_bghover_color',
                        'type'  => 'color',
                        'label' => 'Hover Color',
                    ],
                    [
                        'name'  => 'menu_bgactive_color',
                        'type'  => 'color',
                        'label' => 'Active Color'
                    ],
                    [
                        'name'  => 'menu_paddingdiv',
                        'type'  => 'divider',
                        'label' => 'Item Padding'
                    ],
                    [
                        'name'  => 'menu_padding',
                        'label' => 'Padding',
                        'type'  => 'dimensions',
                        'value' => [
                            'desktop' => [
                                'top'    => 10,
                                'bottom' => 10,
                                'left'   => 10,
                                'right'  => 10
                            ],
                            'tablet'  => [
                                'top'    => 10,
                                'bottom' => 10,
                                'left'   => 10,
                                'right'  => 10
                            ],
                            'phone'   => [
                                'top'    => 10,
                                'bottom' => 10,
                                'left'   => 10,
                                'right'  => 10
                            ]
                        ]
                    ],
                    [
                        'name'  => 'menu_spacingdiv',
                        'type'  => 'divider',
                        'label' => 'Item Spacing'
                    ],
                    [
                        'name'        => 'menu_space_between',
                        'label'       => 'Space Between',
                        'type'        => 'slider',
                        'max'         => 100,
                        'min'         => 0,
                        'value'       => 1,
                        'units'       => 'px',
                        'defaultUnit' => 'px',
                        'responsive'  => false
                    ],
                    [
                        'name'  => 'menu_height_spacer',
                        'type'  => 'divider',
                        'label' => 'Menu Height'
                    ],
                    [
                        'name'        => 'menu_height',
                        'label'       => 'Menu Height',
                        'type'        => 'slider',
                        'max'         => 200,
                        'min'         => 0,
                        'value'       => 50,
                        'units'       => 'px',
                        'defaultUnit' => 'px',
                        'responsive'  => false
                    ]
                ]
            ],
            [
                'name'   => 'dropdown_menu_options',
                'label'  => 'Dropdown Menu',
                'type'   => 'fields-group',
                'status' => 'close',
                'schema' => [
                    [
                        'name'    => 'submenu_typo',
                        'type'    => 'typography',
                        'label'   => 'Typography',
                        'popover' => true
                    ],
                    [
                        'name'  => 'dropdown_colorsdiv',
                        'type'  => 'divider',
                        'label' => 'Item Text Color'
                    ],
                    [
                        'name'  => 'dropdown_text_color',
                        'type'  => 'color',
                        'label' => 'Color'
                    ],
                    [
                        'name'  => 'dropdown_hover_color',
                        'type'  => 'color',
                        'label' => 'Hover Color'
                    ],
                    [
                        'name'  => 'dropdown_active_color',
                        'type'  => 'color',
                        'label' => 'Active Color'
                    ],
                    [
                        'name'  => 'dropdown_bgcolorsdiv',
                        'type'  => 'divider',
                        'label' => 'Item Background'
                    ],
                    [
                        'name'  => 'dropdown_itemHoverbg_color',
                        'type'  => 'color',
                        'label' => 'Hover Background'
                    ],
                    [
                        'name'  => 'dropdown_itemActivebg_color',
                        'type'  => 'color',
                        'label' => 'Active Background'
                    ],
                    [
                        'name'  => 'dropdown_bddiv',
                        'type'  => 'divider',
                        'label' => 'Item Border'
                    ],
                    [
                        'name'    => 'dropdown_item_border',
                        'type'    => 'border',
                        'label'   => 'Border',
                        'popover' => true
                    ],
                    [
                        'name'  => 'dropdown_bgcolorsdiv',
                        'type'  => 'divider',
                        'label' => 'Item Padding'
                    ],
                    [
                        'name'  => 'dropdown_item_padding',
                        'label' => 'Padding',
                        'type'  => 'dimensions'
                    ],
                    [
                        'name'  => 'dropdown_div',
                        'type'  => 'divider',
                        'label' => 'Dropdown Menu Area'
                    ],
                    [
                        'name'        => 'dropdown_distance',
                        'label'       => 'Distance',
                        'type'        => 'slider',
                        'max'         => 200,
                        'min'         => -200,
                        'value'       => 52,
                        'units'       => 'px',
                        'defaultUnit' => 'px',
                        'responsive'  => false
                    ],
                    [
                        'name'        => 'dropdown_width',
                        'label'       => 'Width',
                        'type'        => 'slider',
                        'max'         => 1000,
                        'value'       => 200,
                        'units'       => 'px',
                        'defaultUnit' => 'px',
                        'responsive'  => false
                    ],
                    [
                        'name'  => 'dropdown_bg_color',
                        'type'  => 'color',
                        'label' => 'Background Color',
                        'value' => '#f8f8f8'
                    ],
                    [
                        'name'    => 'dropdown_border',
                        'type'    => 'border',
                        'label'   => 'Border',
                        'popover' => true
                    ],
                    [
                        'name'  => 'dropdown_padding',
                        'label' => 'Padding',
                        'type'  => 'dimensions',
                        'value' => [
                            'desktop' => [
                                'top'    => 15,
                                'bottom' => 15,
                                'left'   => 15,
                                'right'  => 15
                            ],
                            'tablet'  => [
                                'top'    => 15,
                                'bottom' => 15,
                                'left'   => 15,
                                'right'  => 15
                            ],
                            'phone'   => [
                                'top'    => 15,
                                'bottom' => 15,
                                'left'   => 15,
                                'right'  => 15
                            ]
                        ]                        
                    ]
                ]
            ],
            [
                'name'   => 'mobile_menu_options',
                'label'  => 'Responsive Menu',
                'type'   => 'fields-group',
                'status' => 'close',
                'schema' => [
                    [
                        'name'  => 'hamburger_div',
                        'type'  => 'divider',
                        'label' => 'Hamburger Icon'
                    ],
                    [
                        'name'  => 'hamburger_icon_color',
                        'label' => 'Color',
                        'type'  => 'color'
                    ],
                    [
                        'name'  => 'hamburger_bg',
                        'label' => 'Background Color',
                        'type'  => 'color'
                    ],
                    [
                        'name'  => 'hamburger_padding',
                        'label' => 'Padding',
                        'type'  => 'dimensions'
                    ],
                    [
                        'name'    => 'hamburger_border',
                        'type'    => 'border',
                        'label'   => 'Border',
                        'popover' => true
                    ],
                    [
                        'name'  => 'offcanvas_div',
                        'type'  => 'divider',
                        'label' => 'Offcanvas Menu'
                    ],
                    [
                        'name'  => 'offcanvas_bg',
                        'type'  => 'color',
                        'label' => 'Background Color',
                        'value' => '#ffffff'
                    ],
                    [
                        'name'  => 'offcanvas_padding',
                        'label' => 'Padding',
                        'type'  => 'dimensions'
                    ],
                    [
                        'name'  => 'offcanvas_item_div',
                        'type'  => 'divider',
                        'label' => 'Offcanvas Item'
                    ],
                    [
                        'name'  => 'offcanvas_item_text_color',
                        'type'  => 'color',
                        'label' => 'Color'
                    ],
                    [
                        'name'  => 'offcanvas_item_text_hover_color',
                        'type'  => 'color',
                        'label' => 'Hover Color'
                    ],
                    [
                        'name'  => 'offcanvas_item_indicator_color',
                        'type'  => 'color',
                        'label' => 'Sub Menu Indicator Color',
                    ],
                    [
                        'name'        => 'offcanvas_space_between',
                        'label'       => 'Item space',
                        'type'        => 'slider',
                        'max'         => 100,
                        'min'         => 0,
                        'value'       => 1,
                        'units'       => 'px',
                        'defaultUnit' => 'px',
                        'responsive'  => false
                    ],
                ]
            ]
        ]
    ]
];
