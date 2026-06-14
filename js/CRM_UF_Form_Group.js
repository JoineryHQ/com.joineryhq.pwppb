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


  // Add id attribute to bhfe table, so it's easy to reference later.
  $('input#is_pwppb').closest('table').addClass('pwppb-bhfe-table');
  $('input#is_pwppb').closest('tr').addClass('crm-uf-advancesetting-form-block-is_pwppb');

  $('div.crm-uf-advancesetting-form-block table tbody #is_cms_user').closest('tr').after($('input#is_pwppb').closest('tr'));

  // Remove the bhfe table, but only if it's empty.
  if ($('table.pwppb-bhfe-table tr').length == 0) {
    $('table.pwppb-bhfe-table').remove();
  }


  // define on-change handler for 'create cms user' setting field.
  $('#is_cms_user').change(isCmsUserChange);
  // Go ahead and fire that change handler now.
  isCmsUserChange();
});