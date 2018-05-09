<?php
/*
 *
 * @Version       $Id: default25_credits.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.8
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

defined('_JEXEC') or die;
$db   = JFactory::getDBO();
$sql  = "SELECT version FROM ".$db->quoteName('#__it_meta')." WHERE type='component'";
$db->setQuery( $sql);
$version = $db->loadResult();
?>
<div style="text-align:center">`
   <div style="margin: 10px 0 0 0">
      <h1><?php echo JText::_('COM_ISSUETRACKER'); ?></h1>
   </div>

   <div>
      <h2><?php echo JText::_('COM_ISSUETRACKER_VERSION') . " " . $version; ?></h2>
   </div>

   <div>
      <h3><?php echo 'Translation Credits'; ?></h3>
   </div>

   <iframe style="overflow:hidden;height:1550px;width:100%;" frameborder="0" height="100%" width="100%" src="http://macrotoneconsulting.co.uk/index.php?option=com_content&view=article&tmpl=component&id=117"></iframe>

   <br />
   <?php echo JText::_('COM_ISSUETRACKER_CREDIT_TEXT2'); ?>
   <br /><br />
   <?php echo JText::_('COM_ISSUETRACKER_CREDIT_TEXT3'); ?>
</div>