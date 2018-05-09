<?php
/*
 *
 * @Version       $Id: cpanel.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.4.2
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
 * Class IssueTrackerControllerCpanel
 */
class IssueTrackerControllerCpanel extends IssueTrackerController {

    /**
     * Control Panel display method.
     */
    function display() {
        // JRequest::setVar('view', 'cpanel');
        JFactory::getApplication()->input->set('view', 'cpanel');

        parent::display();
    }

}
