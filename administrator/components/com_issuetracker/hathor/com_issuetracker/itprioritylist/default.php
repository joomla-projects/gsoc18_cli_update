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

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canOrder   = $user->authorise('core.edit.state', 'com_issuetracker');
$saveOrder  = $listOrder == 'a.ordering';

require_once dirname(__FILE__).'/coloriser.php';
?>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&view=itprioritylist'); ?>" method="post" name="adminForm" id="adminForm">
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
          <select name="filter_published" class="inputbox" onchange="this.form.submit()">
             <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
             <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true);?>
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
               <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_PRIORITY_NAME', 'a.priority_name', $listDirn, $listOrder); ?>
            </th>

            <th class='left'>
               <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_RANKING' ), 'a.ranking', $listDirn, $listOrder); ?>
            </th>

            <th class='left'>
               <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_RESPONSE_TIME' ), 'a.response_time', $listDirn, $listOrder); ?>
            </th>

            <th class='left'>
               <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_RESOLUTION_TIME' ), 'a.resolution_time', $listDirn, $listOrder); ?>
            </th>

            <th class='left'>
               <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
            </th>

            <?php if (isset($this->items[0]->state)) { ?>
               <th style="width:5%">
                  <?php echo JHtml::_('grid.sort',  'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
               </th>
            <?php } ?>

            <?php if (isset($this->items[0]->ordering)) { ?>
               <th style="width:10%">
                  <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
                  <?php if ($canOrder && $saveOrder) :?>
                     <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'itprioritylist.saveorder'); ?>
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
         <!-- tr class="row<?php echo $i % 2; ?>" -->
         <tr>
            <td class="center">
               <?php echo JHtml::_('grid.id', $i, $item->id); ?>
            </td>

            <td>
               <?php if (isset($item->checked_out) && $item->checked_out) : ?>
                  <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'itprioritylist.', $canCheckin); ?>
               <?php endif; ?>
               <?php if ($canEdit) : ?>
                  <a href="<?php echo JRoute::_('index.php?option=com_issuetracker&task=itpriority.edit&id='.(int) $item->id); ?>">
                  <?php echo $this->escape($item->priority_name); ?></a>
               <?php else : ?>
                  <?php echo $this->escape($item->priority_name); ?>
               <?php endif; ?>
            </td>

            <!-- td -->
              <!-- ?php echo $item->ranking; ? -->
            <!-- /td -->

            <?php echo IssueTrackerPriorityColoriser::colortext($item->ranking, $item->ranking); ?>

            <td>
              <?php echo $item->response_time; ?>
            </td>

            <td>
              <?php echo $item->resolution_time; ?>
            </td>

            <td>
               <?php echo $item->description; ?>
            </td>

            <?php if (isset($this->items[0]->state)) { ?>
               <td class="center">
                  <?php echo JHtml::_('jgrid.published', $item->state, $i, 'itprioritylist.', $canChange, 'cb'); ?>
               </td>
            <?php } ?>

            <?php if (isset($this->items[0]->ordering)) { ?>
                <td class="order">
                   <?php if ($canChange) : ?>
                      <?php if ($saveOrder) :?>
                         <?php if ($listDirn == 'asc') : ?>
                            <span><?php echo $this->pagination->orderUpIcon($i, true, 'itprioritylist.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                            <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'itprioritylist.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                         <?php elseif ($listDirn == 'desc') : ?>
                            <span><?php echo $this->pagination->orderUpIcon($i, true, 'itprioritylist.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                            <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'itprioritylist.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
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