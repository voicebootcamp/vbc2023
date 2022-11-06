<?php
return [
    'slug'   => 'joomla-articles',
    'name'   => 'Joomla Articles',
    'groups' => ['joomla', 'pro'],
    'helpId' => '5d1fec132c7d3a5cd38ec1be',
    'form'   => [
        'general' => [
            [
                'name'   => 'articles_option',
                'label'  => 'Articles Options',
                'type'   => 'fields-group',
                'status' => 'open',
                'schema' => [
                    [
                        'name'    => 'category',
                        'type'    => 'jcategory',
                        'label'   => 'Select Category',
                        'options' => [],
                        'value'   => 'root'
                    ],
                    [
                        'name'  => 'show_child_category_articles',
                        'type'  => 'switch',
                        'label' => 'Child Category Articles',
                        'help'  => 'Show/Hide child category articles',
                        'value' => true
                    ],

                    [
                        'name'  => 'count',
                        'label' => 'No of Articles',
                        'type'  => 'text',
                        'value' => 3,
                        'help'  => 'Num of articles you want to show'
                    ],
                    [
                        'name'       => 'show_featured',
                        'type'       => 'choose',
                        'label'      => 'Featured Articles',
                        'responsive' => false,
                        'value'      => 'show',
                        'options'    => [
                            'show' => ['label' => 'Show'],
                            'hide' => ['label' => 'Hide'],
                            'only' => ['label' => 'Only featured']
                        ]
                    ],
                    [
                        'name'       => 'article_ordering',
                        'type'       => 'choose',
                        'label'      => 'Article Ordering',
                        'value'      => 'a.title',
                        'responsive' => false,
                        'options'    => [
                            'publish_up' => ['label' => 'Latest'],
                            'a.hits'     => ['label' => 'Popular'],
                            'a.ordering' => ['label' => 'Ordering'],
                            'a.title'    => ['label' => 'Title']
                        ]
                    ],
                    [
                        'name'       => 'article_ordering_direction',
                        'type'       => 'choose',
                        'label'      => 'Ordering Direction',
                        'value'      => 'ASC',
                        'responsive' => false,
                        'options'    => [
                            'DESC' => ['label' => 'Descending', 'icon' => 'qxuicon-arrow-alt-from-bottom'],
                            'ASC'  => ['label' => 'Ascending', 'icon' => 'qxuicon-arrow-alt-from-top']
                        ]
                    ],
                ]
            ],
            [
                'name'   => 'articles_layout',
                'label'  => 'Layout',
                'type'   => 'fields-group',
                'schema' => [
                    [
                        'name'    => 'layout',
                        'type'    => 'select',
                        'value'   => 'deck',
                        'options' => [
                            'list'    => 'List',
                            'group'   => 'Group',
                            'deck'    => 'Decks',
                        ]
                    ],
                    [
                        'name' => 'layout_grid',
                        'label' => 'Grid',
                        'type' => 'choose',
                        'responsive' => true,
                        'value' => [
                            'desktop' => 'qx-child-width-1-3',
                            'tablet' => 'qx-child-width-1-2',
                            'phone' => 'qx-child-width-1-1'
                        ],
                        'options' => [
                            'qx-child-width-1-1' => ['label' => '1'],
                            'qx-child-width-1-2' => ['label' => '2'],
                            'qx-child-width-1-3' => ['label' => '3'],
                            'qx-child-width-1-4' => ['label' => '4']
                        ],
                        'depends' => [
                            'layout' => ['deck', 'group']
                        ]
                    ],
                    [
                        'name' => 'grid_gap',
                        'label' => 'Grid Gap',
                        'type' => 'choose',
                        'responsive' => false,
                        'value' => 'qx-grid-medium',
                        'options' => [
                            'qx-grid-small' => ['label' => 'Small'],
                            'qx-grid-medium' => ['label' => 'Medium'],
                            'qx-grid-large' => ['label' => 'Large']
                        ],
                        'depends' => [
                            'layout' => 'deck'
                        ]
                    ]

                ]
            ]
        ],
        'options' => [
            [
                'name'   => 'articles_options',
                'label'  => 'Visual options',
                'type'   => 'fields-group',
                'status' => 'open',
                'schema' => [

                    [
                        'name'  => 'link_titles',
                        'type'  => 'switch',
                        'label' => 'Link Titles',
                        'value' => true
                    ],
                    [
                        'name'  => 'show_image',
                        'type'  => 'switch',
                        'label' => 'Show Image',
                        'value' => false
                    ],
                    [
                        'name'  => 'show_introtext',
                        'type'  => 'switch',
                        'value' => true
                    ],

                    [
                        'name'    => 'introtext_limit',
                        'type'    => 'text',
                        'value'   => 300,
                        'label'   => 'Character Limit',
                        'depends' => ['show_introtext' => true]
                    ],

                    [
                        'name'  => 'show_readmore',
                        'type'  => 'switch',
                        'value' => true,
                        'label' => 'Readmore Button'
                    ],

                    [
                        'name'    => 'readmore_style',
                        'type'    => 'select',
                        'value'   => 'qx-btn-primary',
                        'options' => [
                            'qx-btn-primary'   => 'Primary',
                            'qx-btn-secondary' => 'Secondary',
                            'qx-btn-success'   => 'Success',
                            'qx-btn-info'      => 'Info',
                            'qx-btn-warning'   => 'Warning',
                            'qx-btn-danger'    => 'Danger',
                            'qx-btn-link'      => 'Link',
                            'qx-btn-light'     => 'Light',
                            'qx-btn-dark'      => 'Dark',
                        ],
                        'depends' => ['show_readmore' => true]
                    ],
                    [
                        'name'    => 'readmore_size',
                        'type'    => 'select',
                        'value'   => 'qx-btn-sm',
                        'options' => [
                            'qx-btn-lg' => 'Large',
                            'qx-btn-sm' => 'Small',
                            'qx-btn-md' => 'Default'
                        ],
                        'depends' => ['show_readmore' => true]
                    ],

                    [
                        'name'    => 'readmore_text',
                        'type'    => 'text',
                        'value'   => 'Read More...',
                        'depends' => ['show_readmore' => true]
                    ],
                    [
                        'name'    => 'enable_Bicon',
                        'type'    => 'switch',
                        'label'   => 'Button Icon',
                        'value'   => false,
                        'depends' => ['show_readmore' => true]
                    ],
                    [
                        'name'    => 'show_icon',
                        'type'    => 'media',
                        'filters' => 'icon',
                        'help'    => 'Icon will visible left to the title',
                        'depends' => [
                            'enable_Bicon' => true
                        ]
                    ],
                    [
                        'name'       => 'icon_alignment',
                        'type'       => 'choose',
                        'label'      => 'Icon Placement',
                        'value'      => 'left',
                        'responsive' => false,
                        'options'    => [
                            'left'  => ['label' => 'Left', 'icon' => 'qxuicon-align-left'],
                            'right' => ['label' => 'Right', 'icon' => 'qxuicon-align-right']
                        ],
                        'depends'    => [
                            'enable_Bicon' => true
                        ]
                    ],
                    [
                        'name'  => 'show_date',
                        'type'  => 'switch',
                        'label' => 'Show Date',
                        'help'  => 'Display article creation date',
                        'value' => true
                    ],

                    [
                        'name'    => 'date_format',
                        'type'    => 'text',
                        'label'   => 'Date format',
                        'help'    => 'Date format for article date',
                        'value'   => 'd, M Y',
                        'depends' => ['show_date' => true]
                    ],
                    [
                        'name'  => 'show_meta_icon',
                        'type'  => 'switch',
                        'label' => 'Show Meta Icons',
                        'help'  => 'Icons for metadata such as - date, category, user etc',
                        'value' => true
                    ],

                    [
                        'name'  => 'show_category',
                        'type'  => 'switch',
                        'label' => 'Show Category Name',
                        'value' => false
                    ],

                    [
                        'name'  => 'show_author',
                        'type'  => 'switch',
                        'value' => false
                    ],
                ]
            ]
        ],
        'styles'  => [
            [
                'name'   => 'articles_common',
                'label'  => 'Common',
                'type'   => 'fields-group',
                'status' => 'open',
                'schema' => [
                    [
                        'name'       => 'nalignment',
                        'type'       => 'choose',
                        'label'      => 'Text Alignment',
                        'responsive' => true,
                        'options'    => [
                            'left'    => ['label' => 'Left', 'icon' => 'qxuicon-align-left'],
                            'center'  => ['label' => 'Center', 'icon' => 'qxuicon-align-center'],
                            'right'   => ['label' => 'Right', 'icon' => 'qxuicon-align-right'],
                            'justify' => ['label' => 'Justify', 'icon' => 'qxuicon-align-justify'],
                        ]
                    ],
                    [
                        'name'        => 'image_size',
                        'type'        => 'slider',
                        'max'         => 1000,
                        'units'       => ['px', '%'],
                        'defaultUnit' => '%',
                        'value'       => [
                            'desktop' => '250',
                            'tablet'  => '250',
                            'phone'   => '250'
                        ]
                    ],
                    [
                        'name' => 'common_styles',
                        'type' => 'divider'
                    ],
                    [
                        'name'  => 'bg_margin',
                        'type'  => 'dimensions',
                        'label' => 'Margin'
                    ],

                    [
                        'name'  => 'bg_padding',
                        'type'  => 'dimensions',
                        'label' => 'Padding',
                        'value' => [
                            'desktop' => [
                                'top'    => '15',
                                'bottom' => '15',
                                'left'   => '15',
                                'right'  => '15'
                            ],
                            'tablet'  => [
                                'top'    => '15',
                                'bottom' => '15',
                                'left'   => '15',
                                'right'  => '15'
                            ],
                            'phone'   => [
                                'top'    => '15',
                                'bottom' => '15',
                                'left'   => '15',
                                'right'  => '15'
                            ]
                        ]
                    ],
                    [
                        'name'  => 'bg_color',
                        'type'  => 'color',
                        'label' => 'Background Color'
                    ],
                    [
                        'name'    => 'item_border',
                        'type'    => 'border',
                        'label'   => 'Item Border',
                        'popover' => true
                    ],
                    [
                        'name' => 'contentBodyPadding_styles',
                        'label' => 'Padding (Content Body)',
                        'type' => 'divider'
                    ],
                    [
                        'name'  => 'contentBody_padding',
                        'type'  => 'dimensions',
                        'label' => 'Padding',
                        'value' => [
                            'desktop' => [
                                'top'    => '15',
                                'bottom' => '15',
                                'left'   => '15',
                                'right'  => '15'
                            ],
                            'tablet'  => [
                                'top'    => '15',
                                'bottom' => '15',
                                'left'   => '15',
                                'right'  => '15'
                            ],
                            'phone'   => [
                                'top'    => '15',
                                'bottom' => '15',
                                'left'   => '15',
                                'right'  => '15'
                            ]
                        ]
                    ],                                        
                ]
            ],
            [
                'name'   => 'articles_options',
                'label'  => 'Title',
                'type'   => 'fields-group',
                'schema' => [
                    [
                        'name' => 'title_styles',
                        'type' => 'divider'
                    ],
                    [
                        'name'  => 'title_color',
                        'type'  => 'color',
                        'label' => 'Text Color'
                    ],
                    [
                        'name'  => 'title_hvcolor',
                        'type'  => 'color',
                        'label' => 'Text Hover Color'
                    ],
                    [
                        'name'  => 'title_margin',
                        'type'  => 'dimensions',
                        'label' => 'Margin'
                    ],
                    [
                        'name'    => 'title_font',
                        'type'    => 'typography',
                        'label'   => 'Typography',
                        'popover' => true
                    ],
                ]
            ],
            [
                'name'   => 'articles_meta_options',
                'label'  => 'Meta',
                'type'   => 'fields-group',
                'schema' => [
                    [
                        'name' => 'meta_styles',
                        'type' => 'divider'
                    ],
                    [
                        'name'  => 'meta_color',
                        'type'  => 'color',
                        'label' => 'Text Color'
                    ],
                    [
                        'name'  => 'metaIcon_color',
                        'type'  => 'color',
                        'label' => 'Icon Color'
                    ],
                    [
                        'name'        => 'metaIcon_size',
                        'type'        => 'slider',
                        'label'       => 'Icon Size',
                        'units'       => 'px',
                        'responsive'  => false,
                        'defaultUnit' => 'px'
                    ],
                    [
                        'name'        => 'meta_spacing',
                        'type'        => 'slider',
                        'label'       => 'Spacing',
                        'units'       => 'px',
                        'responsive'  => false,
                        'defaultUnit' => 'px'
                    ],
                    [
                        'name'  => 'meta_margin',
                        'type'  => 'dimensions',
                        'label' => 'Margin',
                    ],

                    [
                        'name'    => 'meta_font',
                        'type'    => 'typography',
                        'label'   => 'Typography',
                        'popover' => true
                    ],
                ]
            ],
            [
                'name'   => 'articles_body_options',
                'label'  => 'Content',
                'type'   => 'fields-group',
                'schema' => [
                    [
                        'name' => 'item_intro_styles',
                        'type' => 'divider'
                    ],
                    [
                        'name'  => 'introtext_color',
                        'type'  => 'color',
                        'label' => 'Text Color',
                    ],

                    [
                        'name'  => 'introtext_margin',
                        'type'  => 'dimensions',
                        'label' => 'Margin',
                    ],
                    [
                        'name'    => 'introtext_font',
                        'type'    => 'typography',
                        'label'   => 'Typography',
                        'popover' => true
                    ],
                ]
            ],
            [
                'name'   => 'articles_button_options',
                'label'  => 'Button',
                'type'   => 'fields-group',
                'schema' => [
                    [
                        'name'  => 'button_styles',
                        'type'  => 'divider',
                        'label' => 'Readmore Button Style'
                    ],
                    [
                        'name'  => 'readmore_color',
                        'type'  => 'color',
                        'label' => 'Button Color',
                    ],
                    [
                        'name'  => 'readmoreHover_color',
                        'type'  => 'color',
                        'label' => 'Text Hover Color',
                    ],
                    [
                        'name'  => 'iconHover_color',
                        'type'  => 'color',
                        'label' => 'Icon Hover Color',
                    ],
                    [
                        'name'       => 'icon_spacing',
                        'label'      => 'Icon Spacing',
                        'type'       => 'slider',
                        'unit'       => 'px',
                        'value'       => '',
                        'responsive' => false,
                        'max'        => 100,
                    ],
                    [
                        'name'    => 'readmore_border',
                        'type'    => 'border',
                        'label'   => 'Border',
                        'popover' => true
                    ],
                    [
                        'name'  => 'readmore_padding',
                        'type'  => 'dimensions',
                        'label' => 'Padding'
                    ],
                    [
                        'name'    => 'readmore_font',
                        'type'    => 'typography',
                        'label'   => 'Typography',
                        'popover' => true
                    ],
                    [
                        'name'    => 'readmore_bg',
                        'type'    => 'background',
                        'label'   => 'Background Color',
                        'popover' => true
                    ],
                ],
            ]
        ]
    ],

];
