<?php
/*
 *
 * @Version       $Id: itloglist.php 2167 2016-01-01 16:41:39Z geoffc $
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
 * Issuetracker Controller
 *
 * @package       Joomla.Components
 * @subpackage    Issuetracker
 */

JLoader::import('joomla.application.component.controlleradmin');

/**
 * Class IssuetrackerControllerItloglist
 */
class IssuetrackerControllerItloglist extends JControllerAdmin
{
   protected   $option     = 'com_issuetracker';

   /**
    * @param array $config
    */
   public function __construct($config = array())
   {
      parent::__construct($config);

      $this->registerTask('purge',   'purge');

   }

   /**
    * Proxy for getModel.
    * @since   1.6
    * @param string $name
    * @param string $prefix
    * @return object
    */
   public function getModel($name = 'Itloglist', $prefix = 'IssuetrackerModel')
   {
      $model = parent::getModel($name, $prefix, array('ignore_request' => true));
      return $model;
   }

    /**
     * Method to purge the table.
     *
     * @param string $name
     * @param string $prefix
     * @return  True.
     */
   public function purge($name = 'Itloglist', $prefix = 'IssuetrackerModel')
   {
      $model = parent::getModel($name, $prefix, array('ignore_request' => true));
      $model->purge();
      $this->setRedirect('index.php?option=com_issuetracker&view=itloglist',JText::_('COM_ISSUETRACKER_TABLE_PURGED_MSG'));
   }
}