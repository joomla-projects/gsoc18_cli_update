<?php
/*
 *
 * @Version       $Id: icon.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.4
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Content Component HTML Helper
 *
 * @static
 * @package    Joomla.Site
 * @subpackage com_issuetracker
 * @since 1.5
 *
 */
class JHtmlIcon
{
   /**
    * @param       $issue
    * @param       $params
    * @param array $attribs
    * @param bool  $legacy
    * @return string
    */
   static function create($issue, $params, $attribs = array(), $legacy = false)
   {
      $jversion = new JVersion();
      if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         JHtml::_('bootstrap.tooltip');
      }

      $uri = JFactory::getURI();

      $url = 'index.php?option=com_issuetracker&task=itissues.add&return='.base64_encode($uri).'&a_id=0';

      if ($params->get('show_icons') || $params->get('showl_icons')) {
         if ($legacy || version_compare( $jversion->getShortVersion(), '3.0', 'lt' )) {
            $text = JHtml::_('image', 'system/new.png', JText::_('JNEW'), null, true);
         } else {
            $text = '<span class="icon-plus"></span>&#160;' . JText::_('JNEW') . '&#160;';
         }
      } else {
         $text = JText::_('JNEW').'&#160;';
      }

      if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         // Add the button classes to the attribs array   This all for J.3.x only
         if (isset($attribs['class'])) {
            $attribs['class'] = $attribs['class'] . ' btn btn-primary';
         } else {
//            $attribs['class'] = 'btn btn-primary';
         }
      }

      $button =  JHtml::_('link',JRoute::_($url), $text);

      if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         $output = JHtml::_('link', JRoute::_($url), $text, $attribs);
      } else {
         $button  = JHtml::_('link',JRoute::_($url), $text);
         $output  = '<span class="hasTip" title="'.JText::_('COM_ISSUETRACKER_CREATE_ISSUE').'">'.$button.'</span>';
      }
      return $output;
   }

   /**
    * @param $issue
    * @param $params
    * @param array $attribs
    * @param bool $legacy
    * @return mixed
    */
   static function email($issue, $params, $attribs = array(), $legacy = false)
   {
      require_once(JPATH_SITE . '/components/com_mailto/helpers/mailto.php');
      // Added to resolve helper call.
      require_once(JPATH_SITE . '/components/com_issuetracker/helpers/route.php');

      $uri        = JURI::getInstance();
      $base       = $uri->toString(array('scheme', 'host', 'port'));
      $template   = JFactory::getApplication()->getTemplate();
      if (isset($issue->id)) {
         $link = $base.JRoute::_(IssueTrackerHelperRoute::getIssueRoute($issue->id) , false);
      } else {
         $link = $base.JRoute::_(IssueTrackerHelperRoute::getIssueRoute("") , false);
      }
      $url     = 'index.php?option=com_mailto&tmpl=component&template='.$template.'&link='.MailToHelper::addLink($link);

      $status  = 'width=400,height=350,menubar=yes,resizable=yes';

      if ($params->get('show_icons') || $params->get('showl_icons')) {
         $jversion = new JVersion();
         if ($legacy  || version_compare( $jversion->getShortVersion(), '3.0', 'lt' )) {
            $text = JHtml::_('image', 'system/emailButton.png', JText::_('JGLOBAL_EMAIL'), null, true);
         } else {
            $text = '<span class="icon-envelope"></span>&#160;' . JText::_('JGLOBAL_EMAIL').'&#160;';
         }
      } else {
         $text = '&#160;'.JText::_('JGLOBAL_EMAIL');
      }

      $attribs['title'] = JText::_('JGLOBAL_EMAIL');
      $attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";

      $output = JHtml::_('link',JRoute::_($url), $text, $attribs);
      return $output;
   }

   /**
    * Display a delete icon for the item.
    *
    * This icon will not display in a popup window, nor if the article is trashed.
    * Edit access checks must be performed in the calling code.
    *
    * @param   object $issue The item in question.
    * @param   object $params The item parameters
    * @param array    $attribs
    * @param bool     $legacy
    * @internal param array $attribs Not used??
    *
    * @return  string   The HTML for the article edit icon.
    * @since   1.6
    */
   static function delete($issue, $params, $attribs = array(), $legacy = false)
   {
      // Ignore if the state is negative (trashed).
      if ($issue->state < 0) {
         return false;
      }

      // $uri = JFactory::getURI();
      // Need the calling URI here not the current one!
      $retval  = JFactory::getApplication()->input->get('return','not_set');
      if ( isset($_SERVER['HTTP_REFERER'])) {
         $referer = $_SERVER['HTTP_REFERER'];
      } else {
         $referer =JURI::base();
      }
      if ($retval == "not_set") {
         $uri = $referer;
      } else {
         $uri = $retval;
      }

      $url = 'index.php?option=com_issuetracker&task=itissues.delete&return='.base64_encode($uri).'&a_id='.$issue->id;

      $jversion = new JVersion();
      if ($params->get('show_icons') || $params->get('showl_icons')) {
         if ($legacy || version_compare( $jversion->getShortVersion(), '3.0', 'lt' )) {
            $text = JHtml::image('media/com_issuetracker/images/16/delete.png', JText::_('JACTION_DELETE'));
         } else {
            // TODO Check icon-delete class.
            $text = '<span class="icon-remove"></span>&#160;' . JText::_('JACTION_DELETE'). '&#160;';
         }
      } else {
         $text = '&#160;'.JText::_('JACTION_DELETE');
      }

      $button =  JHtml::_('link',JRoute::_($url), $text);

      if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         $output = JHtml::_('link', JRoute::_($url), $text, $attribs);
      } else {
         $button  = JHtml::_('link',JRoute::_($url), $text);
         $output  = '<span class="hasTip" title="'.JText::_('COM_ISSUETRACKER_DELETE_ISSUE').'">'.$button.'</span>';
      }

      return $output;
   }


   /**
    * Display an edit icon for the item.
    *
    * This icon will not display in a popup window, nor if the article is trashed.
    * Edit access checks must be performed in the calling code.
    *
    * @param   object $issue The item in question.
    * @param   object $params The item parameters
    * @param   array $attribs Not used??
    *
    * @param bool $legacy
    * @return  string   The HTML for the article edit icon.
    * @since   1.6
    */
   static function edit($issue, $params, $attribs = array(), $legacy = false)
   {
      // Initialise variables.
      $user    = JFactory::getUser();
      // $userId  = $user->get('id');
      $uri     = JFactory::getURI();

      // Ignore if in a popup window.
      if ($params && $params->get('popup')) {
         return false;
      }

      // Ignore if the state is negative (trashed).
      if ($issue->state < 0) {
         return false;
      }

      // Ignore if we are not permitting front end editing
      if ($params->get('allow_fe_edit') == 0 ) {
         return false;
      }

      $jversion = new JVersion();
      if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         JHtml::_('bootstrap.tooltip');
      } else {
         JHtml::_('behavior.tooltip');
      }

      // Show checked_out icon if the article is checked out by a different user
      if (property_exists($issue, 'checked_out') && property_exists($issue, 'checked_out_time') && $issue->checked_out > 0 && $issue->checked_out != $user->get('id')) {
         $checkoutUser  = JFactory::getUser($issue->checked_out);
         $button        = JHtml::_('image','system/checked_out.png', NULL, NULL, true);
         $date          = JHtml::_('date',$issue->checked_out_time);
         $tooltip       = JText::_('JLIB_HTML_CHECKED_OUT').' :: '.JText::sprintf('COM_ISSUETRACKER_CHECKED_OUT_BY', $checkoutUser->name).' <br /> '.$date;
         return '<span class="hasTip" title="'.htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8').'">'.$button.'</span>';
      }

      // $url     = 'index.php?option=com_issuetracker&task=itissues.edit&a_id='.$issue->id.'&return='.base64_encode($uri);
      $url     = 'index.php?option=com_issuetracker&view=form&layout=edit&a_id='.$issue->id.'&return='.base64_encode($uri);

      if ($issue->state == 0) {
         $overlib = JText::_('JUNPUBLISHED');
      } else {
         $overlib = JText::_('JPUBLISHED');
      }

      $date    = JHtml::_('date',$issue->created_on);
      // $author = $issue->created_by ? $issue->created_by : $issue->author;
      $db   = JFactory::getDBO();
      $sql = "SELECT person_name FROM ".$db->quoteName('#__it_people')." WHERE id=" . $db->Quote($issue->identified_by_person_id);
      $db->setQuery( $sql);
      $iname = $db->loadResult();
      $author = $iname ? $iname : $issue->created_by;

      $overlib .= '&lt;br /&gt;';
      $overlib .= $date;
      $overlib .= '&lt;br /&gt;';
      $overlib .= JText::sprintf('COM_ISSUETRACKER_CREATED_BY_NAME',htmlspecialchars($author, ENT_COMPAT, 'UTF-8'));

      if ($legacy || version_compare( $jversion->getShortVersion(), '3.0', 'lt' )) {
         $icon = $issue->state ? 'edit.png' : 'edit_unpublished.png';
         $text = JHtml::_('image', 'system/' . $icon, JText::_('JGLOBAL_EDIT'), null, true);
      } else {
         $icon = $issue->state ? 'edit' : 'eye-close';
         $text = '<span class="hasTooltip icon-' . $icon . ' tip" title="' . JHtml::tooltipText(JText::_('COM_ISSUETRACKER_EDIT_ISSUE'), $overlib, 0) . '"></span>&#160;' . JText::_('JGLOBAL_EDIT') . '&#160;';
      }

      if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         $output = JHtml::_('link', JRoute::_($url), $text, $attribs);
      } else {
         $button  = JHtml::_('link',JRoute::_($url), $text);
         $output  = '<span class="hasTip" title="'.JText::_('COM_ISSUETRACKER_EDIT_ISSUE').' :: '.$overlib.'">'.$button.'</span>';
      }

      return $output;
   }


   /**
    * @param $id
    * @param $params
    * @param array $attribs
    * @param bool $legacy
    * @return mixed
    */
   static function print_popup($id, $params, $attribs = array(), $legacy = false)
   {
      $app     = JFactory::getApplication();
      $input   = $app->input;
      $request = $input->request;

      $url     = '&tmpl=component&print=1&layout=default&page='.@ $request->limitstart;
      $status  = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

      // checks template image directory for image, if non found default are loaded
      if ($params->get('show_icons') || $params->get('showl_icons')) {
         $jversion = new JVersion();
         if ($legacy || version_compare( $jversion->getShortVersion(), '3.0', 'lt' ) ) {
            $text = JHtml::_('image', 'system/printButton.png', JText::_('JGLOBAL_PRINT'), null, true);
         } else {
            $text = '<span class="icon-print"></span>&#160;' . JText::_('JGLOBAL_PRINT') . '&#160;';
         }
      } else {
         $text = JText::_('JGLOBAL_PRINT');
      }

      $attribs['title'] = JText::_('JGLOBAL_PRINT');
      $attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
      $attribs['rel']      = 'nofollow';

      return JHtml::_('link',JRoute::_($url), $text, $attribs);
   }

   /**
    * @param $issue
    * @param $params
    * @param array $attribs
    * @param bool $legacy
    * @return string
    */
   static function print_screen($issue, $params, $attribs = array(), $legacy = false)
   {
      // checks template image directory for image, if non found default are loaded
      if ($params->get('show_icons') || $params->get('showl_icons') ) {
         $jversion = new JVersion();
         if ($legacy || version_compare( $jversion->getShortVersion(), '3.0', 'lt' )) {
            $text = JHtml::_('image', 'system/printButton.png', JText::_('JGLOBAL_PRINT'), null, true);
         } else {
            $text = '<span class="icon-print"></span>&#160;' . JText::_('JGLOBAL_PRINT') . '&#160;';
         }
      } else {
         $text = JText::_('JGLOBAL_PRINT');
      }
      return '<a href="#" onclick="window.print();return false;">'.$text.'</a>';
   }
}