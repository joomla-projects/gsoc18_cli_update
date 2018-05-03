<?php
/*
 *
 * @Version       $Id: view.html.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.8
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::import( 'joomla.application.component.view' );

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

/**
 * Issue Tracker view
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerViewItissues extends JViewLegacy
{
   protected $state;
   protected $item;
   protected $form;
   protected $attachment;

   /**
    * Display the view
    * @param null $tpl
    * @return mixed
    */
   public function display($tpl = null)
   {
      $this->state   = $this->get('State');
      $this->item    = $this->get('Item');
      $this->form    = $this->get('Form');
      $this->params  = JComponentHelper::getParams( 'com_issuetracker' );

      if ( $this->item->id != 0 ) {
         $this->attachment    = $this->check_attachments();
         $this->progress   = $this->get_progress_info();
      } else {
         $date = JFACTORY::getdate();
         $cdate = $date->toSQL();
         $this->form->setFieldAttribute('identified_date', 'default',  $cdate);
      }

      // if ( $this->item->id == 0 ) {
         // Modify progress defaults if required.
         $def_pstate = $this->params->get('def_pstate',0);
         $this->form->setFieldAttribute('pstate', 'default',  $def_pstate);
         $def_progresspublic = $this->params->get('def_progresspublic',1);
         $this->form->setFieldAttribute('progresspublic', 'default',  $def_progresspublic);
         $def_paccess = $this->params->get('def_paccess',2);
         $this->form->setFieldAttribute('paccess', 'default',  $def_paccess);
      // }

      // Extract custom fields for views
      JLoader::import('customfield',JPATH_COMPONENT_ADMINISTRATOR.'/'.'models');
      $cfmodel    = JModelLegacy::getInstance('customfield', 'IssuetrackerModel');

      $this->params = JComponentHelper::getParams( 'com_issuetracker' );
      $def_project = $this->params->get('def_project', 0);

      if (isset($this->item->related_project_id) ) {
         $projid = $this->item->related_project_id;
      } else {
         $projid = $def_project;
      }

      // Back end no access control and only published custom fields.
      $pstate = 1;
      $astate = 0;
      $dmode  = 0;
      $this->custom = $cfmodel->check_customfields($projid, $this->item->id, $pstate, $astate, $dmode);

      $this->canDo   = IssueTrackerHelper::getActions($this->item->id);

      // Check for errors.
      if (count($errors = $this->get('Errors'))) {
         JError::raiseError(500, implode("\n", $errors));
         return false;
      }

      $this->addToolbar();
      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         $this->setLayout("edit");
      } else {
         $this->setLayout("edit25");
      }

      // Add in path to common audit templates
      $this->_addPath( 'template', JPATH_COMPONENT_ADMINISTRATOR . DS . 'views' . DS . 'common' . DS . 'tmpl' );

      return parent::display($tpl);
   }


   /**
    * Check if the field is a required value.
    * @param $value
    * @return bool
    */
   function checkrequired($value)
   {
      $required = false;
      // Expand out the field and check the elements.
      $defs = json_decode($value);
      foreach ($defs as $val) {
         if ( isset($val->required) && $val->required == 1 ) {
            $required = true;
         }
      }
      return $required;
  }

   /**
    * Check if any attachments and get details.
    * This code should be in the model. Move when convenient.
    */

   function check_attachments()
   {
      $issue_id = $this->item->alias;
      // print("Issue No = $issue_id<p>");

      $db = JFactory::getDbo();
      $query = "SELECT count(*) FROM `#__it_attachment` WHERE issue_id = '".$issue_id."' AND state = 1 ";
      $db->setQuery($query);
      $cnt = $db->loadResult();

      if ( $cnt == 0 ) {
         return false;
      } else {
         $query = "SELECT * FROM `#__it_attachment` WHERE issue_id = '".$issue_id."' AND state = 1 ";
         $db->setQuery($query);
         $attachment = $db->loadObjectList();
         return $attachment;
      }
   }

   /**
    * Get progress information from separate table.
    * This code should be in the model. Move when convenient.
    */
   function get_progress_info()
   {
      $issue_id = $this->item->alias;
      // print("Issue No = $issue_id<p>");

      $db = JFactory::getDbo();
      $query = "SELECT count(*) FROM `#__it_progress` WHERE alias = '".$issue_id."'";
      $db->setQuery($query);
      $cnt = $db->loadResult();

      if ( $cnt == 0 ) {
         return false;
      } else {
         // $query = "SELECT * FROM `#__it_progress` WHERE alias = '".$issue_id."'";
         $query   = $db->getQuery(true);

         // Select the required fields from the table.
         $query->select('a.*');

         $query->from('`#__it_progress` AS a');

         // Join over the asset groups.
         $query->select('ag.title AS access_level');
         $query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

         $query->where('alias = "'.$issue_id.'"');

         $db->setQuery($query);
         $progress = $db->loadObjectList();
         return $progress;
      }
   }

   /**
    * Add the page title and toolbar.
    */
   protected function addToolbar()
   {
      require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'issuetracker.php';

      // JRequest::setVar('hidemainmenu', true);
      JFactory::getApplication()->input->set('hidemainmenu', true);

      $user    = JFactory::getUser();
      $isNew      = ($this->item->id == 0);
      if (isset($this->item->checked_out)) {
         $checkedOut   = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
      } else {
         $checkedOut = false;
      }
      $canDo      = IssueTrackerHelper::getActions();
      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);

      // JToolBarHelper::title(JText::_('COM_ISSUETRACKER'), 'type.png');
      $text = $isNew ? JText::_( 'NEW' ) : JText::_( 'EDIT' );
      JToolBarHelper::title(   JText::_( 'COM_ISSUETRACKER' ).': <small>[ ' . $text.' ]</small>', 'issues-add' );

      // If not checked out, can save the item.
      if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
      {
         JToolBarHelper::apply('itissues.apply', 'JTOOLBAR_APPLY');
         JToolBarHelper::save('itissues.save', 'JTOOLBAR_SAVE');
      }
      if (!$checkedOut && ($canDo->get('core.create'))){
         JToolBarHelper::custom('itissues.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
      }
      // If an existing item, can save to a copy.
      if (!$isNew && $canDo->get('core.create')) {
         JToolBarHelper::custom('itissues.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
      }
      if (empty($this->item->id)) {
         JToolBarHelper::cancel('itissues.cancel', 'JTOOLBAR_CANCEL');
      }
      else {
         JToolBarHelper::cancel('itissues.cancel', 'JTOOLBAR_CLOSE');
      }
   }

   /**
    * Get the custom fields group name for any project.
    * @param $pid
    * @return mixed|null|string
    */
   function getCustomGroupName($pid)
   {
      $gname = null;
      if ( !empty($pid) ) {
         $db = JFactory::getDBO();
         $query  = 'SELECT name FROM `#__it_custom_field_group` AS cfg ';
         $query .= ' LEFT JOIN `#__it_projects` AS p ';
         $query .= ' ON p.customfieldsgroup = cfg.id ';
         $query .= ' WHERE p.id = '.$pid;
         $db->setQuery( $query );
         $gname = $db->loadResult();
      }

      if (empty($gname) || $gname == '')
         $gname = JText::_( 'COM_ISSUETRACKER_CUSTOMFIELDS' );
      return $gname;
   }
}