<?php
/*
 *
 * @Version       $Id: default.php 1292 2014-01-12 18:57:04Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.5.2
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2014-01-12 18:57:04 +0000 (Sun, 12 Jan 2014) $
 *
 */

// no direct access
defined('_JEXEC') or die;

require_once( JPATH_COMPONENT.DS.'helpers'.DS.'html'.DS.'grid.php' );

// Import CSS
// $document = JFactory::getDocument();
// $document->addStyleSheet('components/com_issuetracker/assets/css/issuetracker.css');

$user = JFactory::getUser();
$userId  = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canOrder   = $user->authorise('core.edit.state', 'com_issuetracker');
$saveOrder  = $listOrder == 'a.ordering';
if ($saveOrder)
{
   $saveOrderingUrl = 'index.php?option=com_issuetracker&task=jtriggers.saveOrderAjax&tmpl=component';
   JHtml::_('sortablelist.sortable', 'jtriggerList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

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

<?php
//Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar)) {
    $this->sidebar .= $this->extra_sidebar;
}
?>

<?php if (!empty( $this->sidebar)) : ?>
   <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
   </div>
   <div id="j-main-container" class="span10">
<?php else : ?>
   <div id="j-main-container">
<?php endif;?>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&view=jtriggers'); ?>" method="post" name="adminForm" id="adminForm">
  <fieldset id="filter-bar" class="btn-toolbar">
      <div class="filter-search pull-left">
         <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
         <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />
         <button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
         <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
      </div>
      <div class="filter-select pull-right">
         <select name="filter_published" class="inputbox" onchange="this.form.submit()">
            <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
            <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true);?>
         </select>
      </div>
   </fieldset>
   <div class="clr"> </div>

   <table class="ittable table-striped" id="jtriggerList">
      <thead>
         <tr>
            <?php if (isset($this->items[0]->ordering)): ?>
               <th style="width:1%" class="nowrap center hidden-phone">
                  <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
               </th>
            <?php endif; ?>
            <th style="width:1%" class="hidden-phone">
               <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
            </th>
            <?php if (isset($this->items[0]->state)): ?>
               <th style="width:1%" class="nowrap center">
                  <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
               </th>
            <?php endif; ?>

            <th class='left'>
               <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_TABLE_NAME', 'a.table_name', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
               <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_JTRIGGERS_TRIGGER_NAME', 'a.trigger_name', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
               <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_JTRIGGERS_TRIGGER_TYPE', 'a.trigger_type', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
               <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_JTRIGGERS_TRIGGER_EVENT', 'a.trigger_event', $listDirn, $listOrder); ?>
            </th>
            <!-- th class='left'>
               <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_JTRIGGERS_ACTION_ORIENTATION', 'a.action_orientation', $listDirn, $listOrder); ?>
            </th -->
            <th class='left'>
               <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_JTRIGGERS_COLUMNS', 'a.columns', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
               <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_ENABLED', 'a.applied', $listDirn, $listOrder); ?>
            </th>
            <!-- th class='left'>
               <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_JTRIGGERS_TRIGGER_TEXT', 'a.trigger_txt', $listDirn, $listOrder); ?>
            </th -->
            <th class='left'>
               <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_CREATED_BY', 'a.created_by', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
               <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_CREATED_ON', 'a.created_on', $listDirn, $listOrder); ?>
            </th>


            <?php if (isset($this->items[0]->id)): ?>
               <th style="width:1%" class="nowrap center hidden-phone">
                  <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
               </th>
            <?php endif; ?>
            </tr>
         </thead>
         <tfoot>
                <?php
                if (isset($this->items[0])){
                    $colspan = count(get_object_vars($this->items[0]));
                } else{
                    $colspan = 10;
                }
            ?>
            <tr>
               <td colspan="<?php echo $colspan ?>">
                  <?php echo $this->pagination->getListFooter(); ?>
               </td>
            </tr>
         </tfoot>
         <tbody>
         <?php foreach ($this->items as $i => $item) :
            $ordering   = ($listOrder == 'a.ordering');
                $canCreate = $user->authorise('core.create',      'com_issuetracker');
                $canEdit   = $user->authorise('core.edit',        'com_issuetracker');
                $canCheckin   = $user->authorise('core.manage',   'com_issuetracker');
                $canChange = $user->authorise('core.edit.state',  'com_issuetracker');
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
               <td class="center hidden-phone">
                  <?php echo JHtml::_('grid.id', $i, $item->id); ?>
               </td>
                <?php if (isset($this->items[0]->state)): ?>
               <td class="center">
                  <?php echo JHtml::_('jgrid.published', $item->state, $i, 'jtriggers.', $canChange, 'cb'); ?>
               </td>
                <?php endif; ?>

            <td>
               <?php echo $item->table_name; ?>
            </td>

            <td>
            <?php if (isset($item->checked_out) && $item->checked_out) : ?>
               <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'jtriggers.', $canCheckin); ?>
            <?php endif; ?>
            <?php if ($canEdit) : ?>
               <a href="<?php echo JRoute::_('index.php?option=com_issuetracker&task=jtrigger.edit&id='.(int) $item->id); ?>">
               <?php echo $this->escape($item->trigger_name); ?></a>
            <?php else : ?>
               <?php echo $this->escape($item->trigger_name); ?>
            <?php endif; ?>
            </td>
            <td>
               <?php echo $item->trigger_type; ?>
            </td>
            <td>
               <?php echo $item->trigger_event; ?>
            </td>
            <!-- td>
               <?php echo $item->action_orientation; ?>
            </td -->
            <td>
               <?php echo $item->columns; ?>
            </td>
            <td style="margin: 0 auto;">
               <?php echo IssuetrackerGrid::applied( $item->applied, $i, 'jtriggers.', $canChange); ?>
            </td>
            <!-- td>
               <?php echo $item->trigger_text; ?>
            </td -->
            <td>
               <?php echo $item->created_by; ?>
            </td>
            <td>
               <?php echo $item->created_on; ?>
            </td>

            <?php if (isset($this->items[0]->id)): ?>
               <td class="center hidden-phone">
                  <?php echo (int) $item->id; ?>
               </td>
            <?php endif; ?>
            </tr>
            <?php endforeach; ?>
         </tbody>
      </table>

      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
      <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
      <?php echo JHtml::_('form.token'); ?>
</form>
</div>