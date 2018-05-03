<?php
/*
 *
 * @Version       $Id: default_progress.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.10
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
defined('_JEXEC') or die('Restricted Access');

if (! class_exists('IssueTrackerHelperSite')) {
   require_once( JPATH_ROOT.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'helper.php');
}

// Load date helper
if (! class_exists('IssueTrackerHelperDate')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'dates.php');
}

$user = JFactory::getUser();
$isadmin             = IssueTrackerHelperSite::isIssueAdmin($user->id);
$isstaff             = IssueTrackerHelperSite::isIssueStaff($user->id);

$canEdit    = $user->authorise('core.edit',        'com_issuetracker');
$canChange  = $user->authorise('core.edit.state',  'com_issuetracker');

// website root directory
$_root = JURI::root();
$jversion = new JVersion();
if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
   $image_yes = $_root . "administrator/templates/isis/images/admin/tick.png";
   $image_no  = $_root . "administrator/templates/isis/images/admin/publish_r.png";
}else {
   $image_yes = $_root . "administrator/templates/bluestork/images/admin/tick.png";
   $image_no  = $_root . "administrator/templates/bluestork/images/admin/publish_r.png";
}
?>
   <fieldset class="adminform">
      <legend><?php echo JText::_( 'COM_ISSUETRACKER_PROGRESS_HISTORY' ); ?></legend>

   <table class="itstyle table-striped">
      <thead>
         <tr>
            <?php if ( $isadmin || $isstaff ) : ?>
               <th style="width:25px;">
                  <?php echo JText::_('COM_ISSUETRACKER_PROGRESS_LINENO'); ?>
               </th>
            <?php endif; ?>
            <th>
               <?php echo JText::_('COM_ISSUETRACKER_PROGRESS'); ?>
            </th>
            <?php if ( $isadmin || $isstaff ) : ?>
               <th  style="width:68px;">
                  <?php echo JText::_('JPUBLISHED'); ?>
               </th>
            <?php endif; ?>
            <th style="width:75px;">
               <?php echo JText::_('COM_ISSUETRACKER_CREATED_ON'); ?>
            </th>
            <th style="width:85px;">
               <?php echo JText::_('COM_ISSUETRACKER_CREATED_BY'); ?>
            </th>
         </tr>
      </thead>

      <tbody>
         <?php foreach ($this->progress as $i => $prog) : ?>
         <tr class="row<?php echo $i % 2; ?>">
            <?php if ( $isadmin || $isstaff ) : ?>
               <td>
                  <?php echo $prog->lineno; ?>
               </td>
            <?php endif; ?>
            <td>
               <?php echo JHtml::_('content.prepare', $prog->progress); ?>
            </td>
            <?php if ( $isadmin || $isstaff ) : ?>
               <td style="vertical-align:middle;text-align:center;">
                  <!-- ?php echo JHtml::_('jgrid.published', $prog->state, $i, 'pactions.', $canChange, 'cb'); ? -->
                  <?php if ($prog->state == 1) {
                     echo "<img src='" . $image_yes . "' width='16' height='16' border='0' />";
                  } else {
                     echo "<img src='" . $image_no . "' width='16' height='16' border='0' />";
                  } ?>
               </td>
            <?php endif; ?>
            <td>
               <?php echo IssueTrackerHelperDate::getDate($prog->created_on); ?>
            </td>
            <td style="word-wrap:break-word;">
               <?php echo $prog->created_by; ?>
            </td>
         </tr>
         <?php endforeach; ?>
      </tbody>
   </table>
   </fieldset>
