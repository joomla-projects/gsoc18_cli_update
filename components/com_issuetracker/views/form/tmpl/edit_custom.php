<?php
/*
 *
 * @Version       $Id: edit_custom.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.8
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

// Create shortcut to parameters.
$parameters = $this->state->get('params');

// If a guest set general defaults otherwise set user defaults.
if ( $user->guest ) {
   $def_proj = $parameters->get('def_project', 10);
} else {
   // Get users default project
   $def_proj = IssueTrackerHelperSite::getUserdefproj($user->id);
}

$pid = $this->item->related_project_id;
if (empty($pid)) $pid = $def_proj;;
// if (empty($this->custom)) return;

// Add checks for user access groups on custom fields.
$user       = JFactory::getUser();
// $userGroups = JAccess::getGroupsByUser($user->get('id'));
$levels = $user->getAuthorisedViewLevels();

?>
<fieldset class="adminform">
   <div id="customTab">
      <div id="customFieldsContainer">
         <?php if (!empty($this->custom)) : ?>
            <div class="admintable table" id="extraFields">
               <?php foreach($this->custom as $extraField): ?>
                  <?php if ( in_array($extraField->access, $levels) ) { ?>
                     <?php if($extraField->type == 'header'): ?>
                        <div class="formelm">
                           <legend><?php echo $extraField->name; ?></legend>
                        </div>
                     <?php else: ?>
                        <div class="formelm">
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
                        </div>
                        <?php if($extraField->type == 'radio'): ?>
                           <fieldset class="radio" style="vertical-align:top; text-align:left;">
                              <?php echo str_replace('</label>','</label><br />',$extraField->element); ?>
                           </fieldset>
                        <?php elseif($extraField->type == 'multipleCheckbox'): ?>
                           <fieldset class="checkbox" style="vertical-align:top; text-align:left;">
                              <?php echo str_replace('</option>','</option><br />',$extraField->element); ?>
                           </fieldset>
                        <?php else: ?>
                           <?php echo $extraField->element; ?>
                        <?php endif; ?>
                     <?php endif; ?>
                  <?php } ?>
               <?php endforeach; ?>
            </div>
         <?php endif; ?>
      </div>
   </div>
</fieldset>