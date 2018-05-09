<?php
/*
 *
 * @Version       $Id: dbtasks.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.4.2
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

/**
 * Class IssueTrackerControllerDbtasks
 */
class IssueTrackerControllerDbtasks extends JControllerAdmin
{
   /**
    * @param array $config
    */
   public function __construct($config = array())
   {
      parent::__construct($config);

      $this->modelName = 'dbtasks';

      // Register Extra tasks
      $this->registerTask('addsampledata', 'addsampledata');
      $this->registerTask('remsampledata', 'remsampledata');
      $this->registerTask('syncusers', 'syncusers');
      // $this->registerTask('gentriggers', 'gentriggers');
   }

   /**
    * Executes a given controller task. The onBefore<task> and onAfter<task>
    * methods are called automatically if they exist.
    *
    * @param string $task
    * @return null|bool False on execution failure
    */
   public function _execute($task)
   {
/*
      $method_name = 'onBefore'.ucfirst($task);
      if(method_exists($this, $method_name)) {
         $result = $this->$method_name();
         if(!$result) return false;
      }
*/
      // Do not allow the display task to be directly called
      $task = strtolower($task);
      if (isset($this->taskMap[$task])) {
         $doTask = $this->taskMap[$task];
      }
      elseif (isset($this->taskMap['__default'])) {
         $doTask = $this->taskMap['__default'];
      }
      else {
         $doTask = null;
      }
      if($doTask == 'display') {
         JError::raiseError(400, 'Bad Request');
      }

      parent::execute($task);
/*
      $method_name = 'onAfter'.ucfirst($task);
      if(method_exists($this, $method_name)) {
         $result = $this->$method_name();
         if(!$result) return false;
      }
 */
 }

   /**
    * @param string $task
    * @return mixed|void
    */
   public function execute($task)
   {
      if(!in_array($task, array('addsampledata','remsampledata','syncusers','gentriggers'))) $task = 'browse';
      // die("Execute task $task<p>");
      $this->_execute($task);
   }

   public function browse()
   {
/*
      $model = $this->getThisModel();
      // $from = JRequest::getString('from',null);
      $from = JFactory::getApplication()->input->getString('from', null');


      $tables = (array)$model->findTables();
      $lastTable = $model->repairAndOptimise($from);
      if(empty($lastTable))
      {
         $percent = 100;
      }
      else
      {
         $lastTableID = array_search($lastTable, $tables);
         $percent = round(100 * ($lastTableID+1) / count($tables));
         if($percent < 1) $percent = 1;
         if($percent > 100) $percent = 100;
      }

      $this->getThisView()->assign('table',     $lastTable);
      $this->getThisView()->assign('percent',      $percent);

      $model->setState('lasttable', $lastTable);
      $model->setState('percent', $percent);
*/
      // print("Dummy routine");
      $this->display(false);
   }

   public function addsampledata()
   {
      $model = $this->getModel('dbtasks');
/*
      if ($model->addsampledata()) {
         $msg = JText::_( 'COM_ISSUETRACKER_SDATA_ADDED' );
      } else {
         $msg = JText::_( 'COM_ISSUETRACKER_ERROR_ADDING_SAMPLEDATA' );
      }
*/
      // print("Add Sample Data procedure");

      $this->setRedirect('index.php?option=com_issuetracker');
   }

   public function remsampledata()
   {
      $model = $this->getModel('dbtasks');
      // print("Remove sample data procedure  $model");
/*
      if ($model->remsampledata()) {
         $msg = JText::_( 'COM_ISSUETRACKER_SDATA_REMOVED' );
      } else {
         $msg = JText::_( 'COM_ISSUETRACKER_ERROR_REMOVING_SDATA' );
      }
*/
      $this->setRedirect('index.php?option=com_issuetracker');
   }

   public function syncusers()
   {
      $model = $this->getModel('dbtasks');
      // print("Synchronise with Joomla Users procedure  $model");
      $model->syncusers();
      $this->setRedirect('index.php?option=com_issuetracker',JText::_('COM_ISSUETRACKER_SYNCHRONISED'));
   }

/*
   public function gentriggers()
   {
      $model = $this->getModel('dbtasks');
      $model->gentriggers();
      $this->setRedirect('index.php?option=com_issuetracker',JText::_('COM_ISSUETRACKER_AUDIT_CHANGED'));
   }
*/
}
