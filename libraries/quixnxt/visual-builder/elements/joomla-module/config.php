<?php
return [
  'slug' => 'joomla-module',
  'name' => 'Joomla Module',
  'groups' => ['joomla'],
  'helpId' => '5d1ff02d04286369ad8d5f05',
  'form' => [
    'general' => [
      [ 
        'name' => 'modules_core',
        'label' => 'Module',
        'type' => 'fields-group',
        'status' => 'open',
        'schema' => [
          [ 
            'name' => 'module_id', 
            'type' => 'jmodule',  
            'label' => 'Select A Module',
          ]
        ]
      ]
    ],
  ]
];
