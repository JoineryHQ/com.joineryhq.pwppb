CRM.$(function($) {
  var isCmsUserChange = function isCmsUserChange() {
    var settingTr = $('tr.crm-uf-advancesetting-form-block-is_pwppb');
    if ($('#is_cms_user').val() == 0) {
      settingTr.show();
    }
    else {
      // Hide and clear our setting.
      settingTr.hide();
      $('input#is_pwppb').prop('checked', false);
    }
  }

  var isPwppbChange = function isPwppbChange() {
    var settingTr = $('tr.crm-uf-advancesetting-form-block-pwppb_role');
    if ($('#is_pwppb').prop('checked')) {
      settingTr.show();
    }
    else {
      // Hide the role setting.
      settingTr.hide();
    }
  }

  // Add id attribute to bhfe table, so it's easy to reference later.
  $('input#is_pwppb').closest('table').addClass('pwppb-bhfe-table');
  
  var fieldName, fieldTrClass;
  CRM.vars.pwppb.injectedFields.reverse();
  for (var i in CRM.vars.pwppb.injectedFields) {
    fieldName = CRM.vars.pwppb.injectedFields[i];
    fieldTrClass = 'crm-uf-advancesetting-form-block-' + fieldName;
    console.log(i, fieldName, fieldTrClass);
    $('#' + fieldName).closest('tr').addClass(fieldTrClass);
    $('div.crm-uf-advancesetting-form-block table tbody #is_cms_user').closest('tr').after($('tr.' + fieldTrClass));
  }


  // Remove the bhfe table, but only if it's empty.
  if ($('table.pwppb-bhfe-table tr').length == 0) {
    $('table.pwppb-bhfe-table').remove();
  }

  // define on-change handler for 'create cms user' setting field.
  $('#is_cms_user').change(isCmsUserChange);
  // Go ahead and fire that change handler now.
  isCmsUserChange();

  // define on-change handler for 'is_pwppb' setting field.
  $('#is_pwppb').change(isPwppbChange);
  // Go ahead and fire that change handler now.
  isPwppbChange();
});