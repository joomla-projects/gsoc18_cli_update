<?php
/*
 *
 * @Version       $Id: edit25_attachment.php 2167 2016-01-01 16:41:39Z geoffc $
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

// Create shortcut to parameters.
$parameters = $this->state->get('params');

$uploadLimit = $parameters->get('max_file_size', 1);

?>

<fieldset>
   <legend>
      <?php echo JText::_('COM_ISSUETRACKER_ATTACHMENTS_LEGEND'); ?>
   </legend>
   <label for="attachedfile" class="control-label"><?php echo JText::_('COM_ISSUETRACKER_LBL_ATTACHMENT'); ?></label>
   <div class="controls">
      <input type="file" id="attachedfile" name="attachedfile" size="40" />
      <span class="help-block">
         <?php echo JText::sprintf('COM_ISSUETRACKER_LBL_ATTACHMENT_MAXSIZE', $uploadLimit); ?>
      </span>
   </div>
</fieldset>

