<?php
/*
 *
 * @Version       $Id: issuetracker.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.4.2
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Required for Joomla 3.0
if(!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}
// From Akeeba Live Update
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'liveupdate'.DS.'liveupdate.php';
// if(JRequest::getCmd('view','') == 'liveupdate') {
if(JFactory::getApplication()->input->get('view', '') == 'liveupdate') {
   LiveUpdate::handleRequest();
return;
}

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_issuetracker')){
   return JError::raiseWarning(404, JText::_( 'JERROR_ALERTNOAUTHOR' ));
}

// Include dependancies
JLoader::import('joomla.application.component.controller');
// Require the base controller
// require_once( JPATH_COMPONENT.DS.'controller.php' );

$jversion = new JVersion();
if( version_compare( $jversion->getShortVersion(), '2.5.6', 'lt' ) ) {
   $controller = JController::getInstance('IssueTracker');
} else {
   $controller = JControllerLegacy::getInstance('IssueTracker');
}
// $controller->execute( JRequest::getCmd( 'task' ) );
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
