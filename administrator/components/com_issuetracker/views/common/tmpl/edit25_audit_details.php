<?php
/*
 *
 * @Version       $Id: edit25_audit_details.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.2
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
defined('_JEXEC') or die('Restricted Access');

// Load date helper
if (! class_exists('IssueTrackerHelperDate')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'dates.php');
}

// Get the dates in local timezone. Need to add the input tags manually else JHtml complians with the message
// Notice: Object of class JDate could not be converted to int in /share/MD0_DATA/Web/DEV/libraries/joomla/html/html.php on line 901.
if ( $this->item->created_on == '0000-00-00 00:00:00' || empty($this->item->created_on) || is_null($this->item->created_on) ) {
   $d0 = "";
} else {
   $d0 = IssueTrackerHelperDate::getDate($this->item->created_on);
}

if ( $this->item->modified_on == '0000-00-00 00:00:00' || empty($this->item->modified_on) || is_null($this->item->modified_on) ) {
   $d1 = "";
} else {
   $d1 = IssueTrackerHelperDate::getDate($this->item->modified_on);
}

?>
   <fieldset class="adminform">
      <legend><?php echo JText::_( 'COM_ISSUETRACKER_AUDIT_INFORMATION' ); ?></legend>
         <ul class="adminformlist">

         <li><?php echo $this->form->getLabel('created_on'); ?>
         <!-- ?php echo $this->form->getInput('created_on'); ?></li -->
         <input type="text" name="jform[created_on]" id="jform_created_on" value="<?php echo $d0; ?>" size="40" disabled="disabled" readonly="readonly"/>
         </li>

         <li><?php echo $this->form->getLabel('created_by'); ?>
         <?php echo $this->form->getInput('created_by'); ?></li>

         <li><?php echo $this->form->getLabel('modified_on'); ?>
         <input type="text" name="jform[modified_on]" id="jform_modified_on" value="<?php echo $d1; ?>" size="40" disabled="disabled" readonly="readonly"/></li>
         <!-- ?php echo $this->form->getInput('modified_on'); ?></li -->

         <li><?php echo $this->form->getLabel('modified_by'); ?>
         <?php echo $this->form->getInput('modified_by'); ?></li>

         </ul>
   </fieldset>