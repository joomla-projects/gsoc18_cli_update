<?php
/*
 *
 * @Version       $Id: default.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.7
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'issuetracker.php';
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'html'.DS.'grid.php' );

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();
$userId  = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canOrder   = $user->authorise('core.edit.state', 'com_issuetracker');
$saveOrder  = $listOrder == 'a.ordering';
if ($saveOrder) {
   $saveOrderingUrl = 'index.php?option=com_issuetracker&task=itpeoplelist.saveOrderAjax&tmpl=component';
   JHtml::_('sortablelist.sortable', 'peopleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();

// website root directory
$_root = JURI::root();

$image_yes = $_root . "media/com_issuetracker/images/tick.png";
$image_no  = $_root . "media/com_issuetracker/images/publish_x.png";



?>
<script type="text/javascript">
Joomla.orderTable = function() {
   var table = document.getElementById("sortTable");
   var direction = document.getElementById("directionTable");
   var order = table.options[table.selectedIndex].value;
   var dirn = '';
   if (order != '<?php echo $listOrder; ?>') {
      dirn = 'asc';
   } else {
      dirn = direction.options[direction.selectedIndex].value;
   }
   Joomla.tableOrdering(order, dirn, '');
}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&view=itpeoplelist'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
   <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
   </div>
   <div id="j-main-container" class="span10">
<?php else : ?>
   <div id="j-main-container">
<?php endif;?>

      <div id="filter-bar" class="btn-toolbar">
         <div class="filter-search btn-group pull-left">
            <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
            <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />
            <button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
            <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
         </div>
         <div class="btn-group pull-right hidden-phone">
             <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
             <?php echo $this->pagination->getLimitBox(); ?>
         </div>
         <div class="btn-group pull-right hidden-phone">
            <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></label>
            <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
               <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
               <option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
               <option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');  ?></option>
            </select>
         </div>
         <div class="btn-group pull-right">
            <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
            <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
               <option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
               <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
            </select>
         </div>
      </div>
      <div class="clearfix"> </div>

   <table class="ittable table-striped" id="peopleList">
      <thead>
         <tr>
            <?php if (isset($this->items[0]->ordering)): ?>
               <th width="1%" class="nowrap center hidden-phone">
                  <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
               </th>
            <?php endif; ?>
            <th width="1%" class="hidden-phone">
               <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
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

            <?php if ( IssuetrackerHelper::comp_installed('com_acysms')) { ?>
               <th class='left'>
                  <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_SMS_NOTIFICATIONS', 'a.sms_notify', $listDirn, $listOrder); ?>
               </th>
            <?php } ?>

            <?php if (isset($this->items[0]->published)) { ?>
               <th style="width:5%">
                  <?php echo JHtml::_('grid.sort',  'JPUBLISHED', 'a.published', $listDirn, $listOrder); ?>
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
            <?php if (isset($this->items[0]->ordering)): ?>
               <td class="order nowrap center hidden-phone">
                  <?php if ($canChange) :
                     $disableClassName = '';
                     $disabledLabel   = '';
                     if (!$saveOrder) :
                        $disabledLabel    = JText::_('JORDERINGDISABLED');
                        $disableClassName = 'inactive tip-top';
                     endif; ?>
                     <span class="sortable-handler hasTooltip <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>">
                     <i class="icon-menu"></i>
                  </span>
                     <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
                  <?php else : ?>
                     <span class="sortable-handler inactive" >
                     <i class="icon-menu"></i>
                  </span>
                  <?php endif; ?>
               </td>
            <?php endif; ?>
            <td class="hidden-phone">
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

            <td style="text-align:center;">
               <?php if ( $item->registered) {
                  echo "<img src='" . $image_yes . "' width='16' height='16' border='0' />";
               } else {
                  echo "<img src='" . $image_no . "' width='16' height='16' border='0' />";
               } ?>
            </td>

            <td class="center">
               <div class="btn-group">
                  <?php echo IssuetrackerGrid::isadmin( $item->issues_admin, $i, 'itpeoplelist.', $canChange); ?>
               </div>
            </td>

            <td class="center">
               <div class="btn-group">
                  <?php echo IssuetrackerGrid::staff( $item->staff, $i, 'itpeoplelist.', $canChange); ?>
               </div>
            </td>

            <td class="center">
               <div class="btn-group">
                  <?php echo IssuetrackerGrid::msgnotify( $item->email_notifications, $i, 'itpeoplelist.', $canChange); ?>
               </div>
            </td>
            <?php if ( IssuetrackerHelper::comp_installed('com_acysms')) { ?>
               <td class="center">
                  <div class="btn-group">
                     <?php echo IssuetrackerGrid::smsnotify( $item->sms_notify, $i, 'itpeoplelist.', $canChange); ?>
                  </div>
               </td>
            <?php } ?>

            <?php if (isset($this->items[0]->published)) { ?>
               <td class="center">
                  <div class="btn-group">
                     <?php echo JHtml::_('jgrid.published', $item->published, $i, 'itpeoplelist.', $canChange, 'cb'); ?>
                  </div>
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
   </div>

   <div>
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
      <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
      <?php echo JHtml::_('form.token'); ?>
   </div>
</form>