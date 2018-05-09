<?php
/*
 *
 * @Version       $Id: edit_audit_details.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.0
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
defined('_JEXEC') or die;

?>
   <fieldset class="adminform">
      <legend><?php echo JText::_( 'COM_ISSUETRACKER_AUDIT_INFORMATION' ); ?></legend>
         <ul class="adminformlist">

         <li><?php echo $this->form->getLabel('created_on'); ?>
         <?php echo $this->form->getInput('created_on'); ?></li>

         <li><?php echo $this->form->getLabel('created_by'); ?>
         <?php echo $this->form->getInput('created_by'); ?></li>

         <li><?php echo $this->form->getLabel('modified_on'); ?>
         <?php echo $this->form->getInput('modified_on'); ?></li>

         <li><?php echo $this->form->getLabel('modified_by'); ?>
         <?php echo $this->form->getInput('modified_by'); ?></li>

         </ul>

   </fieldset>
