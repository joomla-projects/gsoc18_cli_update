<?php
/*
 *
 * @Version       $Id: edit_header_alias.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.4
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
defined('_JEXEC') or die( 'Restricted access' );

$form = $this->getForm();
$this->form->setFieldAttribute('alias', 'type',     'text');
$this->form->setFieldAttribute('alias', 'disabled', 'false');
$this->form->setFieldAttribute('alias', 'required', 'false');
$this->form->setFieldAttribute('alias', 'readonly', 'true');

$alias = $this->item->alias;
?>
<div class="form-inline form-inline-header">
   <?php if (!empty($alias) ) { ?>
      <div class="control-label">
         <?php echo $form->getLabel('alias'); ?>
      </div>
      <div class="controls">
         <?php echo $form->getInput('alias'); ?>
      </div>
   <?php } ?>
</div>
