<?php
/*
 *
 * @Version       $Id: edit_progress.php 2167 2016-01-01 16:41:39Z geoffc $
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

require_once( JPATH_COMPONENT.DS.'helpers'.DS.'html'.DS.'grid.php' );

// Load date helper
if (! class_exists('IssueTrackerHelperDate')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'dates.php');
}

$user = JFactory::getUser();
$canEdit    = $user->authorise('core.edit',        'com_issuetracker');
$canChange  = $user->authorise('core.edit.state',  'com_issuetracker');

// website root directory
$_root = JURI::root();
$image_yes = $_root . "administrator/templates/isis/images/admin/tick.png";
$image_no  = $_root . "administrator/templates/isis/images/admin/publish_r.png";

?>
<fieldset class="adminform">
   <legend><?php echo JText::_( 'COM_ISSUETRACKER_PROGRESS_HISTORY' ); ?></legend>

   <table class="table table-striped">
      <thead>
         <tr>
            <th class='left' style="width:2%">
               <?php echo JText::_('COM_ISSUETRACKER_PROGRESS_LINENO'); ?>
            </th>
            <th class='left'>
               <?php echo JText::_('COM_ISSUETRACKER_PROGRESS'); ?>
            </th>
            <th style="width:5%">
               <?php echo JText::_('COM_ISSUETRACKER_ACCESS'); ?>
            </th>
            <th style="width:5%">
               <?php echo JText::_('JPUBLISHED'); ?>
            </th>
            <th style="width:5%">
               <?php echo JText::_('COM_ISSUETRACKER_PUBLIC'); ?>
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
         <?php foreach ($this->progress as $i => $prog) : ?>
         <tr class="row<?php echo $i % 2; ?>">
            <td class='left'>
               <?php if ($canEdit) : ?>
                  <a href="<?php echo JRoute::_('index.php?option=com_issuetracker&task=paction.edit&id='.(int) $prog->id); ?>">
                  <?php echo $prog->lineno; ?></a>
               <?php else : ?>
                  <?php echo $prog->lineno; ?>
               <?php endif; ?>
            </td>
            <td class='left'>
               <?php echo $prog->progress; ?>
            </td>
            <td>
               <?php echo $prog->access_level; ?>
            </td>
            <td style="vertical-align:middle;text-align:center;">
               <?php if ($prog->state == 1) {
                  echo "<img src='" . $image_yes . "' width='16' height='16' border='0' />";
               } else {
                  echo "<img src='" . $image_no . "' width='16' height='16' border='0' />";
               } ?>
               <!-- ?php echo JHtml::_('jgrid.published', $prog->state, $i, 'pactions.', $canChange, 'cb'); ? -->
            </td>
            <td style="vertical-align:middle;text-align:center;">
               <?php if ($prog->public == 1) {
                  echo "<img src='" . $image_yes . "' width='16' height='16' border='0' />";
               } else {
                  echo "<img src='" . $image_no . "' width='16' height='16' border='0' />";
               } ?>
               <!-- ?php echo IssuetrackerGrid::privacy( $prog->public, $i, 'pactions.', $canChange); ? -->
            </td>
            <td class='center'>
               <?php echo IssueTrackerHelperDate::getDate($prog->created_on); ?>
            </td>
            <td class='center'>
               <?php echo $prog->created_by; ?>
            </td>
         </tr>
         <?php endforeach; ?>
      </tbody>
   </table>
</fieldset>