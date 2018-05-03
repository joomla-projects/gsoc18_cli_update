<?php
/*
 *
 * @Version       $Id: default.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

defined('_JEXEC') or die('Restricted access');

require_once( JPATH_COMPONENT.DS.'helpers'.DS.'html'.DS.'grid.php' );

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
$user = JFactory::getUser();
$userId  = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canOrder   = $user->authorise('core.edit.state', 'com_issuetracker');
$saveOrder  = $listOrder == 'a.ordering';

// website root directory
$_root = JURI::root();

$image_yes = $_root . "administrator/templates/bluestork/images/admin/tick.png";
$image_no  = $_root . "administrator/templates/bluestork/images/admin/publish_x.png";

?>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&view=itpeoplelist'); ?>" method="post" name="adminForm" id="adminForm">
 <?php if (!empty( $this->sidebar)) : ?>
   <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
   </div>
   <div id="j-main-container" class="span10">
<?php else : ?>
   <div id="j-main-container">
<?php endif;?>
   <fieldset id="filter-bar">
      <div class="filter-search fltlft">
         <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
         <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />
         <button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
         <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
      </div>

      <div class="filter-select fltrt">
        <select name="filter_roles_id" class="inputbox" onchange="this.form.submit()">
            <?php echo JHtml::_('select.options', IssueTrackerHelper::getRoles(), 'value', 'text', $this->state->get('filter.roles_id'));?>
         </select>

         <select name="filter_project_id" class="inputbox" onchange="this.form.submit()">
            <?php echo JHtml::_('select.options', IssueTrackerHelper::getProject_name(), 'value', 'text', $this->state->get('filter.project_id'));?>
         </select>

         <select name="filter_state" class="inputbox" onchange="this.form.submit()">
            <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
            <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('all'=>false,'archived'=>false,'trash'=>false)), 'value', 'text', $this->state->get('filter.state'), true);?>
         </select>
      </div>

   </fieldset>
   <div class="clr"> </div>

   <table class="ittable table-striped">
      <thead>
         <tr>
            <th style="width:1%">
               <input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
            </th>

            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_PERSON_NAME', 'a.person_name', $listDirn, $listOrder); ?>
            </th>

            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_USER_ID', 'a.user_id', $listDirn, $listOrder); ?>
            </th>

            <th class='left'>
            <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_USERNAME' ), 'a.username', $listDirn, $listOrder); ?>
            </th>

            <th class='left'>
            <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_PERSON_EMAIL' ), 'a.person_email', $listDirn, $listOrder); ?>
            </th>

            <th class='left'>
            <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_PERSON_ROLE' ), 'a.person_role', $listDirn, $listOrder); ?>
            </th>

            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_ASSIGNED_PROJECT', 'a.assigned_project', $listDirn, $listOrder); ?>
            </th>

            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_REGISTERED', 'a.registered', $listDirn, $listOrder); ?>
            </th>

            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_ISSUES_ADMINISTRATOR', 'a.issues_admin', $listDirn, $listOrder); ?>
            </th>

            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_ISSUES_STAFF', 'a.staff', $listDirn, $listOrder); ?>
            </th>

            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_EMAIL_NOTIFICATIONS', 'a.email_notifications', $listDirn, $listOrder); ?>
            </th>

            <?php if (isset($this->items[0]->published)) { ?>
               <th style="width:5%">
                  <?php echo JHtml::_('grid.sort',  'JPUBLISHED', 'a.published', $listDirn, $listOrder); ?>
               </th>
            <?php } ?>
            <?php if (isset($this->items[0]->ordering)) { ?>
               <th style="width:10%">
                  <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
                  <?php if ($canOrder && $saveOrder) :?>
                     <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'itpeoplelist.saveorder'); ?>
                  <?php endif; ?>
               </th>
            <?php } ?>
            <?php if (isset($this->items[0]->id)) { ?>
                <th style="width:1%" class="nowrap">
                    <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
            <?php } ?>
         </tr>
      </thead>
      <tfoot>
         <tr>
            <td colspan="10">
               <?php echo $this->pagination->getListFooter(); ?>
            </td>
         </tr>
      </tfoot>
      <tbody>
      <?php foreach ($this->items as $i => $item) :
         $ordering   = ($listOrder == 'a.ordering');
         $canCreate  = $user->authorise('core.create',      'com_issuetracker');
         $canEdit    = $user->authorise('core.edit',        'com_issuetracker');
         $canCheckin = $user->authorise('core.manage',      'com_issuetracker');
         $canChange  = $user->authorise('core.edit.state',  'com_issuetracker');
         ?>
         <tr class="row<?php echo $i % 2; ?>">
            <td class="center">
               <?php echo JHtml::_('grid.id', $i, $item->id); ?>
            </td>

            <td>
            <?php if (isset($item->checked_out) && $item->checked_out) : ?>
               <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'itpeoplelist.', $canCheckin); ?>
            <?php endif; ?>
            <?php if ($canEdit) : ?>
               <a href="<?php echo JRoute::_('index.php?option=com_issuetracker&task=itpeople.edit&id='.(int) $item->id); ?>">
               <?php echo $this->escape($item->person_name); ?></a>
            <?php else : ?>
               <?php echo $this->escape($item->person_name); ?>
            <?php endif; ?>
            </td>

           <td>
               <?php echo $item->user_id; ?>
            </td>

           <td>
               <?php echo $item->username; ?>
            </td>
            <td>
               <?php echo $item->person_email; ?>
            </td>
            <td>
               <?php echo $item->role_name; ?>
            </td>
             <td>
               <?php echo $item->project_name; ?>
            </td>

            <td style="margin: 0 auto;">
               <?php if ( $item->registered) {
                   echo "<img src='" . $image_yes . "' width='16' height='16' border='0' />";
                } else {
                   echo "<img src='" . $image_no . "' width='16' height='16' border='0' />";
                } ?>
            </td>

            <td style="margin: 0 auto;">
               <?php echo IssuetrackerGrid::isadmin( $item->issues_admin, $i, 'itpeoplelist.', $canChange); ?>
            </td>

            <td style="margin: 0 auto;">
               <?php echo IssuetrackerGrid::staff( $item->staff, $i, 'itpeoplelist.', $canChange); ?>
            </td>

            <td style="margin: 0 auto;">
               <?php echo IssuetrackerGrid::msgnotify( $item->email_notifications, $i, 'itpeoplelist.', $canChange); ?>
            </td>

            <?php if (isset($this->items[0]->published)) { ?>
                <td class="center">
                   <?php echo JHtml::_('jgrid.published', $item->published, $i, 'itpeoplelist.', $canChange, 'cb'); ?>
                </td>
            <?php } ?>
            <?php if (isset($this->items[0]->ordering)) { ?>
                <td class="order">
                   <?php if ($canChange) : ?>
                      <?php if ($saveOrder) :?>
                         <?php if ($listDirn == 'asc') : ?>
                            <span><?php echo $this->pagination->orderUpIcon($i, true, 'itpeoplelist.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                            <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'itpeoplelist.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                         <?php elseif ($listDirn == 'desc') : ?>
                            <span><?php echo $this->pagination->orderUpIcon($i, true, 'itpeoplelist.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                            <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'itpeoplelist.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                         <?php endif; ?>
                      <?php endif; ?>
                      <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
                      <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
                   <?php else : ?>
                      <?php echo $item->ordering; ?>
                   <?php endif; ?>
                </td>
            <?php } ?>
            <?php if (isset($this->items[0]->id)) { ?>
               <td class="center">
                  <?php echo (int) $item->id; ?>
              </td>
            <?php } ?>
         </tr>
         <?php endforeach; ?>
      </tbody>
   </table>

   <div>
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
      <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
      <?php echo JHtml::_('form.token'); ?>
   </div>
</form>