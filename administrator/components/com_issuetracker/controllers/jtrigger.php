<?php
/*
 *
 * @Version       $Id: jtrigger.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.5.0
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Jtrigger controller class.
 */
class IssuetrackerControllerJtrigger extends JControllerForm
{
   /**
    *
    */
   function __construct() {
        $this->view_list = 'jtriggers';
        parent::__construct();
    }
}