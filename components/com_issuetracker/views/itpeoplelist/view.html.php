<?php
/*
 *
 * @Version       $Id: view.html.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.5.0
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::import( 'joomla.application.component.view');

/**
 * HTML View class for the Issue Tracker Component
 *
 * @package    Issue Tracker
 * @subpackage Components
 */
class IssueTrackerViewItpeoplelist extends JViewLegacy
{
   protected $print;
   protected $state;
   protected $pagination;
   protected $params;
   protected $pageclass_sfx;
   protected $data;
   protected $sortDirection;
   protected $sortColumn;
   /**
    * @param null $tpl
    * @return mixed|void
    */
   function display($tpl = null){
      $app     = JFactory::getApplication();
      $params  = $app->getParams();
      $this->params = $params;

      //Escape strings for HTML output
      $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

      $this->print      = JFactory::getApplication()->input->getBool('print');

      $data = $this->get('Data');
      $this->data = $data;

      $pagination = $this->get('Pagination');
      $this->pagination = $pagination;

      $this->state = $this->get('State');

      $this->sortDirection   = $this->state->get('filter_order_Dir');
      $this->sortColumn      = $this->state->get('filter_order');

      parent::display($tpl);
   }
}
