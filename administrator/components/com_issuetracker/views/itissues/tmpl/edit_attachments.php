<?php
/*
 *
 * @Version       $Id: edit_attachments.php 2167 2016-01-01 16:41:39Z geoffc $
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

$user = JFactory::getUser();
$canEdit    = $user->authorise('core.edit',        'com_issuetracker');

?>
<fieldset class="adminform">
   <legend><?php echo JText::_( 'COM_ISSUETRACKER_ATTACHMENT_INFORMATION' ); ?></legend>

   <table class="ittable table-striped">
      <thead>
         <tr>
            <th class='center'>
            <?php echo JText::_('COM_ISSUETRACKER_ATTACHMENTS_FILENAME'); ?>
            </th>
            <th class='center'>
            <?php echo JText::_('COM_ISSUETRACKER_ATTACHMENTS_FILETYPE'); ?>
            </th>
            <th class='center'>
            <?php echo JText::_('COM_ISSUETRACKER_ATTACHMENTS_SIZE'); ?>
            </th>
            <th class='center'>
               <!-- Dummy for download button. -->
            </th>
            <th class='center'>
            <?php echo JText::_('COM_ISSUETRACKER_CREATED_ON'); ?>
            </th>
            <th class='center'>
            <?php echo JText::_('COM_ISSUETRACKER_CREATED_BY'); ?>
            </th>
         </tr>
      </thead>

      <tbody>
         <?php foreach ($this->attachment as $i => $att) : ?>
         <tr class="row<?php echo $i % 2; ?>">
            <td  class='center'>
               <?php if ($canEdit) : ?>
                  <a href="<?php echo JRoute::_('index.php?option=com_issuetracker&task=attachment.edit&id='.(int) $att->id); ?>">
                  <?php echo $this->escape($att->filename); ?></a>
               <?php else : ?>
                  <?php echo $this->escape($att->filename); ?>
               <?php endif; ?>
               <!-- ?php echo $this->escape($att->filename); ? -->
            </td>
            <td class='center'>
               <?php echo $att->filetype; ?>
            </td>
            <td class='center'>
               <?php echo $att->size; ?>
            </td>
            <td class='center'>
               <a class="btn btn-info" href="<?php echo 'index.php?option=com_issuetracker&task=attachment.read&id='.$att->id ?>">
               <class="icon-folder-open icon-white">
               <?php echo JText::_('COM_ISSUETRACKER_ATTACHMENT_DOWNLOAD') ?>
               </a>
           </td>
           <td class='center'>
               <?php echo IssueTrackerHelperDate::getDate($att->created_on); ?>
            </td>
            <td class='center'>
               <?php echo $att->created_by; ?>
            </td>
         </tr>
         <?php endforeach; ?>
      </tbody>
   </table>
</fieldset>