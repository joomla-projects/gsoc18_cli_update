<?php
/*
 *
 * @Version       $Id: default_custom.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.1
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
defined('_JEXEC') or die('Restricted Access');

$user       = JFactory::getUser();
$canEdit    = $user->authorise('core.edit',        'com_issuetracker');
$canChange  = $user->authorise('core.edit.state',  'com_issuetracker');

// Get custom group name for the display.
$gname = $this->GetCustomGroupName($this->data->related_project_id);
// echo "<pre>"; var_dump($this->custom); echo "</pre>";

?>
<fieldset>
   <legend><?php echo $gname; ?></legend>
   <dl>
      <?php foreach($this->custom as $extraField): ?>
         <?php if($extraField->type == 'header'): ?>
            <dt>
               <h4 class="ExtraFieldHeader">
                  <?php echo $extraField->name; ?>
               </h4>
            </dt>
            <dd></dd>
            <div class="clearfix"></div>
         <?php else: ?>
            <dt>
               <?php echo $extraField->name; ?>
            </dt>
            <dd class="dl-horizontal">
               <?php echo $extraField->element; ?>
            </dd>
         <?php endif; ?>
      <?php endforeach; ?>
   </dl>
</fieldset>
