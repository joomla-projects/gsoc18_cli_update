<?php
/*
 *
 * @Version       $Id: support.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.5.0
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

JLoader::import('joomla.application.component.controller');


/**
 * Class IssueTrackerControllerSupport
 */
class IssueTrackerControllerSupport extends IssueTrackerController {

    /**
     *
     */
    function display() {
        JFactory::getApplication()->input->set('view', 'support');
        parent::display();
    }
}