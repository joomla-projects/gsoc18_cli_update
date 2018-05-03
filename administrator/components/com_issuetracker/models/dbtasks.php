<?php
/*
 *
 * @Version       $Id: dbtasks.php 2275 2016-03-21 16:09:48Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.11
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-03-21 16:09:48 +0000 (Mon, 21 Mar 2016) $
 *
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load log helper
if (! class_exists('IssueTrackerHelperLog')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'log.php');
}

/**
 * Class IssueTrackerModelDbtasks
 */
class IssueTrackerModelDbtasks extends JModelLegacy
{

   /**
    * @return bool
    */
   public function addsampledata()
   {
      $app = JFactory::getApplication();

      // First check whether we have the people ids in use.
      $db = $this->getDBO();
      $query  = 'select count(*) from `#__it_people` where id between 2 and 18';
      $db->setQuery($query);
      $result = $db->loadResult();

      if ($result > 0 ) {
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_WARNING_ITPEOPLE_IDS_INUSE'));
         // We have one (or possibly more?) of the ids we are using for the sample data in use.
         // We need to modify the sample people and then after loading update the it_issues with the
         // revised person id.
         if ( $result == 1 ) {
            // We have one of the ids in use, probably the Super User but might be the Anonymous user we created.
            $query  = "SELECT id FROM `#__it_people` ";
            $query .= " WHERE user_id IN (SELECT user_id FROM `#__user_usergroup_map` WHERE group_id = 8) ";
            $query .= " AND id < 20";
            $db->setQuery($query);
            $jversion = new JVersion();
            if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
               $supers = $db->loadColumn();
            } else {
               $supers - $db->LoadResultArray();
            }
            $cntsuper = count($supers);

            $query = "select id from `#__it_people` where person_name = 'Anonymous'";
            $db->setQuery($query);
            $anon_id = $db->loadResult();

            // Call add projects separately
            $db->setQuery('CALL #__create_sample_projects');
            $db->execute();

            // Now add people having modified the used super id.
            // We generally use 2->18 but with the Super User using one of these we use 19 instead.
            // The anonymous user will normally be id=1 but it the super user is using it it will be id=2.
            // Need to insert the records individually since the classes do not like multiple inserts on a single statement.  Very Strange!!
            // Build up our insert string.
            $query = "INSERT INTO `#__it_people` (`id`, `person_name`, `person_email`, `registered`, `person_role`, `username`, `assigned_project`) VALUES";
            if ( in_array(2, $supers) || $anon_id == 2 ) {
               $query1 = $query . " ('19', 'Thomas Cobley', 'tom.cobley@bademail.com', '0', '1', 'tcobley', null) ";
            } else {
               $query1 = $query . " ('2', 'Thomas Cobley', 'tom.cobley@bademail.com', '0', '1', 'tcobley', null) ";
            }
            $db->setQuery($query1);
            $db->execute();

            if ( in_array(3, $supers ) ) {
               $query1 = $query . " (19, 'Harry Hawke', 'harry.hawke@bademail.com', '0', '4', 'hhawke', null) ";
            } else {
               $query1 = $query . " (3, 'Harry Hawke', 'harry.hawke@bademail.com', '0', '4', 'hhawke', null) ";
            }
            $db->setQuery($query1);
            $db->execute();

            if ( in_array(4, $supers ) ) {
               $query1 = $query . " (19, 'Tom Pearce', 'tom.pearce@bademail.com', '0', '4', 'tpearce', null) ";
            } else {
               $query1 = $query . " (4, 'Tom Pearce', 'tom.pearce@bademail.com', '0', '4', 'tpearce', null) ";
            }
            $db->setQuery($query1);
            $db->execute();
            if ( in_array(5, $supers ) ) {
               $query1 = $query . " (19, 'Bill Brewer', 'bill.brewer@bademail.com', '0', '3', 'bbrewer', 2) ";
            } else {
               $query1 = $query . " (5, 'Bill Brewer', 'bill.brewer@bademail.com', '0', '3', 'bbrewer', 2) ";
            }
            $db->setQuery($query1);
            $db->execute();
            if ( in_array(6, $supers ) ) {
               $query1 = $query . " (19, 'Jan Stewer', 'jan.stewer@bademail.com', '0', '3', 'jstewer', 3) ";
            } else {
               $query1 = $query . " (6, 'Jan Stewer', 'jan.stewer@bademail.com', '0', '3', 'jstewer', 3) ";
            }
            $db->setQuery($query1);
            $db->execute();
            if ( in_array(7, $supers ) ) {
               $query1 = $query . " (19, 'Peter Gurney', 'peter.gurney@bademail.com', '0', '3', 'pgurney', 4) ";
            } else {
               $query1 = $query . " (7, 'Peter Gurney', 'peter.gurney@bademail.com', '0', '3', 'pgurney', 4) ";
            }
            $db->setQuery($query1);
            $db->execute();
            if ( in_array(8, $supers ) ) {
               $query1 = $query . " (19, 'Peter Davy', 'peter.davy@bademail.com', '0', '3', 'pdavy', 5) ";
            } else {
               $query1 = $query . " (8, 'Peter Davy', 'peter.davy@bademail.com', '0', '3', 'pdavy', 5) ";
            }
            $db->setQuery($query1);
            $db->execute();
            if ( in_array(9, $supers ) ) {
               $query1 = $query . " (19, 'Daniel Whiddon', 'daniel.whiddon@bademail.com', '0', '3', 'dwhiddon', 6) ";
            } else {
               $query1 = $query . " (9, 'Daniel Whiddon', 'daniel.whiddon@bademail.com', '0', '3', 'dwhiddon', 6) ";
            }
            $db->setQuery($query1);
            $db->execute();
            if ( in_array(10, $supers ) ) {
               $query1 = $query . " (19, 'Jack London', 'jack.london@bademail.com', '0', '5', 'jlondon', 2) ";
            } else {
               $query1 = $query . " (10, 'Jack London', 'jack.london@bademail.com', '0', '5', 'jlondon', 2) ";
            }
            $db->setQuery($query1);
            $db->execute();
            if ( in_array(11, $supers ) ) {
               $query1 = $query . " (19, 'Mark Tyne', 'mark.tyne@bademail.com', '0', '5', 'mtyne', 2) ";
            } else {
               $query1 = $query . " (11, 'Mark Tyne', 'mark.tyne@bademail.com', '0', '5', 'mtyne', 2) ";
            }
            $db->setQuery($query1);
            $db->execute();
            if ( in_array(12, $supers ) ) {
               $query1 = $query . " (19, 'Jane Kerry', 'jane.kerry@bademail.com', '0', '5', 'jkerry', 6) ";
            } else {
               $query1 = $query . " (12, 'Jane Kerry', 'jane.kerry@bademail.com', '0', '5', 'jkerry', 6) ";
            }
            $db->setQuery($query1);
            $db->execute();
            if ( in_array(13, $supers ) ) {
               $query1 = $query . " (19, 'Olive Pope', 'olive.pope@bademail.com', '0', '5','opope', 3) ";
            } else {
               $query1 = $query . " (13, 'Olive Pope', 'olive.pope@bademail.com', '0', '5','opope', 3) ";
            }
            $db->setQuery($query1);
            $db->execute();
            if ( in_array(14, $supers ) ) {
               $query1 = $query . " (19, 'Russ Sanders', 'russ.sanders@bademail.com', '0', '5', 'rsanders', 4) ";
            } else {
               $query1 = $query . " (14, 'Russ Sanders', 'russ.sanders@bademail.com', '0', '5', 'rsanders', 4) ";
            }
            $db->setQuery($query1);
            $db->execute();
            if ( in_array(15, $supers ) ) {
               $query1 = $query . " (19, 'Tucker Uberton', 'tucker.uberton@bademail.com', '0', '5', 'ruberton', 4) ";
            } else {
               $query1 = $query . " (15, 'Tucker Uberton', 'tucker.uberton@bademail.com', '0', '5', 'ruberton', 4) ";
            }
            $db->setQuery($query1);
            $db->execute();
            if ( in_array(16, $supers ) ) {
               $query1 = $query . " (19, 'Vicky Mitchell', 'vicky.mitchell@bademail.com', '0', '5', 'vmitchell', 5) ";
            } else {
               $query1 = $query . " (16, 'Vicky Mitchell', 'vicky.mitchell@bademail.com', '0', '5', 'vmitchell', 5) ";
            }
            $db->setQuery($query1);
            $db->execute();
            if ( in_array(17, $supers ) ) {
               $query1 = $query . " (19, 'Scott Tiger', 'scott.tiger@bademail.com', '0', '5', 'stiger', 5)";
            } else {
               $query1 = $query . " (17, 'Scott Tiger', 'scott.tiger@bademail.com', '0', '5', 'stiger', 5)";
            }
            $db->setQuery($query1);
            $db->execute();
            if ( in_array(18, $supers ) ) {
               $query1 = $query . " (19, 'John Gilpin', 'john.gilpin@bademail.com', '0', '5', 'jgilpin', 5)";
            } else {
               $query1 = $query . " (18, 'John Gilpin', 'john.gilpin@bademail.com', '0', '5', 'jgilpin', 5)";
            }
            $db->setQuery($query1);
            $db->execute();

            // Now add the issues
            $db->setQuery('CALL #__create_sample_issues');
            $db->execute();

            // Finally update the issues with our revised person_id.
            if ( $cntsuper > 0 ) {
               $db->setQuery("UPDATE #__it_issues set identified_by_person_id = 19 where identified_by_person_id IN ('".implode("','",$supers)."')");
               $db->execute();
               $db->setQuery("UPDATE #__it_issues set assigned_to_person_id = 19 where assigned_to_person_id IN ('".implode("','",$supers)."')");
               $db->execute();
            }

            // Now update the staff field in it_people
            $db->setQuery("UPDATE #__it_people SET staff = 1 WHERE user_id IN (SELECT distinct assigned_to_person_id FROM #__it_issues)");
            $db->execute();
            return true;
         } else {
            $app->enqueueMessage(JText::_('COM_ISSUETRACKER_ERROR_CANNOT_LOAD_SAMPLE_DATA'));
            return false;
         }
      } else {
         $db->setQuery('CALL #__add_it_sample_data');
         $result = $db->execute();

         if ( ! $result ) {
            $err = $db->getErrorNum();
            // Check for duplicate key, data already loaded.  Note that if system debug is on then the error is still displayed.
            if ($err == 1062) {
               $app->enqueueMessage(JText::_('COM_ISSUETRACKER_ERROR_SAMPLE_DATA_ALREADY_LOADED'));
            } else {
               $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
            }
            return false;
         }

         // Now update the staff field in it_people
         $db->setQuery("UPDATE #__it_people set staff = 1 WHERE user_id IN (SELECT distinct assigned_to_person_id FROM #__it_issues)");
         $db->execute();
         // return true;
      }

      // Now rebuild the projects table
      $this->rebuildTable();

      $db->setQuery('OPTIMIZE TABLE '.$db->quoteName('#__it_issues'));
      $db->execute();
      $db->setQuery('OPTIMIZE TABLE '.$db->quoteName('#__it_projects'));
      $db->execute();
      $db->setQuery('OPTIMIZE TABLE '.$db->quoteName('#__it_people'));
      $db->execute();

      return true;
   }

   /**
    * @return bool
    */
   public function remsampledata()
   {
      $app = JFactory::getApplication();
      // Get parameters
      $this->_params = JComponentHelper::getParams( 'com_issuetracker' );
      $defproject = $this->_params->get('def_project', 1);

      $db = $this->getDBO();
      // Check that it_people contains the sample people.  This may include the Super User of course.
      $query = "SELECT COUNT(*) from `#__it_people` where id between 2 AND 18";
      $db->setQuery($query);
      $result = $db->loadResult();

      if ( $result != 17 ) {
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_ERROR_SAMPLEDATA_NOT_INSTALLED'),'error');
         return false;
      }

      // Double Check that it is indeed our sample users in the range.  Should be sufficient to check for three users.
      // If the super user was created with a low id then subsequent users will follow on, and hence if they later load our component and try adding
      // the sample data it will fail, but they may try and remove it, perhaps by accident!
      $query = "SELECT COUNT(*) from `#__it_people` where person_name in ('Thomas Cobley','Peter Davy','John Gilpin') AND id between 2 AND 18";
      $db->setQuery($query);
      $result = $db->loadResult();
      if ( $result != 3 ) {
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_ERROR_SAMPLEDATA_NOT_INSTALLED'),'error');
         return false;
      }

      // Check that the default project is not one of the sample projects.
      if ( $defproject >2 && $defproject < 10 ) {
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_ERROR_DEFPROJECT_ASSIGNMENT'),'error');
         return false;
      }

      // Check if they have created any issues of their own and/or assigned people or issues to any of the samples
      $query = "SELECT COUNT(*) from `#__it_issues` where related_project_id between 2 AND 9 AND id > 28";
      $db->setQuery($query);
      $result = $db->loadResult();

      if ( $result > 0 ) {
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_ERROR_ISSUES_DEFPROJECT_ASSIGNMENT'),'error');
         return false;
      }

      // Check if the Super User id is in our range 2->18.
      $query  = "SELECT id FROM `#__it_people` ";
      $query .= " WHERE user_id IN (SELECT user_id FROM `#__user_usergroup_map` WHERE group_id = 8) ";
      $query .= " AND id < 20";
      $db->setQuery($query);

      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         $supers = $db->loadColumn();
      } else {
         $supers - $db->LoadResultArray();
      }
      $cntsuper = count($supers);

      // Check anonymous id as well.
      $query = "select id from `#__it_people` where person_name = 'Anonymous'";
      $db->setQuery($query);
      $anon_id = $db->loadResult();

      $query = "SELECT COUNT(*) from `#__it_people` where assigned_project between 3 AND 7 AND id > ";
      if ( $cntsuper > 0 ) {
         $query .=  "19 AND id NOT IN ('".implode("','",$supers)."')";
      } else {
         $query .= '18';
      }
      $db->setQuery($query);
      $result = $db->loadResult();
      if ( $result > 0 ) {
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_ERROR_PEOPLE_DEFPROJECT_ASSIGNMENT'),'error');
         return false;
      }

      $query = "SELECT COUNT(*) from `#__it_issues` where identified_by_person_id between 2 AND ";
      if ( $cntsuper > 0 ) {
         $query .=  "19 AND id > 28 AND identified_by_person_id NOT IN ('".implode("','",$supers)."')";
      } else {
         $query .= '18 AND id > 28 ';
      }
      $db->setQuery($query);
      $result = $db->loadResult();
      if ( $result > 0 ) {
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_ERROR_ISSUES_IDENTPEOPLE_ASSIGNMENT'),'error');
         return false;
      }

      $query = "SELECT COUNT(*) from `#__it_issues` where assigned_to_person_id between 2 AND ";
      if ( $cntsuper > 0 ) {
         $query .=  "19 AND id > 28 AND assigned_to_person_id NOT IN ('".implode("','",$supers)."')";
      } else {
         $query .= '18 AND id > 28 ';
      }
      $db->setQuery($query);
      $result = $db->loadResult();
      if ( $result > 0 ) {
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_ERROR_ISSUES_ASSIGNPEOPLE_ASSIGNMENT'),'error');
         return false;
      }

      // Remove attachments and progress records for the sample issues if any.
      for ($x = 1; $x <= 29; $x++) {
          $this->delete_attachments($x);
          $this->delete_progress_recs($x);
      }

      // Also need to check that the Super user is not within our range.  Joomla 2.5.4 and above change.
      // If it is we have to remove the people ids around it.
      if ( $cntsuper > 0 ) {
         // Remove the sample data
         $db->setQuery("delete from `#__it_issues` where id < 29");
         $db->execute();
         $db->setQuery("delete from `#__it_people` where id >1 AND id < 20 AND id NOT IN (".implode(",",$supers).",".$anon_id.")");
         $db->execute();
         $db->setQuery("delete from `#__it_projects` where id > 2 AND id < 8");
         $db->execute();
      } else {
         $db->setQuery("CALL #__remove_it_sample_data");
         $db->execute();
      }

      // Only need to rebuild since we are removing entries.
      $this->rebuildTable();

      $db->setQuery('OPTIMIZE TABLE '.$db->quoteName('#__it_issues'));
      $db->execute();
      $db->setQuery('OPTIMIZE TABLE '.$db->quoteName('#__it_projects'));
      $db->execute();
      $db->setQuery('OPTIMIZE TABLE '.$db->quoteName('#__it_people'));
      $db->execute();

      return true;
   }

   public function syncusers()
   {
      $app = JFactory::getApplication();
      $user = JFactory::getUser();

      // Get parameters
      $this->_params = JComponentHelper::getParams( 'com_issuetracker' );
      $defrole = $this->_params->get('def_role', 6);
      $defproject = $this->_params->get('def_project', 1);

      $db = $this->getDBO();
      // $db->setQuery('CALL #__update_it_users');
      $query = "INSERT IGNORE INTO `#__it_people` (user_id, person_name, username, person_email, registered, person_role, assigned_project, created_by, created_on)";
      $query.= "\n   SELECT id, name, username, email, '1', ";
      $query .= "'" .$defrole."','" .$defproject. "','" .$user->username. "', registerDate FROM `#__users`";
      $db->setQuery($query);
      $result = $db->execute();

      if ( ! $result ) {
         $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
      }

      $db->setQuery('OPTIMIZE TABLE '.$db->quoteName('#__it_issues'));
      $db->execute();
      $db->setQuery('OPTIMIZE TABLE '.$db->quoteName('#__it_projects'));
      $db->execute();
      $db->setQuery('OPTIMIZE TABLE '.$db->quoteName('#__it_people'));
      $db->execute();
   }


   /**
    * Method to remove any progress records associated with the issue.
    *
    * @param integer $issue Input issue identifier.
    *
    * @return boolean Return true or the number of records deleted.
    */
   private function delete_progress_recs($issue)
   {
      $db = $this->getDBO();
      $query  = "SELECT count(*) FROM `#__it_progress` WHERE issue_id = '".$issue."'";
      $db->setQuery( $query );
      $delcnt = $db->loadResult();

      if ( $delcnt > 0 ) {
         $query  = "DELETE FROM `#__it_progress` WHERE issue_id = '".$issue."'";
         $db->setQuery( $query );
         $delcnt = $db->loadResult();
         return $delcnt;
      }

      return true;
   }

   /**
    * Method to remove any attachments associated with the issue.
    *
    * @param integer $issue Input issue identifier.
    *
    * @return boolean Return true or the number of attachments deleted.
    */
   private function delete_attachments($issue)
   {
      $db = $this->getDBO();
      $query  = "SELECT count(*) FROM `#__it_attachment` WHERE issue_id = ";
      $query .= "(SELECT alias FROM `#__it_issues` WHERE id = '".$issue."')";
      $db->setQuery( $query );
      $delcnt = $db->loadResult();

      if ( $delcnt > 0 ) {
         JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_issuetracker/tables');
         $row = JTable::getInstance('Attachment', 'IssueTrackerTable', array());

         $query  = "SELECT id FROM `#__it_attachment` WHERE issue_id = ";
         $query .= "(SELECT alias FROM `#__it_issues` WHERE id = '".$issue."')";
         $db->setQuery( $query );
         $pks = $db->loadColumn();

         foreach ( $pks as $pk) {
            $row->delete($pk);
         }
         return $delcnt;
      }

      return true;
   }


   /* Routines for rebuilding projects under a Nested table.
    *
    */
   /**
    * @return bool
    */
   function rebuildTable()
   {
      // Get model
      // $att = JModel::getInstance('itprojects','IssueTrackerModel');

      // Get an instance of the table object.
      $table = JTable::getInstance('Itprojects', 'IssueTrackerTable');

      if (!$table->rebuild()) {
         $this->setError($table->getError());
         return false;
      }

      // Clear the cache
      // $this->cleanCache();

      return true;

   }
}