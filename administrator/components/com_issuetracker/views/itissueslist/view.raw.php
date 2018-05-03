<?php
/*
 *
 * @Version       $Id: view.raw.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.4
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * View class for a list of issues.
 *
 * @package     Joomla.Administrator
 * @subpackage  Issue Tracker
 * @since       1.6
 */
class IssuetrackerViewItissueslist extends JViewLegacy
{
   /**
    * Display the view
    *
    * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
    *
    * @return bool|mixed
    */
   public function display($tpl = null)
   {
      $basename      = $this->get('BaseName');
      $filetype      = $this->get('FileType');
      $mimetype      = $this->get('MimeType');
      $content    = $this->get('Content');

      // Check for errors.
      if (count($errors = $this->get('Errors')))
      {
         JError::raiseError(500, implode("\n", $errors));

         return false;
      }

      $document = JFactory::getDocument();
      $document->setMimeEncoding($mimetype);
      JFactory::getApplication()->setHeader('Content-disposition', 'attachment; filename="' . $basename . '.' . $filetype . '"; creation-date="' . JFactory::getDate()->toRFC822() . '"', true);
      echo $content;

      return true;
   }
}
