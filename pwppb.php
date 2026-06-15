<?php

declare(strict_types = 1);

// phpcs:disable PSR1.Files.SideEffects
require_once 'pwppb.civix.php';
// phpcs:enable

use CRM_Pwppb_ExtensionUtil as E;

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 */
function pwppb_civicrm_buildForm($formName, &$form) {
  if (CIVICRM_UF !== 'WordPress') {return;}
  
  $methodName = "buildForm_{$formName}";
  if (is_callable("CRM_Pwppb_Util_Form::$methodName")) {
    CRM_Pwppb_Util_Form::$methodName($form);
  }
}

/**
 * Implements hook_civicrm_validateForm().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_validateForm
 */
function pwppb_civicrm_validateForm($formName, &$fields, &$files, &$form, &$errors) {
  if (CIVICRM_UF !== 'WordPress') {return;}

  $methodName = "validateForm_{$formName}";
  if (is_callable("CRM_Pwppb_Util_Form::$methodName")) {
    CRM_Pwppb_Util_Form::$methodName($fields, $files, $form, $errors);
  }
}

/**
 * Implements hook_civicrm_postProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postProcess
 */
function pwppb_civicrm_postProcess($formName, CRM_Core_Form &$form) {
  if (CIVICRM_UF !== 'WordPress') {return;}

  $methodName = "postProcess_{$formName}";
  if (is_callable("CRM_Pwppb_Util_Form::$methodName")) {
    CRM_Pwppb_Util_Form::$methodName($formName, $form);
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function pwppb_civicrm_config(\CRM_Core_Config $config): void {
  if (CIVICRM_UF !== 'WordPress') {return;}
  _pwppb_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function pwppb_civicrm_install(): void {
  if (CIVICRM_UF !== 'WordPress') {return;}
  _pwppb_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function pwppb_civicrm_enable(): void {
  if (CIVICRM_UF !== 'WordPress') {return;}
  _pwppb_civix_civicrm_enable();
}
