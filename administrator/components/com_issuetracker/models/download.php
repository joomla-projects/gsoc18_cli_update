<?php
/*
 *
 * @Version       $Id: download.php 2167 2016-01-01 16:41:39Z geoffc $
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

jimport('joomla.application.component.modelform');

/**
 * Issuetracker Download model.
 *
 *
 * @package       Joomla.Components
 * @subpackage    Issuetracker
 */
class IssueTrackerModelDownload extends JModelForm
{
   protected $_context = 'com_issuetracker.issues';

   /**
    * Auto-populate the model state.
    *
    * Note. Calling getState in this method will result in recursion.
    *
    * @return  void
    *
    * @since   1.6
    */
   protected function populateState()
   {
      $input = JFactory::getApplication()->input;

      $basename = $input->cookie->getString(JApplication::getHash($this->_context . '.basename'), '__SITE__');
      $this->setState('basename', $basename);

      $compressed = $input->cookie->getInt(JApplication::getHash($this->_context . '.compressed'), 1);
      $this->setState('compressed', $compressed);
   }

   /**
    * Method to get the record form.
    *
    * @param   array    $data      Data for the form.
    * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
    *
    * @return  mixed  A JForm object on success, false on failure
    *
    * @since   1.6
    */
   public function getForm($data = array(), $loadData = true)
   {
      // Get the form.
      $form = $this->loadForm('com_issuetracker.download', 'download', array('control' => 'jform', 'load_data' => $loadData));

      if (empty($form))
      {
         return false;
      }

      return $form;
   }

   /**
    * Method to get the data that should be injected in the form.
    *
    * @return  mixed  The data for the form.
    *
    * @since   1.6
    */
   protected function loadFormData()
   {
      $data = array(
         'basename'     => $this->getState('basename'),
         'compressed'   => $this->getState('compressed')
      );

      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {

         $this->preprocessData('com_issuetracker.download', $data);

         return $data;
      } else {
         return array(
            'basename'     => $this->getState('basename'),
            'compressed'   => $this->getState('compressed')
         );

      }
   }
}
