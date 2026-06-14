<?php

use CRM_Pwppb_ExtensionUtil as E;

return [
  // Option Group: Pwppb Device Status
  [
    'name' => 'OptionGroup_pwppb_settings',
    'entity' => 'OptionGroup',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'OptionGroup_pwppb_settings',
        'title' => E::ts('Pwppb Profile Settings'),
        'description' => E::ts('Pwppb: Saved settings per-profile'),
        'is_reserved' => TRUE,
        'is_locked' => TRUE,
        'is_active' => TRUE,
        'data_type' => 'String',
        'cleanup' => 'always',
        'update' => 'always',
      ],
    ],
  ],  
];
