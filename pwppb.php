<?php
declare(strict_types = 1);

// phpcs:disable PSR1.Files.SideEffects
require_once 'pwppb.civix.php';
// phpcs:enable

use CRM_Pwppb_ExtensionUtil as E;

function TODO() {
  // This is sample code for creating a wppb user
  $user_name = 't'. time();
  $user_email = $user_name . '@a.a';
  $meta = [
    'user_login' => $user_name,
    'first_name' => 'test',
    'last_name' => $user_name,
    'user_pass' => wp_hash_password('i'),
    'user_email' => $user_email,
    'civicrm-phone_1' => '',
    'role' => 'provider',
    'form_name' => 'unspecified',
    'wppb_login_after_register_a2' => true,
  ];

  wppb_signup_user( $user_name, $user_email, true, $meta );
  
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 */
function pwppb_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_UF_Form_Group') {
    // Create new field.
    $form->addElement('checkbox', 'is_pwppb', E::ts('Use WP Profile Builder to Create WordPress User'));

    // Assign bhfe fields to the template, so our new field has a place to live.
    $tpl = CRM_Core_Smarty::singleton();
    $bhfe = $tpl->getTemplateVars('beginHookFormElements');
    if (!$bhfe) {
      $bhfe = array();
    }
    $bhfe[] = 'is_pwppb';
    $form->assign('beginHookFormElements', $bhfe);

    // Add javascript that will relocate our field to a sensible place in the form.
    CRM_Core_Resources::singleton()->addScriptFile('com.joineryhq.pwppb', 'js/CRM_UF_Form_Group.js');

    // Set defaults so our field has the right value.
    $gid = $form->getVar('_id');
    if ($gid) {
      $settings = CRM_Pwppb_Util_Setting::getUFGroupSettings($gid);
      $defaults = array(
        'is_pwppb' => $settings['is_pwppb'],
      );
      $form->setDefaults($defaults);
    }
  }  
}

/**
 * Implements hook_civicrm_postProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postProcess
 */
function pwppb_civicrm_postProcess($formName, &$form) {
  if ($formName == 'CRM_UF_Form_Group') {
    $gid = $form->getVar('_id');
    // Get existing settings and add in our is_pwppb value. (Because
    // saveAllUFGRoupSettings() assumes we're passing all setting values.
    $settings = CRM_Pwppb_Util_Setting::getUFGroupSettings($gid);
    $settings['is_pwppb'] = $form->_submitValues['is_pwppb'];
    CRM_Pwppb_Util_Setting::saveAllUFGRoupSettings($gid, $settings);
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function pwppb_civicrm_config(\CRM_Core_Config $config): void {
  _pwppb_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function pwppb_civicrm_install(): void {
  _pwppb_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function pwppb_civicrm_enable(): void {
  _pwppb_civix_civicrm_enable();
}
