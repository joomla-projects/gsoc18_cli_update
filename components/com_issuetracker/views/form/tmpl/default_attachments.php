<?php
/*
 *
 * @Version       $Id: default_attachments.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.5
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

if (! class_exists('JHtmlIssueTracker')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'html'.DS.'itissues.php');
}

?>
<fieldset class="adminform">
   <legend><?php echo JText::_( 'COM_ISSUETRACKER_TITLE_ATTACHMENTS' ); ?></legend>

   <table class="itstyle table-striped">
      <thead>
         <tr>
            <th>
            <?php echo JText::_('COM_ISSUETRACKER_ATTACHMENTS_FILENAME'); ?>
            </th>
            <th>
            <?php echo JText::_('COM_ISSUETRACKER_ATTACHMENTS_FILETYPE'); ?>
            </th>
            <th>
            <?php echo JText::_('COM_ISSUETRACKER_ATTACHMENTS_SIZE'); ?>
            </th>
            <th>
            <?php echo JText::_('COM_ISSUETRACKER_CREATED_ON'); ?>
            </th>
            <th>
            <?php echo JText::_('COM_ISSUETRACKER_CREATED_BY'); ?>
            </th>
         </tr>
      </thead>

      <tbody>
         <?php foreach ($this->attachment as $i => $att) : ?>
         <tr class="row<?php echo $i % 2; ?>">
            <td>
               <div style="float: right;">
                  <?php echo JHtml::_('issuetracker.viewAttachment', $att->id); ?>
               </div>
               <span title="<?php echo JText::_( 'COM_ISSUETRACKER_VIEW_ATTACHMENT' );?>::<?php echo $this->escape($att->filename); ?>">
                  <a href="<?php echo 'index.php?option=com_issuetracker&task=attachment.read&id='.$att->id; ?>"><?php echo $this->escape($att->filename); ?></a>
               </span>
            </td>
            <td>
               <?php echo $att->filetype; ?>
            </td>
            <td>
               <?php echo $att->size; ?>
            </td>
            <td>
               <?php echo IssueTrackerHelperDate::getDate($att->created_on); ?>
            </td>
            <td>
               <?php echo $att->created_by; ?>
            </td>
         </tr>
         <?php endforeach; ?>
      </tbody>
   </table>
</fieldset>