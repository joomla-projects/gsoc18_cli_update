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

defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('behavior.tooltip');

// Import CSS
$document   = JFactory::getDocument();
$document->addStyleSheet('components/com_issuetracker/assets/css/issuetracker.css');

$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$canOrder   = $user->authorise('core.edit.state', 'com_issuetracker');
$saveOrder  = $listOrder == 'a.type';
?>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&view=emails'); ?>" method="post" name="adminForm" id="adminForm">
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
               <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
            </th>
            <th>
               <?php echo JHtml::_('grid.sort', 'COM_ISSUETRACKER_EMAIL_TYPE', 'a.type', $listDirn, $listOrder); ?>
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
                  <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'emails.saveorder'); ?>
               <?php endif; ?>
            </th>
                <?php } ?>
            <th style="width:1%" class="nowrap">
               <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
            </th>
         </tr>
      </thead>

      <tfoot>
         <tr>
            <td colspan="5"><?php echo $this->pagination->getListFooter(); ?></td>
         </tr>
      </tfoot>

      <tbody>
         <?php foreach($this->items as $i => $item):
            $ordering   = ($listOrder == 'a.ordering');
            $canEdit = $user->authorise('core.edit', 'com_issuetracker.email.'.$item->id);
            $canCheckin = $user->authorise('core.admin', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
            //$canEditOwn  = $user->authorise('core.edit.own', 'com_issuetracker.email.'.$item->id) && $item->created_user_id == $userId;
            $canEditOwn = false;
            $canChange  = $user->authorise('core.edit.state',  'com_issuetracker.email.'.$item->id) && $canCheckin;
         ?>
            <tr class="row<?php echo $i % 2; ?>">
               <td class="center">
                  <?php echo JHtml::_('grid.id', $i, $item->id); ?>
               </td>
               <td>
                  <?php
                     if($item->checked_out)
                     {
                        echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'email.', $canCheckin);
                     }
                  ?>
                  <?php if ($canEdit || $canEditOwn) : ?>
                     <a href="<?php echo JRoute::_('index.php?option=com_issuetracker&task=email.edit&id='.$item->id);?>">
                        <?php echo $this->escape($item->type); ?></a>
                  <?php else : ?>
                     <?php echo $this->escape($item->type); ?>
                  <?php endif; ?>
               </td>

               <td>
                  <?php echo $item->description; ?>
               </td>

               <?php if (isset($this->items[0]->state)) { ?>
               <td class="center">
                  <?php echo JHtml::_('jgrid.published', $item->state, $i, 'emails.', $canChange, 'cb'); ?>
               </td>
               <?php } ?>
               <?php if (isset($this->items[0]->ordering)) { ?>
               <td class="order">
                  <?php if ($canChange) : ?>
                     <?php if ($saveOrder) :?>
                        <?php if ($listDirn == 'asc') : ?>
                           <span><?php echo $this->pagination->orderUpIcon($i, true, 'emails.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                           <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'emails.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                        <?php elseif ($listDirn == 'desc') : ?>
                           <span><?php echo $this->pagination->orderUpIcon($i, true, 'emails.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                           <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'emails.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                        <?php endif; ?>
                     <?php endif; ?>
                     <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
                     <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
                  <?php else : ?>
                     <?php echo $item->ordering; ?>
                  <?php endif; ?>
               </td>
               <?php } ?>

               <td>
                  <?php echo $item->id; ?>
               </td>
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