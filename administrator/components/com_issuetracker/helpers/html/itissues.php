<?php
/**
 * @Version       $Id: itissues.php 1857 2014-11-04 20:19:10Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.5
 * @Copyright     Copyright (C) 2011 - 2014 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2014-11-04 20:19:10 +0000 (Tue, 04 Nov 2014) $
 *
 */

defined('_JEXEC') or die;

/**
 * Extended Utility class for the Issue Tracker component.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_issuetracker
 * @since       2.5
 */
class JHtmlIssueTracker
{
   /**
    * Displays an icon to link to a view for an issue.
    *
    * @param   integer  $issueId  The issue ID
    *
    * @return  string  A link to view an issue.
    *
    * @since   2.5
    */
   public static function viewIssue($issueId)
   {
      // $title = JText::_('COM_ISSUETRACKER_VIEW_ISSUE');

      return '<a href="' . JRoute::_('index.php?option=com_issuetracker&view=itissues&id=' . (int) $issueId) . '">'
             . JHtml::image('media/com_issuetracker/images/16/view.png', JText::_('COM_ISSUETRACKER_VIEW_ISSUE')) . '</a>';
    }

   /**
    * Displays an icon to link to a view for an issue.
    *
    * @param $aId
    * @internal param int $issueId The issue ID
    *
    * @return  string  A link to view an issue.
    *
    * @since   2.5
    */
   public static function viewAttachment($aId)
   {
      // $title = JText::_('COM_ISSUETRACKER_VIEW_ISSUE');

      return '<a href="' . JRoute::_('index.php?option=com_issuetracker&task=attachment.read&id=' . (int) $aId) . '">'
             . JHtml::image('media/com_issuetracker/images/16/view.png', JText::_('COM_ISSUETRACKER_VIEW_ATTACHMENT')) . '</a>';
    }

}