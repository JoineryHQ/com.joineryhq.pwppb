<?php

use CRM_Pwppb_ExtensionUtil as E;
use Civi\Api4\OptionValue;

/**
 * Settings-related utility methods.
 *
 */
class CRM_Pwppb_Util_Setting {

  public static function getUFGroupSettings($ufGroupId) {
    $settingName = "ufgroup_settings_{$ufGroupId}";
    $optionValue = OptionValue::get(FALSE)
      ->addSelect('id')
      ->addSelect('value')
      ->addWhere('option_group_id:name', '=', 'OptionGroup_pwppb_settings')
      ->addWhere('name', '=', $settingName)
      ->setLimit(1)
      ->execute()
      ->first();
    $settingJson = ($optionValue['value'] ?? '{}');
    return json_decode($settingJson, TRUE);
  }

  public static function saveAllUFGRoupSettings($ufGroupId, $settings) {

    $settingName = "ufgroup_settings_{$ufGroupId}";

    $result = OptionValue::get(FALSE)
      ->addSelect('id')
      ->addWhere('option_group_id:name', '=', 'OptionGroup_pwppb_settings')
      ->addWhere('name', '=', $settingName)
      ->setLimit(1)
      ->execute()
      ->first();
    

    $save = OptionValue::save(FALSE)
      ->addRecord([
        'id' => $result['id'] ?? NULL,
        'name' => $settingName,
        'label' => 'Settings for UFGroup ' . $ufGroupId,
        'option_group_id:name' => 'OptionGroup_pwppb_settings',
        'value' => json_encode($settings + ['uf_group_id' => $ufGroupId]),
      ]);

    try {
      $save->execute();
      return TRUE;
    }
    catch (CRM_Core_Exception $e) {
      throw $e;
      return FALSE;
    }

  }

}
