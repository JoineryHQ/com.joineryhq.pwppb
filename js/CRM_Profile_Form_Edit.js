(function($, ts) {
  
  // Add id attribute to bhfe table, so it's easy to reference later.
  $('input#pwppb_username').closest('table').addClass('pwppb-bhfe-table');
  $('input#pwppb_username').closest('tr').addClass('crm-uf-advancesetting-form-block-pwppb_username');

  $('div.crm-uf-advancesetting-form-block table tbody #is_cms_user').prepend($('input#pwppb_username').closest('tr'));
  $('div#crm-profile-block').prepend('<div id="editrow-pwppb-username" class="crm-section editrow_pwppb_username-section form-item"><div class="label"></div><div class="edit-value content"></div><div class="clear">');
  $('#editrow-pwppb-username div.label').append($('input#pwppb_username').closest('tr').find('label'));
  $('#editrow-pwppb-username div.edit-value.content').append($('input#pwppb_username').closest('td').find('*'));
  
/*
 * 
 *div id="editrow-first_name" class="crm-section editrow_first_name-section form-item"><div class="label"><label for="first_name">  First Name
     <span class="crm-marker" title="This field is required.">*</span>

<input maxlength="64" size="30" name="first_name" type="text" id="first_name" class="big crm-form-text required"></div></div></div>
 */
  
  // Remove the bhfe table, but only if it's empty.
  if ($('table.pwppb-bhfe-table tr').length == 0) {
    $('table.pwppb-bhfe-table').remove();
  }

})(CRM.$, CRM.ts('com.joineryhq.pwppb'));

