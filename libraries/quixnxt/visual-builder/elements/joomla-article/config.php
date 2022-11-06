<?php
include_once(__DIR__ . '/helper.php');

$articles = array_reduce(QuixJoomlaArticleElement::getListJoomlaArticle(), static function ($carry, $article) {
    $carry[$article->id] = $article->title;
    return $carry;
}, [ ]);

return [
  'slug' => 'joomla-article',
  'name' => 'Joomla Single Article',
  'groups' => ['joomla', 'pro'],
  'helpId' => '5d1fec1d2c7d3a5cd38ec1bf',
  'form' => [
    'general' => [
      [
        'name' => 'article_core',
        'label' => 'Article',
        'type' => 'fields-group',
        'status' => 'open',
        'schema' => [
          [
            'name' => 'article_id',
            'type' => 'select',
            'label' => 'Select Article',
            'options' => $articles
          ],
          [
            'name' => 'show_title',
            'type' => 'switch',
            'label' => 'Show Title',
            'value'=> true
          ],
          [ 'name' => 'show_content',
            'type' => 'switch',
            'value'=> true
          ],

          [ 'name' => 'content_type',
            'type' => 'select',
            'value' => 'introtext',
            'label' => 'Content Type',
            'depends'=>['show_content'=> true],
            'options' => [
              'full_article' => "Full Article",
              'fulltext' => "Full Text",
              'introtext' => "Introtext",
            ]
          ],
          [ 'name' => 'show_image',
            'type' => 'switch',
            'label' => 'Show Image',
            'value'=> true
          ],
        ]
      ],
      [
        'name' => 'article_options',
        'label' => 'Options',
        'type' => 'fields-group',
        'schema' => [
          [ 'name' => 'link_titles',
            'type' => 'switch',
            'label' => 'Link Titles',
            'value'=> false
          ],
          [
            'name' => 'show_meta_icon',
            'type' => 'switch',
            'label' => 'Show Meta Icons',
            'help' => 'Icons for metadata such as - date, category, user etc',
            'value' => true
          ],
          [ 'name' => 'show_category',
            'type' => 'switch',
            'label' => 'Show Category Name',
            'value'=> true
          ],

          [ 'name' => 'show_author',
            'type' => 'switch',
            'value'=> true
          ],
          [
            'name' => 'show_date',
            'type' => 'switch',
            'label' => 'Show Date',
            'help' => 'Display article creation date',
            'value'=> true
          ],
          
          [ 'name' => 'date_format',
            'type' => 'text',
            'label' => 'Date format',
            'help' => 'Date format for article date',
            'value'=> 'd, M Y',
            'depends' => [ 'show_date' => true ]
          ],

          [ 'name' => 'show_readmore',
            'type' => 'switch',
            'value'=> false,
            'label' => 'Readmore Button'
          ],
          
          [ 'name' => 'readmore_style',
            'type' => 'select',
            'value' => 'qx-btn-link',
            'options' => [
              'qx-btn-primary' => 'Primary',
              'qx-btn-secondary' => 'Secondary',
              'qx-btn-success' => 'Success',
              'qx-btn-info' => 'Info',
              'qx-btn-warning' => 'Warning',
              'qx-btn-danger' => 'Danger',
              'qx-btn-link' => 'Link',
              'qx-btn-light' => 'Light',
              'qx-btn-dark' => 'Dark',
            ],
            'depends'=>['show_readmore'=> true]
          ],
          [ 'name' => 'readmore_size',
            'type' => 'select',
            'value' => 'qx-btn-sm',
            'options' => [
              'qx-btn-lg' => 'Large',
              'qx-btn-sm' => 'Small',
              'qx-btn-md' => 'Default'
            ],
            'depends'=>['show_readmore'=> true]
          ],

          [ 'name' => 'readmore_text',
            'type' => 'text',
            'value' => 'Read More...',
            'depends'=>['show_readmore'=> true]
          ],
          [
            'name' => 'enable_Bicon',
            'type' => 'switch',
            'label' => 'Button Icon',
            'value' => false,
            'depends'=>['show_readmore'=> true]
          ],
          [
            'name' => 'show_icon',
            'type' => 'media',
            'filters' => 'icon',
            'help' => 'Icon will visible left to the title',
            'depends' => [
              'enable_Bicon' => true
            ]
          ],
          [
            'name' => 'icon_alignment',
            'type' => 'choose',
            'label' => 'Icon Placement',
            'value' => 'left',
            'responsive' => false,
            'options' => [
              'left' => ['label'=> 'Left', 'icon' => 'qxuicon-align-left'],
              'right' => ['label' => 'Right', 'icon' => 'qxuicon-align-right']
            ],
            'depends' => [
              'enable_Bicon' => true
            ]
          ]
        ]
      ],
      [
        'name' => 'article_behaviour',
        'label' => 'Layout',
        'type' => 'fields-group',
        'schema' => [
          [ 'name' => 'article_layout',
            'type' => 'select',
            'label' => 'Article Layout',
            'multiple'=> true,
            'value'=> ['art_title', 'art_meta', 'art_image', 'art_content', 'art_button'],
            'options'=> [
              'art_title'=> 'Title',
              'art_meta'=> 'Meta',
              'art_image'=> 'Image',
              'art_content'=> 'Content',
              'art_button'=> 'Button'
            ]
          ],
        ]
      ],
    ],
    'styles' => [
      [
        'name' => 'articles_common',
        'label' => 'Common',
        'type' => 'fields-group',
        'status' => 'open',
        'schema' => [
          [
            'name' => 'alignment',
            'type' => 'choose',
            'label' => 'Text Alignment',
            'responsive' => true,
            'options' => [
              'left' => ['label' => 'Left', 'icon' => 'qxuicon-align-left'],
              'center' => ['label' => 'Center', 'icon' => 'qxuicon-align-center'],
              'right' => ['label' => 'Right', 'icon' => 'qxuicon-align-right'],
              'justify' => ['label' => 'Justify', 'icon' => 'qxuicon-align-justify'],
            ]
          ],
          [
            'name' => 'image_size',
            'type' => 'slider',
            'max' => 1000,
            'units' => ['px', '%'],
            'defaultUnit' => '%',
            'value' => [
              'desktop' => '250',
              'tablet' => '250',
              'phone' => '250'
            ]
          ],          
        ]
      ],
      [
        'name' => 'articles_options',
        'label' => 'Title',
        'type' => 'fields-group',
        'schema' => [
          [ 'name' => 'title_color',
            'type' => 'color',
            'label' => 'Text Color'
          ],
          [ 'name' => 'title_margin',
            'type' => 'dimensions',
            'label' => 'Margin'
          ],

          [ 'name' => 'title_font',
            'type' => 'typography',
            'label' => 'Typography',
            'popover' => true
          ],
        ]
      ],
      [
        'name' => 'articles_meta_options',
        'label' => 'Meta',
        'type' => 'fields-group',
        'schema' => [
          [
            'name' => 'meta_color',
            'type' => 'color',
            'label' => 'Text Color'
          ],
          [
            'name' => 'metaIcon_color',
            'type' => 'color',
            'label' => 'Icon Color'
          ],
          [
            'name' => 'metaIcon_size',
            'type' => 'slider',
            'label' => 'Icon Size',
            'responsive' => false,
            'max' => 100,
            'value' => 14
          ],
          [
            'name' => 'meta_spacing',
            'type' => 'slider',
            'label' => 'Spacing',
            'responsive' => false,
            'max' => 100,
            'value' => 0
          ],
          [
            'name' => 'meta_margin',
            'type' => 'dimensions',
            'label' => 'Margin',
            'value' => [
              'desktop' => [
                'top' => '0',
                'bottom' => '10',
                'left' => '0',
                'right' => '0'
              ],
              'tablet' => [
                'top' => '0',
                'bottom' => '10',
                'left' => '0',
                'right' => '0'
              ],
              'phone' => [
                'top' => '0',
                'bottom' => '10',
                'left' => '0',
                'right' => '0'
              ]
            ]
          ],
          [
            'name' => 'meta_font',
            'type' => 'typography',
            'label' => 'Typography',
            'popover' => true
          ],
        ]
      ],
      [
        'name' => 'articles_body_options',
        'label' => 'Content',
        'type' => 'fields-group',
        'schema' => [
          [
            'name' => 'item_intro_styles',
            'type' => 'divider'
          ],
          [ 'name' => 'introtext_color',
            'type' => 'color',
            'label' => 'Text Color',
          ],

          [ 'name' => 'introtext_margin',
            'type' => 'dimensions',
            'label' => 'Margin',
          ],

          [ 'name' => 'introtext_font',
            'type' => 'typography',
            'label' => 'Typography',
            'popover' => true
          ],
        ]
      ],
      [
        'name' => 'articles_button_options',
        'label' => 'Button',
        'type' => 'fields-group',
        'schema' => [
          [
            'name' => 'item_button_styles',
            'type' => 'divider'
          ],
          [ 'name' => 'readmore_color',
            'type' => 'color',
            'label' => 'Text Color',
          ],
          [ 'name' => 'readmoreHover_color',
            'type' => 'color',
            'label' => 'Text Hover Color',
          ],
          [ 'name' => 'iconHover_color',
            'type' => 'color',
            'label' => 'Icon Hover Color',
          ],
          [
            'name' => 'icon_spacing',
            'label' => 'Icon Spacing',
            'type' => 'slider',
            'responsive' => false,
            'units' => 'px',
            'defaultUnit' => 'px',
            'max' => 250,
            'default' => 20
          ],
          [ 'name' => 'readmore_padding',
            'type' => 'dimensions',
            'label' => 'Padding',
          ],

          [ 'name' => 'readmore_font',
            'type' => 'typography',
            'label' => 'Typography',
            'popover' => true
          ],
          [ 'name' => 'readmore_bg',
            'type' => 'background',
            'label' => 'Background',
            'popover' => true
          ],
          [ 'name' => 'readmore_border',
            'type' => 'border',
            'label' => 'Border',
            'popover' => true
          ],
        ]
      ],
    ]
  ],

];
