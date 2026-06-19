<?php

use CRM_Pwppb_ExtensionUtil as E;

/**
 * Settings-related utility methods.
 *
 */
class CRM_Pwppb_Util_Form {

  public static function hasValidFields(CRM_UF_Form_Group $form): bool {
    // Form must have a primary email address field.
    $ret = FALSE;
    $gid = $form->getVar('_id');
    $ufFieldCount = \Civi\Api4\UFField::get(FALSE)
      ->addWhere('uf_group_id', '=', $gid)
      ->addWhere('field_name', '=', 'email')
      ->addWhere('is_active', '=', TRUE)
      ->addWhere('location_type_id', 'IS NULL')
      ->execute()
      ->count();
    return (bool) $ufFieldCount;
    foreach ($ufFieldGet as $ufField) {
      if ($ufField['field_name'] == 'email-Primary') {
        $ret = TRUE;
        break;
      }
    }
    return $ret;
  }

  public static function isPwppb(CRM_Core_Form $form): bool {
    switch (get_class($form)) {
      case 'CRM_UF_Form_Group':
        $idKey = '_id';
        break;
      case 'CRM_Profile_Form_Edit':
        if (CRM_Core_Session::getLoggedInContactID()) {
          // This form will NOT get Pwppb treatment, at any point in its workflow,
          // if the user is already logged in (because Pwppb treatment consists
          // of creating a user, and that's nonsensical for logged-in users.
          return FALSE;
        }
        $idKey = '_gid';
        break;
      default:
        return FALSE;
    }
    $gid = $form->getVar($idKey);
    $settings = CRM_Pwppb_Util_Setting::getUFGroupSettings($gid);
    $ret = ($settings['is_pwppb'] ?? FALSE);
    return $ret;
  }
  
  public static function buildForm_CRM_Profile_Form_Edit (CRM_Core_Form &$form) {
    if (CRM_Pwppb_Util_Form::isPwppb($form)) {
      // if profile is_pwppb:
      // make email-Primary required.
      $emailElementName = 'email-Primary';
      $emailElement = $form->getElement($emailElementName);
      $error = $form->addRule($emailElementName, E::ts('%1 is a required field.', [1 => $emailElement->_label]), 'required');

      // inject 'username' field.
      $form->add('text', 'pwppb_username', 'Username', ['size' => 30], TRUE);
      // Assign bhfe fields to the template, so our new field has a place to live.
      $tpl = CRM_Core_Smarty::singleton();
      $bhfe = $tpl->getTemplateVars('beginHookFormElements');
      if (!$bhfe) {
        $bhfe = array();
      }
      $bhfe[] = 'pwppb_username';
      $form->assign('beginHookFormElements', $bhfe);
      CRM_Core_Resources::singleton()->addScriptFile('com.joineryhq.pwppb', 'js/CRM_Profile_Form_Edit.js');
    }
  }

  public static function buildForm_CRM_UF_Form_Group (CRM_Core_Form &$form) {
    // Create new fields.
    $form->addElement('checkbox', 'is_pwppb', E::ts('Use WP Profile Builder to Create WordPress User'));
    $wpRoles = wp_roles()->roles;
    $wpRoleOptions = CRM_Utils_Array::collect('name', $wpRoles);
    $form->addSelect('pwppb_role', [
      'options' => $wpRoleOptions, 
      'placeholder' => '- '. E::ts('Select role') . ' -', 
      // wppb only supports a single role.
      'multiple' => FALSE,
      'label' => E::ts('WP role for new user'),
    ]);
    $form->add('textarea', 'pwppb_msg', E::ts('Status message after WP user creation'));
    
    // Assign bhfe fields to the template, so our new field has a place to live.
    $injectedFields = [
      'is_pwppb',
      'pwppb_role',
      'pwppb_msg',
    ];
    $tpl = CRM_Core_Smarty::singleton();
    $bhfe = $tpl->getTemplateVars('beginHookFormElements');
    if (!$bhfe) {
      $bhfe = array();
    }
    $bhfe = array_merge($bhfe, $injectedFields);
    $form->assign('beginHookFormElements', $bhfe);

    // Add vars to js
    $jsVars = [
      'injectedFields' => $injectedFields,
    ];
    CRM_Core_Resources::singleton()->addVars('pwppb', $jsVars);

    // Add javascript that will relocate our field to a sensible place in the form.
    CRM_Core_Resources::singleton()->addScriptFile('com.joineryhq.pwppb', 'js/CRM_UF_Form_Group.js');

    // Set defaults so our field has the right value.
    $gid = $form->getVar('_id');
    if ($gid) {
      $settings = CRM_Pwppb_Util_Setting::getUFGroupSettings($gid);
      $defaults = array(
        'is_pwppb' => $settings['is_pwppb'],
        'pwppb_role' => $settings['pwppb_role'],
      );
      $form->setDefaults($defaults);
    }
  }

  public static function validateForm_CRM_Profile_Form_Edit(&$fields, &$files, &$form, &$errors) {
    if (CRM_Pwppb_Util_Form::isPwppb($form)) {
      // validate username per wp requirements
      if (username_exists($fields['pwppb_username'])) {
        $errors['pwppb_username'] = E::ts('The provided username is not available. Please try a different username.');
      }
      elseif (!validate_username($fields['pwppb_username'])) {
        $errors['pwppb_username'] = E::ts('The provided username is not suitable. Please try a username containing only letters, numbers, and underscores.');
      }
      if (email_exists($fields['email-Primary'])) {
        $errors['email-Primary'] = E::ts('The provided email address is not available for use. Please try a different email address.');
      }
    }
  }

  public static function postProcess_CRM_UF_Form_Group($formName, CRM_Core_Form &$form) {
    $gid = $form->getVar('_id');
    // Get existing settings and add in our is_pwppb value. (Because
    // saveAllUFGRoupSettings() assumes we're passing all setting values).
    $settings = CRM_Pwppb_Util_Setting::getUFGroupSettings($gid);
    $settings['is_pwppb'] = $form->_submitValues['is_pwppb'];
    $settings['pwppb_role'] = $form->_submitValues['pwppb_role'];
    $settings['pwppb_msg'] = $form->_submitValues['pwppb_msg'];
    CRM_Pwppb_Util_Setting::saveAllUFGRoupSettings($gid, $settings);

    if (CRM_Pwppb_Util_Form::isPwppb($form) && !CRM_Pwppb_Util_Form::hasValidFields($form)) {
      // we're saving the form config. If is_pwppb is set, ensure email-Primary field is provided,
      // or else alert to user.
      $messageTitle = E::ts('Missing Primary Email field');
      $message = E::ts('This profile is set to "Use WP Profile Builder to Create WordPress User", but it does not have the field "Email (Primary)", which is required for this functionality.');
      CRM_Core_Session::setStatus($message, $messageTitle);
    }
  }

  public static function postProcess_CRM_Profile_Form_Edit($formName, CRM_Core_Form &$form) {
    if (CRM_Pwppb_Util_Form::isPwppb($form)) {
      // Get submitted form values.
      $submittedValues = $form->getSubmittedValues();
      
      // Create a wppb user
      $userName = $submittedValues['pwppb_username'];
      $userEmail = $submittedValues['email-Primary'];
      $wppbMeta = [
        'user_login' => $userName,
        'first_name' => ($submittedValues['first_name'] ?? NULL),
        'last_name' => ($submittedValues['last_name'] ?? NULL),
        'user_pass' => wp_hash_password(bin2hex(random_bytes(12))),
        'user_email' => $userEmail,
        'form_name' => 'unspecified',
      ];
      
      // Get settings.
      $formSettings = CRM_Pwppb_Util_Setting::getUFGroupSettings($form->getVar('_gid'));
      
      // Determine a wp user role (from settings), if any.
      $wpRole = ($formSettings['pwppb_role'] ?? NULL);
      if ($wpRole) {
        $wppbMeta['role'] = $wpRole;
      }

      // Trigger account creation via wppb
      wppb_signup_user($userName, $userEmail, NULL, $wppbMeta);

      // Determine a user message (from settings), if any.
      // fixme: I believe this setting is not needed, as 'redirect on submit' can 
      // take the user to a page that says anything we want.
      $userMessage = ($formSettings['pwppb_msg'] ?? NULL);
      if ($userMessage) {
        CRM_Core_Session::setStatus($userMessage, E::ts('Needs confirmation'));
      }
      
    }
  }
}