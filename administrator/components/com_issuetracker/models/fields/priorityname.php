<?php
/*
 *
 * @Version       $Id: priorityname.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.4.0
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
defined('_JEXEC') or die('Restricted access');

if(!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

JLoader::import('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldPriorityname extends JFormField
{
   /**
    * The form field type.
    *
    * @var     string
    * @since   1.6
    */
   protected $type = 'priorityname';

   /**
    * Method to get the field input markup.
    *
    * @return  string   The field input markup.
    * @since   1.6
    */
   protected function getInput()
   {
    // Initialize variables.
      // $text = '';

      $db      = JFactory::getDbo();
      $query   = $db->getQuery(true);

      $query->select('priority_name As text');
      $query->from('#__it_priority a');
      $query->where('id = '.$this->value);

      // Get the options.
      $db->setQuery($query);

      $text = $db->loadResult();

      return $text;

   }
}