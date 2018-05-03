<?php
/*
 *
 * @Version       $Id: itissueslist.raw.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.5
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


/**
 * Tracks list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_issuetracker
 * @since       1.6
 */
class IssuetrackerControllerItissueslist extends JControllerLegacy
{
   /**
    * @var    string  The context for persistent state.
    *
    * @since  1.6
    */
   protected $context = 'com_issuetracker.itissueslist';

   /**
    * Proxy for getModel.
    *
    * @param   string  $name    The name of the model.
    * @param   string  $prefix  The prefix for the model class name.
    * @param   array   $config  Configuration array for model. Optional.
    *
    * @return  JModel
    *
    * @since   1.6
    */
   public function getModel($name = 'Itissueslist', $prefix = 'IssuetrackerModel', $config = array())
   {
      $model = parent::getModel($name, $prefix, array('ignore_request' => true));

      return $model;
   }

   /**
    * Display method for the raw issue data.
    *
    * @param   boolean  $cachable If true, the view output will be cached
    * @param array|bool $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}
    * .
    *
    * @return  JController  This object to support chaining.
    *
    * @since   1.5
    * @todo    This should be done as a view, not here!
    */
   public function display($cachable = false, $urlparams = false)
   {
      // Get the document object.
      $document   = JFactory::getDocument();
      $vName      = 'itissueslist';
      $vFormat = 'raw';

      // Get and render the view.
      if ($view = $this->getView($vName, $vFormat))
      {
         // Get the model for the view.
         $model = $this->getModel($vName);

         // Load the filter state.
         $app = JFactory::getApplication();

         $search = $app->getUserState($this->context.'.filter.search');
         $model->setState('filter.search', $search);

         $assigned = $app->getUserState($this->context.'.filter.assigned');
         $model->setState('filter.assigned', $assigned);

         $identifier = $app->getUserState($this->context.'.filter.identifier');
         $model->setState('filter.identifier', $identifier);

         $published = $app->getUserState($this->context.'.filter.state');
         $model->setState('filter.state', $published);

         $projectId = $app->getUserState($this->context.'.filter.project_id');
         $model->setState('filter.project_id', $projectId);

         $statusId = $app->getUserState($this->context.'.filter.status_id');
         $model->setState('filter.status_id', $statusId);

         $typeId = $app->getUserState($this->context.'.filter.type_id');
         $model->setState('filter.type_id', $typeId);

         $priorityId = $app->getUserState($this->context.'.filter.priority_id');
         $model->setState('filter.priority_id', $priorityId);

         $createdbyId = $app->getUserState($this->context.'.filter.created_by_id');
         $model->setState('filter.created_by', $createdbyId);
         $createdonId = $app->getUserState($this->context.'.filter.created_on_id');
         $model->setState('filter.created_on', $createdonId);
         $modifiedbyId = $app->getUserState($this->context.'.filter.modified_id');
         $model->setState('filter.modified_by', $modifiedbyId);
         $modifiedonId = $app->getUserState($this->context.'.filter.modified_on');
         $model->setState('filter.modified_on', $modifiedonId);

         $tag = $app->getUserState($this->context.'.filter.tag');
         $model->setState('filter.tag', $tag);

         $model->setState('list.limit', 0);
         $model->setState('list.start', 0);

//         $form = JRequest::getVar('jform');
         $form = $app->input->get('jform', '', 'array');
         $model->setState('basename', $form['basename']);
         $model->setState('compressed', $form['compressed']);

         $config = JFactory::getConfig();
         $cookie_domain = $config->get('cookie_domain', '');
         $cookie_path = $config->get('cookie_path', '/');

         setcookie(JApplication::getHash($this->context . '.basename'), $form['basename'], time() + 365 * 86400, $cookie_path, $cookie_domain);
         setcookie(JApplication::getHash($this->context . '.compressed'), $form['compressed'], time() + 365 * 86400, $cookie_path, $cookie_domain);

         // Push the model into the view (as default).
         $view->setModel($model, true);

         // Push document object into the view.
         $view->document = $document;

         $view->display();
      }
   }
}