<?php
/*
 * @Version       $Id: edit_notifychk.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.7
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
defined('_JEXEC') or die('Restricted Access');

?>

<script type="text/javascript">
   function setnotifyvalue() {
      var sel_ident = document.getElementById('jformidentified_by_person_id').value;
      var url = IT_BasePath + 'index.php?option=com_issuetracker&task=itissues.setnotify&iid=' + sel_ident;
      $IT.ajax({
         url : url,
         type : 'POST',
         dataType : "json",
         success : function(response) {
            $IT('#itextTab').hide();
         }
      });
   }
   function getnotifyvalue() {
      var sel_ident = document.getElementById('jformidentified_by_person_id').value;
      var url = IT_BasePath + 'index.php?option=com_issuetracker&task=itissues.getnotify&iid=' + sel_ident;
      $IT.ajax({
         url : url,
         type : 'POST',
         dataType : "json",
         success : function(response) {
            if ( response == null || response == '') {
               $IT('#itextTab').show();
            } else {
               $IT('#itextTab').hide();
            }
         }
      });
   }
</script>

<fieldset class="adminform">
   <div id="itextTab" style="display: none;">
      <div class="admintable table">
         <div class="formelm">
            <label>
               <?php echo ""; ?>
            </label>
            <?php echo JText::_('COM_ISSUETRACKER_NOTIFY_WILL_NOT_OCCUR_MSG'); ?>
            <button type="button" onclick="setnotifyvalue()">
               <?php echo JText::_('COM_ISSUETRACKER_MODIFY_BUTTON'); ?>
            </button>
         </div>
      </div>
   </div>
</fieldset>