<?php
/*
 *
 * @Version       $Id: view.html.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.4
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

defined('_JEXEC') or die('Restricted access');

/**
 * View class for download a list of issues.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_issuetracker
 * @since       1.6
 */
class IssuetrackerViewDownload extends JViewLegacy
{
   protected $form;

   /**
    * Display the view
    *
    * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
    *
    * @return bool|mixed
    */
   public function display($tpl = null)
   {
      $this->form = $this->get('Form');

      // Check for errors.
      if (count($errors = $this->get('Errors')))
      {
         JError::raiseError(500, implode("\n", $errors));

         return false;
      }

      return parent::display($tpl);

   }
}