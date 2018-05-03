<?php
/*
 *
 * @Version       $Id: default.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.8
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

defined('_JEXEC') or die('Restricted access');

// Load date helper
if (! class_exists('IssueTrackerHelperDate')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'dates.php');
}

// JHtml::_('behavior.tooltip');
// JHTML::_('script','system/multiselect.js',false,true);
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
   $saveOrderingUrl = 'index.php?option=com_issuetracker&task=itissueslist.saveOrderAjax&tmpl=component';
   JHtml::_('sortablelist.sortable', 'issueList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

// echo "<pre>"; var_dump($this->items); echo "</pre>";
// echo "<pre>"; var_dump($this->state->params); echo "</pre>";
// Get the optional headings settings
$show_assigned = $this->state->params->get('show_assigned_to_headings');
$show_identifier = $this->state->params->get('show_identified_by_headings');
$show_created_by = $this->state->params->get('show_created_by_headings');
$show_created_on = $this->state->params->get('show_created_on_headings');
$show_modified_by = $this->state->params->get('show_modified_by_headings');
$show_modified_on = $this->state->params->get('show_modified_on_headings');

$show_start_date = $this->state->params->get('show_identified_date_headings');
$show_close_date = $this->state->params->get('show_close_date_headings');

$allow_private = $this->state->params->get('allow_private_issues');

require_once dirname(__FILE__).'/coloriser.php';

// website root directory
$_root = JURI::root();
$image_yes  = $_root . "administrator/templates/isis/images/admin/tick.png";
$image_no   = $_root . "administrator/templates/isis/images/admin/publish_x.png";
$archived   = $this->state->get('filter.state') == 2 ? true : false;
$trashed    = $this->state->get('filter.state') == -2 ? true : false;
$sortFields = $this->getSortFields();
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

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&view=itissueslist'); ?>" method="post" name="adminForm" id="adminForm">
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


      <table class="ittable table-striped" id="issueList">
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
                  <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_ISSUE_SUMMARY' ), 'a.issue_summary', $listDirn, $listOrder); ?>
               </th>
               <th class='left'>
                  <?php echo JHtml::_('grid.sort', JText::_('COM_ISSUETRACKER_ISSUE_NUMBER'), 'a.alias', $listDirn, $listOrder); ?>
               </th>
               <th class='left'>
                  <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_PROJECT_NAME' ), 'ppath', $listDirn, $listOrder); ?>
               </th>
               <?php if ($show_identifier == 1 ) : ?>
                  <th class='left'>
                     <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_IDENTIFYING_PERSON' ), 'identifying_name', $listDirn, $listOrder); ?>
                  </th>
               <?php endif; ?>
               <?php if ($show_assigned == 1 ) : ?>
                  <th class='left'>
                     <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_ASSIGNED_PERSON' ), 'person_name', $listDirn, $listOrder); ?>
                  </th>
               <?php endif; ?>
               <th class='left'>
                     <?php echo JHTML::_('grid.sort', JText::_( 'JSTATUS' ), 'a.status', $listDirn, $listOrder); ?>
               </th>
               <th class='left'>
                  <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_TYPE' ), 'a.issue_type', $listDirn, $listOrder); ?>
               </th>
               <th class='left'>
                  <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_PRIORITY' ), 'a.priority', $listDirn, $listOrder); ?>
               </th>

               <?php if (isset($this->items[0]->state)) { ?>
                  <th style="width:5%">
                     <?php echo JHtml::_('grid.sort',  'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
                  </th>
               <?php } ?>

               <?php if ($allow_private == 1 ) : ?>
                  <?php if (isset($this->items[0]->public)) { ?>
                     <th style="width:3%">
                        <?php echo JHtml::_('grid.sort',  JText::_( 'COM_ISSUETRACKER_FIELD_PUBLIC') , 'a.public', $listDirn, $listOrder); ?>
                     </th>
                  <?php } ?>
               <?php endif; ?>

               <?php if ($show_start_date == 1 ) : ?>
                  <th class='left'>
                     <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_FIELD_IDENTIFIED_DATE' ), 'a.identified_date', $listDirn, $listOrder); ?>
                  </th>
               <?php endif; ?>

               <?php if ($show_close_date == 1 ) : ?>
                  <th class='left'>
                     <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_FIELD_CLOSE_DATE' ), 'a.actual_resolution_date', $listDirn, $listOrder); ?>
                  </th>
               <?php endif; ?>

               <?php if ($show_created_by == 1 ) : ?>
                  <th class='left'>
                     <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_FIELD_CREATED_BY_LABEL' ), 'a.created_by', $listDirn, $listOrder); ?>
                  </th>
               <?php endif; ?>

               <?php if ($show_created_on == 1 ) : ?>
                  <th class='left'>
                     <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_FIELD_CREATED_ON_LABEL' ), 'a.created_on', $listDirn, $listOrder); ?>
                  </th>
               <?php endif; ?>

               <?php if ($show_modified_by == 1 ) : ?>
                  <th class='left'>
                     <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_FIELD_MODIFIED_BY_LABEL' ), 'a.modified_by', $listDirn, $listOrder); ?>
                  </th>
               <?php endif; ?>

               <?php if ($show_modified_on == 1 ) : ?>
                  <th class='left'>
                     <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_FIELD_MODIFIED_ON_LABEL' ), 'a.modified_on', $listDirn, $listOrder); ?>
                  </th>
               <?php endif; ?>

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
            <!-- tr class="row<?php echo $i % 2; ?>" -->
            <tr>
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
                     <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'itissueslist.', $canCheckin); ?>
                  <?php endif; ?>
                  <?php if ($canEdit) : ?>
                     <a href="<?php echo JRoute::_('index.php?option=com_issuetracker&task=itissues.edit&id='.(int) $item->id); ?>">
                     <?php echo $this->escape($item->issue_summary); ?></a>
                  <?php else : ?>
                     <?php echo $this->escape($item->issue_summary); ?>
                  <?php endif; ?>
               </td>

               <td>
                  <?php echo $item->alias; ?>
               </td>
               <td>
                  <?php echo $item->project_name; ?>
               </td>
               <?php if ($show_identifier == 1 ) : ?>
                  <td>
                     <?php echo $item->identifying_name; ?>
                  </td>
               <?php endif; ?>
               <?php if ($show_assigned == 1 ) : ?>
                  <td>
                     <?php echo $item->person_name; ?>
                  </td>
               <?php endif; ?>
               <td>
                  <?php echo $item->status_name; ?>
               </td>
               <td>
                  <?php echo $item->type_name; ?>
               </td>

               <!-- td -->
                  <!-- ?php echo $item->priority_name; ? -->
               <!-- /td -->
               <?php echo IssueTrackerPriorityColoriser::colortext($item->priority_name, $item->ranking); ?>

               <?php if (isset($this->items[0]->state)) { ?>
                  <td class="center">
                     <div class="btn-group">
                        <?php if ($item->public != 0) echo JHtml::_('jgrid.published', $item->state, $i, 'itissueslist.', $canChange, 'cb'); ?>
                        <?php
                           // Create dropdown items
                           $action = $archived ? 'unarchive' : 'archive';
                           JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'itissueslist');

                           $action = $trashed ? 'untrash' : 'trash';
                           JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'itissueslist');

                           // Render dropdown list
                        echo JHtml::_('actionsdropdown.render', $this->escape($item->issue_summary));
                        ?>
                     </div>
                  </td>
               <?php } ?>

               <?php if ($allow_private == 1 ) : ?>
                  <td  style="margin: 0 auto;">
                     <?php if ($item->public == 1) {
                        echo "<img src='" . $image_yes . "' width='16' height='16' border='0' />";
                     } else {
                         echo "<img src='" . $image_no . "' width='16' height='16' border='0' />";
                     } ?>
                  </td>
               <?php endif; ?>

               <?php if ($show_start_date == 1 ) : ?>
                  <td>
                     <?php echo IssueTrackerHelperDate::getDate($item->identified_date); ?>
                  </td>
               <?php endif; ?>
               <?php if ($show_close_date == 1 ) : ?>
                  <td>
                     <?php if ( $item->actual_resolution_date == "0000-00-00 00:00:00" ) { echo ""; } else { echo IssueTrackerHelperDate::getDate($item->actual_resolution_date); } ?>
                     <!-- ?php echo $item->actual_resolution_date; ? -->
                  </td>
               <?php endif; ?>

               <?php if ($show_created_by == 1 ) : ?>
                  <td>
                     <?php echo $item->created_by; ?>
                  </td>
               <?php endif; ?>
               <?php if ($show_created_on == 1 ) : ?>
                  <td>
                     <?php echo IssueTrackerHelperDate::getDate($item->created_on); ?>
                  </td>
               <?php endif; ?>
               <?php if ($show_modified_by == 1 ) : ?>
                  <td>
                     <?php echo $item->modified_by; ?>
                  </td>
               <?php endif; ?>
               <?php if ($show_modified_on == 1 ) : ?>
                  <td>
                     <?php if ( $item->modified_on == "0000-00-00 00:00:00" ) { echo ""; } else { echo IssueTrackerHelperDate::getDate($item->modified_on); } ?>
                  </td>
               <?php endif; ?>

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