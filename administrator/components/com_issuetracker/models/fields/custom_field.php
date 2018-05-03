<?php
/*
 *
 * @Version       $Id: custom_field.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.4.0
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
defined('JPATH_BASE') or die('Restricted access');

JLoader::import('joomla.html.html');
JLoader::import('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldCustom_field extends JFormField
{
   /**
    * The form field type.
    *
    * @var     string
    * @since   1.6
    */
   protected $type = 'text';

   /**
    * Method to get the field input markup.
    *
    * @return  string   The field input markup.
    * @since   1.6
    */
   protected function getInput()
   {
      // Initialize variables.
      $html = array();

      return implode($html);
   }
}