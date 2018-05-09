<?php
/*
 *
 * @Version       $Id: edit_progress.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.0
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
defined('_JEXEC') or die('Restricted Access');

$user = JFactory::getUser();
$canEdit    = $user->authorise('core.edit',        'com_issuetracker');
$canChange  = $user->authorise('core.edit.state',  'com_issuetracker');

?>
   <fieldset class="adminform">
      <legend><?php echo JText::_( 'COM_ISSUETRACKER_PROGRESS_HISTORY' ); ?></legend>

   <table class="ittable table-striped">
      <thead>
         <tr>
            <th class='left' style="width:2%">
               <?php echo JText::_('COM_ISSUETRACKER_PROGRESS_LINENO'); ?>
            </th>
            <th class='left'>
               <?php echo JText::_('COM_ISSUETRACKER_PROGRESS'); ?>
            </th>
            <th style="width:5%">
               <?php echo JText::_('JPUBLISHED'); ?>
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
            <td class="center">
               <?php echo JHtml::_('jgrid.published', $prog->state, $i, 'pactions.', $canChange, 'cb'); ?>
            </td>
            <td class='center'>
               <?php echo $prog->created_on ?>
            </td>
            <td class='center'>
               <?php echo $prog->created_by; ?>
            </td>
         </tr>
         <?php endforeach; ?>
      </tbody>
   </table>
</fieldset>