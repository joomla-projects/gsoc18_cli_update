/**
 *
 * @Version       $Id: edit.php 1316 2014-01-22 17:34:52Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.5
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2014-01-22 17:34:52 +0000 (Wed, 22 Jan 2014) $
 *
 */
 
var $IT = jQuery.noConflict();
$IT(document).ready(function() {
   var sel_view = document.getElementById('view').value;
   // alert('Ready function View:' + sel_view);    
   switch(sel_view) {
      case 'feitissues':
         break;
      case 'form':
      case 'itissues':
         $IT('#itCustomFieldsValidationResults').hide();
         initCustomFieldsEditor();
         break;
      case 'customfield':    
         // Default settings.
         setDefaults();
         $IT('defaultval_field').hide();

         // Logic to determine whether an existing record or not to handle
         // the displayed fields.
         var sel_id = document.getElementById('id').value;
         // alert('Record ID = ' + sel_id);
         var sel_value = document.getElementById('value').value;
         // alert('Existing Values: ' + sel_value);
         var newField = 0;
         var values = [];
         if (sel_id) {
            // alert('This is an existing record');
            newField = 0;
            values = $IT.parseJSON(sel_value);
         } else {
            // alert('This is a new record.');
            newField = 1;
            values[0] = " ";
            // Set form type to default.
            document.getElementById('jformtype').value = '0';
         }

         var sel_type = document.getElementById('jformtype').value;
         // alert('Type: ' + sel_type);
         switch (sel_type) {
            case 'header':
               $IT('defaultval_field').hide();
               $IT('tooltip_field').hide();
               $IT('req_field').hide();
               break;
            case 'date':
               $IT('defaultval_field').hide();
               $IT('defaultval_field_date').show();
               $IT('valid_field').hide();
               // $('tooltip_field').hide();
               break;
            case 'textfield':
               $IT('defaultval_field_text').show();
               break;
            case 'textarea':
               $IT('defaultval_field_textarea').show();
               $IT('textarea_fields').show();
               break;
            case 'radio':
            case 'multipleCheckbox':
               $IT('shownull_field').hide();
               $IT('req_field').show();
               $('defaultval_field_other').show();
               renderExtraFields(sel_type, values, newField);
               break;
            case 'select':
            case 'multipleSelect':
               $IT('shownull_field').show();
               $IT('req_field').show();
               $('defaultval_field_other').show();
               renderExtraFields(sel_type, values, newField);
               break;
            case 'link':
               renderExtraFields(sel_type, values, newField);
               break;
            default:
               // alert(sel_value + ' not currently supported.');
               $IT('defaultval_field').show();
               break;
         }
         break;
      default:
         alert('View ' + sel_view + ' not supported.');
         break;
   }
});


// Custom fields validation
function validateCustomFields() {
  $IT('#itCustomFieldsValidationResults').hide();
  $IT('.itRequired').removeClass('itInvalid');
  $IT('#tabCustomFields a').removeClass('itInvalid');
  var response = {};
  var efResults = [];
  response.isValid = true;
  response.errorFields = [];
  $IT('.required').each(function() {
    var id = $IT(this).attr('id');
    var id2 =  id.substr(id.length - 2 );
    if (id.substring(0,8) == 'ITCustom' && id2 == 'id' ) {
      var ff =id.substring(0, id.indexOf('-'));
      var value;
      var setval = 0;
      $IT("textarea[id='" + ff + "']").each(function() {
        // var itag = $IT(this).prop("tagName");
	     if ($IT(this).hasClass('itCustomFieldEditor')) {
   	    if ( typeof tinymce != 'undefined') {
	         value = tinyMCE.get(id).getContent()
   	    } else {
            value = $IT(this).val();  // Catch all in case editor not shown!
          }
        } else {
          value = $IT(this).val();
        }
        if ($IT.trim(value) !== '' ) {
          setval = 1;
        }
      });

      $IT("select[id='" + ff + "']").each(function() {
        value = $IT(this).val();
        if ( $IT.trim(value) !== '') {
          setval = 1;
        }
      });

      $IT("input[name='" + ff + "']").each(function() {
        // var itag = $IT(this).prop("tagName");
        var type = $IT(this).attr('type');
        if ( type == 'radio') {
          setval = 1;
        }
        if ( type == 'multipleCheckbox') {
          setval = 1;
        }
        if ( type == 'text' ) {
          value = $IT(this).val();
          if ($IT.trim(value) !== '' ) {
            setval = 1;
          }
        }
      });

      if (setval == 0) {
        $IT('#itCustomFieldsValidationResults').show();
        $IT(this).addClass('itInvalid');
	     response.isValid = false;
	     var label = $IT("label[for='" + ff.substring(2) + "']").text();
	     response.errorFields.push(label);
	   }
    }
  });

  $IT.each(response.errorFields, function(key, value) {
    efResults.push('<li>' + value + '</li>');
  });
  if (response.isValid === false) {
	 $IT('#itCustomFieldsMissing').html(efResults);
	 $IT('#itCustomFieldsValidationResults').css('display','block');
	 $IT('#tabCustomFields a').addClass('itInvalid');
  }
  return response.isValid;
}

/*
function syncCustomFieldsEditor() {
  $IT('.itCsutomFieldEditor').each(function() {
    if ( typeof tinymce != 'undefined') {
      var content= tinyMCE.get(id).getContent();
      if (content == '<br>' || content == '<br />') {
        editor.setContent('');
      }
      editor.saveContent();
    }
  });
}
*/

function displayFormFields(thisForm) {
  var sel_proj = document.getElementById('jformrelated_project_id').value;
  // alert('Selected project id is :' + sel_proj);
  if ( sel_proj == '' ) sel_proj = 0;
  $IT('#ITCustomDiv').hide();
  $IT('#itCustomFieldsValidationResults').hide();
  var url = IT_BasePath + 'index.php?option=com_issuetracker&task=itissues.customFields&pid=' + sel_proj + '&id=' + $IT('input[name=id]').val();
  // alert('Generated url '+url);
  $IT('#customFieldsContainer').fadeOut('slow', function() {
    $IT.ajax({
      url : url,
      type : 'get',
      success : function(response) {
        if ( response == null || response == '') {
          if( $IT('#ITCustomDiv').css("display") != 'none') {
            $IT('#ITCustomDiv').hide();
          }
          $IT('#ITCustomDiv').empty();
        } else {
          $IT('#customFieldsContainer').html(response);
          initCustomFieldsEditor();
          $IT('img.calendar').each(function() {
            var inputFieldID = $IT(this).prev().attr('id');
            var imgFieldID = $IT(this).attr('id');
            Calendar.setup({
              inputField : inputFieldID,
              ifFormat : "%Y-%m-%d",
              button : imgFieldID,
              align : "Tl",
              singleClick : true
            });
          });
          $IT('#customFieldsContainer').fadeIn('slow');
          $IT('#ITCustomDiv').show();
        }
      }
    });
  });
}

// Following does like IT in (as) the selector.  TODO
function setDefaults(){
  // alert("Set defaults");
  $('textarea_fields').hide();
  $('shownull_field').hide();
  $('displayfe_field').hide();
  $('valid_field').hide();
  $('tooltip_field').show();
  $('defaultval_field_date').hide();
  $('defaultval_field_text').hide();
  $('req_field').show();
  $('defaultval_field_textarea').hide();
  $('defaultval_field_other').hide();
  // Following added for J2.5
  // document.getElementById('jformtype').value = '0';
}

function setDisplay(thisForm) {
   var sel_type = document.getElementById('jformtype').value;
   setDefaults();
   // alert('Type: ' + sel_type);
   var sel_id = document.getElementById('id').value;
   var newField = 0;
   var values = [];
   if (sel_id) {
      // alert('Existing record');
      newField = 0;
      var sel_values = document.getElementById('value').value;
      values = $IT.parseJSON(sel_values);
   } else {
      // alert('New record');
      newField = 1;
      values[0] = " ";
   }

   // Following does not like adding IT to the selector. TODO
   switch (sel_type) {
      case 'header':
         $('defaultval_field').hide();
         $('tooltip_field').hide();
         $('req_field').hide();
         break;
      case 'date':
         $('defaultval_field').hide();
         $('defaultval_field').hide();
         $('defaultval_field_date').show();
         $('valid_field').hide();
         // $IT('tooltip_field').hide();
         break;
      case 'textfield':
         $('defaultval_field').hide();
         $('defaultval_field_text').show();
         break;
      case 'textarea':
         $('defaultval_field').hide();
         $('defaultval_field_textarea').show();
         $('textarea_fields').show();
         break;
      case 'radio':
      case 'multipleCheckbox':
         $('defaultval_field').hide();
         $('shownull_field').hide();
         $('req_field').show();
         $('defaultval_field_other').show();
         renderExtraFields(sel_type, values, newField);
         break;
      case 'select':
      case 'multipleSelect':
         $('defaultval_field').hide();
         $('shownull_field').show();
         $('req_field').show();
         $('defaultval_field_other').show();
         renderExtraFields(sel_type, values, newField);
         break;
      default:
         alert(sel_type + ' not supported.');
         $('defaultval_field').show();
         break;
   }
}

// Extra fields
function addOption() {
   var div = $IT('<div/>').appendTo($IT('#select_dd_options'));
   var input = $IT('<input/>', {
      name : 'option_name[]',
      type : 'text'
   }).appendTo(div);
   var input = $IT('<input/>', {
      name : 'option_value[]',
      type : 'hidden'
   }).appendTo(div);
   var input = $IT('<input/>', {
      value : IT_JLanguage[0],
      type : 'button'
   }).appendTo(div);
   input.click(function() {
      $IT(this).parent().remove();
   })
}

function renderExtraFields(fieldType, fieldValues, isNewField) {
   var target = $IT('#CustomFieldsTypesDiv');
   var currentType = document.getElementById('jformtype').value;
      
   // alert('In Rendering routine: ' + fieldType);
   // Clear out any existing entries.
   $IT('#CustomFieldsTypesDiv').empty();

   switch (fieldType) {
      case 'select':
      case 'multipleSelect':
      case 'multipleCheckbox':
      case 'radio':
         // alert('Type ' + fieldType + ' ' + currentType + ' ' + isNewField);
         var input = $IT('<input/>', {
            value : IT_JLanguage[2],
            type : 'button'
         }).appendTo(target);
         input.click(function() {
            addOption();
         });
         var br = $IT('<br/><br/>').appendTo(target);
         var div = $IT('<div/>', {
            id : 'select_dd_options'
         }).appendTo(target);
         if (isNewField || currentType != fieldType) {
            addOption();
         } else {
            $IT.each(fieldValues, function(index, value) {
               var div = $IT('<div/>').appendTo($IT('#select_dd_options'));
               var input = $IT('<input/>', {
                  name : 'option_name[]',
                  type : 'text',
                  value : value.name
               }).appendTo(div);
               var input = $IT('<input/>', {
                  name : 'option_value[]',
                  type : 'hidden',
                  value : value.value
               }).appendTo(div);
               var input = $IT('<input/>', {
                  value : IT_JLanguage[0],
                  type : 'button'
               }).appendTo(div);
               input.click(function() {
                  $IT(this).parent().remove();
               })
           });
         }
         break;
      case 'date':
         // We are not using this!
         var id = 'DateField' + $IT.now();
         var input = $IT('<input/>', {
            name : 'option_value[]',
            type : 'text',
            id : id,
            value : fieldValues[0].value,
            readonly : 'readonly'
         }).appendTo(target);
         var img = $IT('<img/>', {
            id : id + '_img',
            'class' : 'calendar',
            src : 'templates/system/images/calendar.png',
            alt : IT_JLanguage[3]
         }).appendTo(target);
         Calendar.setup({
            inputField : id,
            ifFormat : "%Y-%m-%d",
            button : id + '_img',
            align : "Tl",
            singleClick : true
         });
         var notice = $IT('<span/>').html('(' + IT_JLanguage[1] + ')').appendTo(target);
         break;
      case 'header':
         target.html(' - ');
         var input = $IT('<input/>', {
            name : 'option_value[]',
            type : 'hidden'
         }).appendTo(target);
         if (!isNewField && currentType == fieldType) {
            input.val(fieldValues[0].value);
         }
         break;

      default:
         var title = $IT('<span/>', {
            'class' : 'notice'
         }).html(IT_JLanguage[4]).appendTo(target);
         break;
   }
}

function initCustomFieldsEditor() {
   $IT('.itCustomFieldEditor').each(function() {
      var id = $IT(this).attr('id');
      if ( typeof tinymce != 'undefined') {
         if ( tinyMCE.majorVersion == '4') {
            var oInstance = tinyMCE.get(id);
            if (oInstance) {
              if (oInstance.isHidden()) tinyMCE.remove(oInstance);
				  tinyMCE.execCommand('mceRemoveControl', true, id);
              // tinyMCE.execCommand('mceRemoveEditor', false, id);
            } else {
               tinyMCE.execCommand('mceAddEditor', false, id);
            }
         } else {   
            if (tinyMCE.get(id)) {
                tinymce.EditorManager.remove(tinyMCE.get(id));
                tinyMCE.execCommand('mceAddControl', false, id);
            } else {
               tinyMCE.execCommand('mceAddControl', false, id);
            }
         }
      }
  });
}