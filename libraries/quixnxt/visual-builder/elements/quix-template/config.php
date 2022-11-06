<?php
return [
  'slug' => 'quix-template',
  'name' => 'Quix Template',
  'groups' => ['core'],
  'helpId' => '5d1ff02d04286369ad8d5f05',
  'form' => [
    'general' => [
      [ 
        'name' => 'templates',
        'label' => 'Select Template',
        'type' => 'fields-group',
        'status' => 'open',
        'schema' => [
          [ 
            'name' => 'id',
            'type' => 'templates',
            'label' => 'Select a template',
            'value' => ''
          ]
        ]
      ]
    ],
  ]
];
