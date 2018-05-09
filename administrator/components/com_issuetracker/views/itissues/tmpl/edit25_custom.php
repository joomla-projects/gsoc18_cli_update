<?php
/*
 *
 * @Version       $Id: edit25_custom.php 2167 2016-01-01 16:41:39Z geoffc $
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

$user = JFactory::getUser();
$canEdit    = $user->authorise('core.edit',        'com_issuetracker');
$canChange  = $user->authorise('core.edit.state',  'com_issuetracker');

// echo "<pre>"; var_dump($this->custom); echo "</pre>";
?>
<fieldset class="adminform">
   <legend><?php echo JText::_( 'COM_ISSUETRACKER_CUSTOMFIELDS' ); ?></legend>

   <div class="simpleTabsContent" id="customTab">
      <div id="customFieldsContainer">
         <ul class="adminformlist" id="customFields">
            <?php if (!empty($this->custom) ) foreach($this->custom as $extraField): ?>
               <?php if($extraField->type == 'header') { ?>
                  <li>
                     <h4 class="CustomFieldHeader">
                        <?php echo $extraField->name; ?>
                     </h4>
                  </li>
                  <div class="clr"></div>
               <?php } else { ?>
                  <li>
                     <?php
                        $required = false;
                        $required = $this->checkrequired($extraField->value);
                        if (!empty($extraField->tooltip)) {
                           if ($required ) {
                              $classtext = "hasTooltip required";
                           } else {
                              $classtext = "hasTooltip";
                           }
                        } else {
                           if ($required ) {
                              $classtext = "required";
                           } else {
                              $classtext = "";
                           }
                        }
                     ?>
                     <label for="CustomField_<?php echo $extraField->id; ?>" id="ITCustomField_<?php echo $extraField->id.'-id'; ?>" <?php if (!empty($classtext)) echo 'class="'.$classtext.'" title="'.$extraField->tooltip.'"'?>><?php echo $extraField->name; ?><?php if ($required ) echo '<span class="star">&#160;*</span>'; ?></label>
                     <?php if($extraField->type == 'radio'): ?>
                        <fieldset class="radio" style="vertical-align:top; text-align:left;">
                           <?php echo str_replace('</label>','</label><br />',$extraField->element); ?>
                        </fieldset>
                     <?php else: ?>
                        <?php echo $extraField->element; ?>
                     <?php endif; ?>
                  </li>
                  <div class="clr"></div>
               <?php } ?>
            <?php endforeach; ?>
         </ul>
      </div>
   </div>
</fieldset>