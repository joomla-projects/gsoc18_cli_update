<?php
/*
 *
 * @Version       $Id: audit.php 2280 2016-04-24 15:54:22Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.11
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-04-24 16:54:22 +0100 (Sun, 24 Apr 2016) $
 *
 */
defined('_JEXEC') or die;

// Load log helper
if (! class_exists('IssueTrackerHelperLog')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'log.php');
}

/*
 *
 * Issue Tracker helper.
 *
 */
/**
 * Class IssueTrackerAuditHelper
 */
class IssueTrackerAuditHelper
{

   protected static $change_table = 'it_chistory';
   protected static $trig_ident = '_audit_';

   /*
    * Method to create a database trigger for the basic audit.
    *
    */
   /**
    * @param null $tname
    * @param string $type
    * @param string $event
    */
   function createTrigger( $tname = NULL, $type = 'BEFORE', $event = 'INSERT' )
   {
      $events = array('INSERT','UPDATE','DELETE');
      $types  = array('BEFORE','AFTER');
      if ( empty($tname) )  return;

      if ( !in_array( $event, $events) ) {
         echo '<p>' . JText::_('COM_ISSUETRACKER_INVALID_TRIG_EVENT_TEXT') . '</p>';
         return;
      }

      if ( !in_array( $type, $types) ) {
         echo '<p>' . JText::_('COM_ISSUETRACKER_INVALID_TRIG_TYPE_TEXT') . '</p>';
         return;
      }

      if ( empty($db) ) $db = JFactory::getDbo();

      // Create database trigger.
      $trig = $tname;
      if ( $type = 'AFTER' ) {
         switch ($event) {
            case 'INSERT':
               $trig .= "_bi";
               break;
            case 'UPDATE':
               $trig .= "_bu";
               break;
            case 'DELETE':
               $trig .= "_bd";
               break;
         }
      } else {
         switch ($event) {
            case 'INSERT':
               $trig .= "_ai";
               break;
            case 'UPDATE':
               $trig .= "_au";
               break;
            case 'DELETE':
               $trig .= "_ad";
               break;
         }
      }

      $query= "DROP TRIGGER IF EXISTS `".$trig."`;";
      $db->setQuery($query);
      $db->execute();

      $query="create trigger `".$trig."`";
      $query.= "\n".$type." ".$event." ON `".$tname."`";
      $query.= "\nFOR EACH ROW";
      $query.= "\nBEGIN ";
      $query.= "\n   IF (NEW.ACTUAL_END_DATE = '0000-00-00 00:00:00') THEN";
      $query.= "\n      SET NEW.ACTUAL_END_DATE := NULL;";
      $query.= "\n   END IF;";
      if ( $event == 'INSERT' ) {
        $query.= "\n   IF (NEW.CREATED_ON IS NULL OR NEW.CREATED_ON = '0000-00-00 00:00:00') THEN";
         $query.= "\n      SET NEW.CREATED_ON := sysdate();";
         $query.= "\n   END IF; ";
         $query.= "\n   IF (NEW.CREATED_BY IS NULL OR NEW.CREATED_BY = '') THEN";
         $query.= "\n      SET NEW.CREATED_BY := USER();";
         $query.= "\n   END IF; ";
      } elseif ( $event == 'UPDATE' ) {
         $query.= "\n   IF (NEW.MODIFIED_ON IS NULL OR NEW.MODIFIED_ON = '0000-00-00 00:00:00') THEN";
         $query.= "\n      SET NEW.MODIFIED_ON := sysdate();";
         $query.= "\n   END IF; ";
         $query.= "\n   IF (NEW.MODIFIED_BY IS NULL OR NEW.MODIFIED_BY = '') THEN";
         $query.= "\n      SET NEW.MODIFIED_BY := USER();";
         $query.= "\n   END IF; ";
      }
      $query.= "\nEND;";

      $db->setQuery($query);
      $db->execute();
   }

    /**
     * Gets a list of the tables being monitored.
     *
     * @param int $inchead
     * @return  JObject
     * @since   1.6
     */
   public static function getTablename( $inchead = 0 )
   {
      $db = JFactory::getDBO();
      $db->setQuery( 'SELECT distinct `table_name` AS value, `table_name` AS text FROM `#__it_chistory` ORDER BY table_name');
      $options = array();
      // Add an optional null value line if requested
      if ( $inchead == 0 ) {
         $options[] = JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_TABLE').' -' );
      }

      foreach( $db->loadObjectList() as $r){
         $options[] = JHTML::_('select.option',  $r->value, $r->text );
      }
      return $options;
   }

    /**
     * Gets a list of the tables being monitored.
     *
     * @param int $inchead
     * @return  JObject
     * @since   1.6
     */
   public static function getTrig_tablename( $inchead = 0 )
   {
      $db = JFactory::getDBO();
      $db->setQuery( 'SELECT distinct `table_name` AS value, `table_name` AS text FROM `#__it_triggers` ORDER BY table_name');
      $options = array();
      // Add an optional null value line if requested
      if ( $inchead == 0 ) {
         $options[] = JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_TABLE').' -' );
      }

      foreach( $db->loadObjectList() as $r){
         $options[] = JHTML::_('select.option',  $r->value, $r->text );
      }
      return $options;
   }

   /*
    * Given the input data create and audit trigger based on criteria.
    *
    */
   /**
    * @param $data
    * @return bool
    */
   public static function createAuditTrigger( & $data )
   {
      // echo "<PRE>";var_dump($data);echo "</PRE>";
      $app = JFactory::getApplication();

      // Set up access to default parameters
      $params     = JComponentHelper::getParams( 'com_issuetracker' );
      $ftranslate = $params->get('ftranslate', 0);
      // $ftranslate = 1;

      $events = array('INSERT','UPDATE','DELETE');
      $types   = array('BEFORE','AFTER');

      $tname = $data['table_name'];

      if ( empty($tname) )  {
         $app->enqueuemessage(JText::_('COM_ISSUETRACKER_NO_TABLENAME_SPECIFIED'), 'error');
         return false;   //Shouldn't occur.
      }

      $event = $data['trigger_event'];
      if ( !in_array( $event, $events) ) {
         $app->enqueuemessage(JText::_('COM_ISSUETRACKER_INVALID_TRIG_EVENT_TEXT'), 'error');
         return false;
      }

      $type = $data['trigger_type'];
      if ( !in_array( $type, $types) ) {
         $app->enqueuemessage(JText::_('COM_ISSEUTRACKER_INVALID_TRIG_TYPE_TEXT'), 'error');
         return false;
      }

      // if ( empty($db) ) $db = JFactory::getDbo();

      // Create database trigger.
      $trig = $tname.self::$trig_ident;
      if ( $type == 'BEFORE' ) {
         switch ($event) {
            case 'INSERT':
               $trig .= "bi";
               break;
            case 'UPDATE':
               $trig .= "bu";
               break;
            case 'DELETE':
               $trig .= "bd";
               break;
         }
      } else {
         switch ($event){
            case 'INSERT':
               $trig .= "ai";
               break;
            case 'UPDATE':
               $trig .= "au";
               break;
            case 'DELETE':
               $trig .= "ad";
               break;
         }
      }

      // Now update the trigger name.
      $data['trigger_name'] = $trig;

      // Check if we have a trigger already in out table of this name.
      $db = JFactory::getDBO();
      $db->setQuery("SELECT count(*) FROM `#__it_triggers` WHERE trigger_name = '".$trig."'");
      $res = $db->loadResult();

      if ( ($data['id'] == 0 && $res >= 1 ) || $res > 2 ) {
         return false;
      }

      // Determine component as well as we can.
      $pos1 = strpos($tname, '_') + 1;
      $pos2 = strpos($tname, '_', $pos1);
      if ( empty($pos2)) {
         $compnt = substr($tname, $pos1);
      } else {
         $compnt = substr($tname, $pos1, $pos2-$pos1);
      }

      $trigstr ="CREATE TRIGGER `".$trig."`";
      $trigstr.= "\n ".$type." ".$event." ON `".$tname."`";
      $trigstr.= "\n FOR EACH ROW";
      $trigstr.= "\n BEGIN ";

      $cols = json_decode($data['columns']);
      // For each of the values specified in the columns array set up the comparison statement.
      // Get the field type from the database
      switch ($event) {
         case "INSERT":
            $trigstr.= "\n   DECLARE nval VARCHAR(255);";
            $trigstr.= "\n   DECLARE changedby VARCHAR(12);";
            $trigstr.= "\n   DECLARE component VARCHAR(255) DEFAULT '".$compnt."';";
            $trigstr.= "\n   DECLARE cexists BOOLEAN;";

            $text = self::create_insert_text($tname, $cols);
            $trigstr .= $text;
            break;

         case "UPDATE";
            $trigstr.= "\n   DECLARE nval VARCHAR(255);";
            $trigstr.= "\n   DECLARE oval VARCHAR(255);";
            $trigstr.= "\n   DECLARE changedby VARCHAR(12);";
            $trigstr.= "\n   DECLARE component VARCHAR(255) DEFAULT '".$compnt."';";
            $trigstr.= "\n   DECLARE cexists BOOLEAN;";

            $text = self::create_update_text($tname, $cols);
            $trigstr .= $text;
            break;

         case "DELETE":
            $trigstr.= "\n   DECLARE oval VARCHAR(255);";
            $trigstr.= "\n   DECLARE changedby VARCHAR(12);";
            $trigstr.= "\n   DECLARE component VARCHAR(255) DEFAULT '".$compnt."';";
            $trigstr.= "\n   DECLARE cexists BOOLEAN;";

            $text = self::create_delete_text($tname, $cols);
            $trigstr .= $text;
            break;
         }

      $trigstr.= "\nEND;";

      $data['trigger_text'] = $trigstr;
      return true;
   }

   /**
    * @param $tname
    * @param $cols
    * @return string
    */
   static function create_insert_text($tname, $cols)
   {
      // Set up access to default parameters
      $params     = JComponentHelper::getParams( 'com_issuetracker' );
      $ftranslate = $params->get('ftranslate', 0);

      $db = JFactory::getDBO();
      $prefix = $db->getPrefix();
      $ctable = $db->getPrefix().self::$change_table;

      // echo "<PRE>"; var_dump($cols); echo "</PRE>";
      $str = null;

      $db->setQuery("select COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE  TABLE_NAME = '".$tname."' AND COLUMN_KEY = 'PRI' ");
      $pkeycol = $db->loadResult();

      // Check for a created_by field and use it to populate audit trail if available.
      $str.= "\n   SELECT IF(count(*) >0 , TRUE, FALSE) INTO cexists ";
      $str.= "\n   FROM INFORMATION_SCHEMA.COLUMNS ";
      $str.= "\n   WHERE TABLE_NAME  = '".$tname."' ";
      $str.= "\n   AND COLUMN_NAME = 'created_by'";
      $str.= "\n   AND DATA_TYPE IN ('int','bigint');";

      $str.= "\n  IF ( cexists ) THEN ";
      $str.= "\n     SELECT IFNULL(created_by,0) into changedby FROM `".$tname."` WHERE ".$pkeycol." = NEW.".$pkeycol."; ";
      $str.= "\n  ELSE ";
      $str.= "\n     SELECT 0 INTO changedby; ";
      $str.= "\n  END IF; ";

      // Check for an alias field for key link field.
      $db->setQuery("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME like '".$tname."' AND COLUMN_NAME = 'alias' ");
      $res = $db->loadResult();

      if ( ! empty($res) ) {
         $aliascol = 'alias';
      } else {
         $aliascol = $pkeycol;
      }

      // Special case since we do not use it_people alias yet.
      if ( substr($tname, -9) == 'it_people') {
         $aliascol = $pkeycol;
      }

      // A convoluted test but have to allow for J2.5 and J3 which return different possible results.
      if (  $cols[0] == '(array) All' || $cols == '(array) All' || $cols == 'All' || empty($cols[0]) || empty($cols) || in_array('All', $cols) ) {
         $db->setQuery("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME like '".$tname."' ");
         $res = $db->loadObjectList();

         foreach($res as $row) {
            $col = $row->COLUMN_NAME;
            $str.= "\n  IF (NEW.".$col." IS NOT NULL) THEN";

            if ( $ftranslate ) {
               $statement = 0;
               if ( $col == 'state' ) {
                  $statement = 1;
                  $str .= self::trans_state('I');
               }
               if ( $col == 'checked_out' || ( $col == 'created_by' && $row->DATA_TYPE == 'int' ) || ($col == 'modified_by' && $row->DATA_TYPE == 'int') ) {
                  $statement = 1;
                  $str.= "\n   SELECT CONCAT(name, ' (', username, ')') into nval FROM `".$prefix."users` WHERE NEW.".$col." != 0 AND id = NEW.".$col.";" ;
               }

               if ( $statement == 1 ) {
                  $str.= "\n     INSERT INTO ".$ctable;
                  $str.= "\n        (table_name, column_name, column_type, component, new_value, action, row_key, row_key_link, change_by)";
                  $str.= "\n     VALUES ('".$tname."', '".$col."', '".$row->DATA_TYPE."', component, nval, 'INSERT', NEW.".$pkeycol.", NEW.".$aliascol.", changedby);";
               } else {
                  $str.= "\n     INSERT INTO ".$ctable;
                  $str.= "\n        (table_name, column_name, column_type, component, new_value, action, row_key, row_key_link, change_by)";
                  $str.= "\n     VALUES ('".$tname."', '".$col."', '".$row->DATA_TYPE."', component, NEW.".$col.", 'INSERT', NEW.".$pkeycol.", NEW.".$aliascol.", changedby);";
               }
            } else {
               $str.= "\n     INSERT INTO ".$ctable;
               $str.= "\n        (table_name, column_name, column_type, component, new_value, action, row_key, row_key_link, change_by)";
               $str.= "\n     VALUES ('".$tname."', '".$col."', '".$row->DATA_TYPE."', component, NEW.".$col.", 'INSERT', NEW.".$pkeycol.", NEW.".$aliascol.", changedby);";
            }
            $str.= "\n   END IF;";
         }
      } else {
         foreach($cols as $col) {
            $db->setQuery("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME like '".$tname."' AND COLUMN_NAME = '".$col."' ");
            $ctype = $db->loadResult();
            $str.= "\n  IF (NEW.".$col." IS NOT NULL) THEN";

            if ( $ftranslate ) {
               $statement = 0;
               if ( $col == 'state' ) {
                  $statement = 1;
                  $str .= self::trans_state('I');
               }
               if ( $col == 'checked_out' || ( $col == 'created_by' && $ctype == 'int' ) || ($col == 'modified_by' && $ctype == 'int') ) {
                  $statement = 1;
                  $str.= "\n   SELECT CONCAT(name, ' (', username, ')') into nval FROM `".$prefix."users` WHERE NEW.".$col." != 0 AND id = NEW.".$col.";" ;
               }

               if ( $statement == 1 ) {
                  $str.= "\n     INSERT INTO ".$ctable;
                  $str.= "\n        (table_name, column_name, column_type, component, new_value, action, row_key, row_key_link, change_by)";
                  $str.= "\n     VALUES ('".$tname."', '".$col."', '".$ctype."', component, nval, 'INSERT', NEW.".$pkeycol.", NEW.".$aliascol.", changedby);";
               } else {
                  $str.= "\n     INSERT INTO ".$ctable;
                  $str.= "\n        (table_name, column_name, column_type, component, new_value, action, row_key, row_key_link, change_by)";
                  $str.= "\n     VALUES ('".$tname."', '".$col."', '".$ctype."', component, NEW.".$col.", 'INSERT', NEW.".$pkeycol.", NEW.".$aliascol.", changedby);";
               }
            } else {
               $str.= "\n     INSERT INTO ".$ctable;
               $str.= "\n        (table_name, column_name, column_type, component, new_value, action, row_key, row_key_link, change_by)";
               $str.= "\n     VALUES ('".$tname."', '".$col."', '".$ctype."', component, NEW.".$col.", 'INSERT', NEW.".$pkeycol.", NEW.".$aliascol.", changedby);";
            }
            $str.= "\n   END IF;";
         }
      }
      return $str;
   }

   /**
    * @param $tname
    * @param $cols
    * @return string
    */
   static function create_update_text($tname, $cols)
   {
      // Set up access to default parameters
      $params     = JComponentHelper::getParams( 'com_issuetracker' );
      $ftranslate = $params->get('ftranslate', 0);

      $db = JFactory::getDBO();
      $prefix = $db->getPrefix();
      $ctable = $prefix . self::$change_table;
      // echo "<PRE>"; var_dump($cols); echo "</PRE>";
      $str = null;

      $db->setQuery("select COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$tname."' AND COLUMN_KEY = 'PRI' ");
      $pkeycol = $db->loadResult();

      // Check for a modified_by field to use.
      $str.= "\n   SELECT IF(count(*) >0 , TRUE, FALSE) INTO cexists ";
      $str.= "\n   FROM INFORMATION_SCHEMA.COLUMNS ";
      $str.= "\n   WHERE TABLE_NAME  = '".$tname."' ";
      $str.= "\n   AND COLUMN_NAME = 'modified_by'";
      $str.= "\n   AND DATA_TYPE IN ('int','bigint');";

      $str.= "\n  IF ( cexists ) THEN ";
      $str.= "\n     SELECT IFNULL(modified_by, 0) INTO changedby FROM `".$tname."` WHERE ".$pkeycol." = NEW.".$pkeycol."; ";
      $str.= "\n  ELSE ";
      $str.= "\n     SELECT 0 INTO changedby; ";
      $str.= "\n  END IF; ";

      // Check for an alias field for key link field.
      $db->setQuery("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME like '".$tname."' AND COLUMN_NAME = 'alias' ");
      $res = $db->loadResult();

      if ( ! empty($res) ) {
         $aliascol = 'alias';
      } else {
         $aliascol = $pkeycol;
      }

      // Special case since we do not use it_people alias yet.
      if ( substr($tname, -9) == 'it_people') {
         $aliascol = $pkeycol;
      }

      if (  $cols[0] == '(array) All' || $cols == '(array) All' || $cols == 'All' || empty($cols[0]) || empty($cols) || in_array('All', $cols) ) {
         $db = JFactory::getDBO();
         $db->setQuery("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name like '".$tname."' ");
         $res = $db->loadObjectList();

         foreach($res as $row) {
            $col = $row->COLUMN_NAME;
            $str.= "\n  IF (OLD.".$col." IS NULL AND NEW.".$col." IS NOT NULL) OR";
            $str.= "\n     (OLD.".$col." IS NOT NULL AND NEW.".$col." IS NULL) OR";
            $str.= "\n     (OLD.".$col." != NEW.".$col.") THEN";

            if ( $ftranslate ) {
               $statement = 0;
               if ( $col == 'state' ) {
                  $statement = 1;
                  $str .= self::trans_state('U');
               }
               if ( $col == 'checked_out' || ( $col == 'created_by' && $row->DATA_TYPE == 'int' ) || ($col == 'modified_by' && $row->DATA_TYPE == 'int') ) {
                  $statement = 1;
                  $str.= "\n   SELECT CONCAT(name, ' (', username, ')') into oval FROM `".$prefix."users` WHERE OLD.".$col." != 0 AND id = OLD.".$col.";" ;
                  $str.= "\n   SELECT CONCAT(name, ' (', username, ')') into nval FROM `".$prefix."users` WHERE NEW.".$col." != 0 AND id = NEW.".$col.";" ;
               }

               if ( $statement == 1 ) {
                  $str.= "\n   INSERT INTO ".$ctable;
                  $str.= "\n     (table_name, column_name, column_type, component, old_value, new_value, action, row_key, row_key_link, change_by, change_date)";
                  $str.= "\n   VALUES ('".$tname."', '".$col."', '".$row->DATA_TYPE."', component, oval, nval, 'UPDATE', NEW.".$pkeycol.", NEW.".$aliascol.", changedby, NOW());";
               } else {
                  $str.= "\n  INSERT INTO ".$ctable;
                  $str.= "\n     (table_name, column_name, column_type, component, old_value, new_value, action, row_key, row_key_link, change_by, change_date)";
                  $str.= "\n  VALUES ('".$tname."', '".$col."', '".$row->DATA_TYPE."', component, OLD.".$col.", NEW.".$col.", 'UPDATE', NEW.".$pkeycol.", NEW.".$aliascol.", changedby, NOW());";
               }
            } else {
               $str.= "\n  INSERT INTO ".$ctable;
               $str.= "\n     (table_name, column_name, column_type, component, old_value, new_value, action, row_key, row_key_link, change_by, change_date)";
               $str.= "\n  VALUES ('".$tname."', '".$col."', '".$row->DATA_TYPE."', component, OLD.".$col.", NEW.".$col.", 'UPDATE', NEW.".$pkeycol.", NEW.".$aliascol.", changedby, NOW());";
            }
            $str.= "\n  END IF;";
         }
      } else {
         foreach($cols as $col) {
            $db->setQuery("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME like '".$tname."'  AND COLUMN_NAME = '".$col."' ");
            $ctype = $db->loadResult();

            $str.= "\n   IF (OLD.".$col." IS NULL AND NEW.".$col." IS NOT NULL) OR";
            $str.= "\n      (OLD.".$col." IS NOT NULL AND NEW.".$col." IS NULL) OR";
            $str.= "\n      (OLD.".$col." != NEW.".$col.") THEN";

            if ( $ftranslate ) {
               $statement = 0;
               if ( $col == 'state' ) {
                  $statement = 1;
                  $str .= self::trans_state('U');
               }
               if ( $col == 'checked_out' || ( $col == 'created_by' && $ctype == 'int' ) || ($col == 'modified_by' && $ctype == 'int') ) {
                  $statement = 1;
                  $str.= "\n   SELECT CONCAT(name, ' (', username, ')') into oval FROM `".$prefix."users` WHERE OLD.".$col." != 0 AND id = OLD.".$col.";" ;
                  $str.= "\n   SELECT CONCAT(name, ' (', username, ')') into nval FROM `".$prefix."users` WHERE NEW.".$col." != 0 AND id = NEW.".$col.";" ;
               }

               if ( $statement == 1 ) {
                  $str.= "\n   INSERT INTO ".$ctable;
                  $str.= "\n     (table_name, column_name, column_type, component, old_value, new_value, action, row_key, row_key_link, change_by, change_date)";
                  $str.= "\n   VALUES ('".$tname."', '".$col."', '".$ctype."', component, oval, nval, 'UPDATE', NEW.".$pkeycol.", NEW.".$aliascol.", changedby, NOW());";
               } else {
                  $str.= "\n  INSERT INTO ".$ctable;
                  $str.= "\n     (table_name, column_name, column_type, component, old_value, new_value, action, row_key, row_key_link, change_by, change_date)";
                  $str.= "\n  VALUES ('".$tname."', '".$col."', '".$ctype."', component, OLD.".$col.", NEW.".$col.", 'UPDATE', NEW.".$pkeycol.", NEW.".$aliascol.", changedby, NOW());";
               }
            } else {
               $str.= "\n   INSERT INTO ".$ctable;
               $str.= "\n      (table_name, column_name, column_type, component, old_value, new_value, action, row_key, row_key_link, change_by, change_date)";
               $str.= "\n   VALUES ('".$tname."', '".$col."', '".$ctype."', component, OLD.".$col.", NEW.".$col.", 'UPDATE', NEW.".$pkeycol.", NEW.".$aliascol.", changedby, NOW());";
            }
            $str.= "\n   END IF;";
         }
      }
      return $str;
   }

   /**
    * @param $tname
    * @param $cols
    * @return string
    */
   static function create_delete_text($tname, $cols)
   {
      // Set up access to default parameters
      $params     = JComponentHelper::getParams( 'com_issuetracker' );
      $ftranslate = $params->get('ftranslate', 0);

      $db = JFactory::getDBO();
      $prefix = $db->getPrefix();
      $ctable = $db->getPrefix().self::$change_table;
      // echo "<PRE>"; var_dump($cols); echo "</PRE>";
      $str = null;

      $db->setQuery("select COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE  TABLE_NAME = '".$tname."' AND COLUMN_KEY = 'PRI' ");
      $pkeycol = $db->loadResult();

      // Check if modified_by field exists.
      $str.= "\n   SELECT IF(count(*) >0 , TRUE, FALSE) INTO cexists ";
      $str.= "\n   FROM INFORMATION_SCHEMA.COLUMNS ";
      $str.= "\n   WHERE TABLE_NAME  = '".$tname."' ";
      $str.= "\n   AND COLUMN_NAME = 'modified_by'";
      $str.= "\n   AND DATA_TYPE IN ('int','bigint');";

      $str.= "\n  IF ( cexists ) THEN ";
      $str.= "\n     SELECT IFNULL(modified_by,0) into changedby FROM `".$tname."` WHERE ".$pkeycol." = OLD.".$pkeycol."; ";
      $str.= "\n  ELSE ";
      $str.= "\n     SELECT 0 INTO changedby; ";
      $str.= "\n  END IF; ";

      // Need to use a created_by field if modified was NULL. i.e. It was created but never modified.
      // Really should get current user from session but we are in the DB so how?
      // Look in #__session table to see if we can work it out and relate
      // the db session with the originating Joomla connection.

      // Check for an alias field for key link field.
      $db->setQuery("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME like '".$tname."' AND COLUMN_NAME = 'alias' ");
      $res = $db->loadResult();

      if ( ! empty($res) ) {
         $aliascol = 'alias';
      } else {
         $aliascol = $pkeycol;
      }

      // Special case since we do not use it_people alias yet.
      if ( substr($tname, -9) == 'it_people') {
         $aliascol = $pkeycol;
      }

      if (  $cols[0] == '(array) All' || $cols == '(array) All' || $cols == 'All' || empty($cols[0]) || empty($cols) || in_array('All', $cols) ) {
         $db = JFactory::getDBO();
         $db->setQuery("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name like '".$tname."' ");
         $res = $db->loadObjectList();

         foreach($res as $row) {
            $col = $row->COLUMN_NAME;
            $str.= "\n  IF (OLD.".$col." IS NOT NULL) THEN";

           if ( $ftranslate ) {
               $statement = 0;
               if ( $col == 'state' ) {
                  $statement = 1;
                  $str .= self::trans_state('D');
               }
               if ( $col == 'checked_out' || ( $col == 'created_by' && $row->DATA_TYPE == 'int' ) || ($col == 'modified_by' && $row->DATA_TYPE == 'int') ) {
                  $statement = 1;
                  $str.= "\n   SELECT CONCAT(name, ' (', username, ')') into oval FROM `".$prefix."users` WHERE OLD.".$col." != 0 AND id = OLD.".$col.";" ;
               }

               if ( $statement == 1 ) {
                  $str.= "\n     INSERT INTO ".$ctable;
                  $str.= "\n        (table_name, column_name, column_type, component, old_value, action, row_key, row_key_link, change_by, change_date)";
                  $str.= "\n     VALUES ('".$tname."', '".$col."', '".$row->DATA_TYPE."', component, oval, 'DELETE', OLD.".$pkeycol.", OLD.".$aliascol.", changedby, NOW());";
               } else {
                  $str.= "\n     INSERT INTO ".$ctable;
                  $str.= "\n        (table_name, column_name, column_type, component, old_value, action, row_key, row_key_link, change_by, change_date)";
                  $str.= "\n     VALUES ('".$tname."', '".$col."', '".$row->DATA_TYPE."', component, OLD.".$col.", 'DELETE', OLD.".$pkeycol.", OLD.".$aliascol.", changedby, NOW());";
               }
            } else {
               $str.= "\n     INSERT INTO ".$ctable;
               $str.= "\n        (table_name, column_name, column_type, component, old_value, action, row_key, row_key_link, change_by, change_date)";
               $str.= "\n     VALUES ('".$tname."', '".$col."', '".$row->DATA_TYPE."', component, OLD.".$col.", 'DELETE', OLD.".$pkeycol.", OLD.".$aliascol.", changedby, NOW());";
            }
            $str.= "\n   END IF;";
         }
      } else {
         foreach($cols as $col) {
            $db->setQuery("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME like '".$tname."'  AND COLUMN_NAME = '".$col."' ");
            $ctype = $db->loadResult();

            $str.= "\n  IF (OLD.".$col." IS NOT NULL) THEN";
           if ( $ftranslate ) {
               $statement = 0;
               if ( $col == 'state' ) {
                  $statement = 1;
                  $str .= self::trans_state('D');
               }
               if ( $col == 'checked_out' || ( $col == 'created_by' && $ctype == 'int' ) || ($col == 'modified_by' && $ctype == 'int') ) {
                  $statement = 1;
                  $str.= "\n   SELECT CONCAT(name, ' (', username, ')') into oval FROM `".$prefix."users` WHERE OLD.".$col." != 0 AND id = OLD.".$col.";" ;
               }

               if ( $statement == 1 ) {
                  $str.= "\n     INSERT INTO ".$ctable;
                  $str.= "\n        (table_name, column_name, column_type, component, old_value, action, row_key, row_key_link, change_by, change_date)";
                  $str.= "\n     VALUES ('".$tname."', '".$col."', '".$ctype."', component, oval, 'DELETE', OLD.".$pkeycol.", OLD.".$aliascol.", changedby, NOW());";
               } else {
                  $str.= "\n     INSERT INTO ".$ctable;
                  $str.= "\n        (table_name, column_name, column_type, component, old_value, action, row_key, row_key_link, change_by, change_date)";
                  $str.= "\n     VALUES ('".$tname."', '".$col."', '".$ctype."', component, OLD.".$col.", 'DELETE', OLD.".$pkeycol.", OLD.".$aliascol.", changedby, NOW());";
               }
            } else {
               $str.= "\n     INSERT INTO ".$ctable;
               $str.= "\n        (table_name, column_name, column_type, component, old_value, action, row_key, row_key_link, change_by, change_date)";
               $str.= "\n     VALUES ('".$tname."', '".$col."', '".$ctype."', component, OLD.".$col.", 'DELETE', OLD.".$pkeycol.", OLD.".$aliascol.", changedby, NOW());";
            }
            $str.= "\n   END IF;";
         }
      }
      return $str;
   }

   /**
    * @param $type
    * @return null|string
    */
   static function trans_state($type)
   {
      $str = NULL;

      if ( $type == 'U' || $type == 'I' ) {
         $str.= "\n   IF (NEW.state is not null) THEN";
         $str.= "\n      CASE NEW.state";
         $str.= "\n         WHEN  0 THEN SET nval = '".JText::_('JUNPUBLISHED')."';";
         $str.= "\n         WHEN  1 THEN SET nval = '".JText::_('JPUBLISHED')."';";
         $str.= "\n         WHEN  2 THEN SET nval = '".JText::_('JARCHIVED')."';";
         $str.= "\n         WHEN -1 THEN SET nval = '".JText::_('JARCHIVED')."';";
         $str.= "\n         WHEN -2 THEN SET nval = '".JText::_('JTRASHED')."';";
//         $str.= "\n         WHEN -3 THEN SET nval = '".JText::_('JREPORT')."';";
         $str.= "\n      END CASE;";
         $str.= "\n   END IF;";
      }

      if ( $type == 'U' || $type == 'D' ) {
         $str.= "\n   IF (OLD.state is not null) THEN";
         $str.= "\n      CASE OLD.state";
         $str.= "\n         WHEN  0 THEN SET oval = '".JText::_('JUNPUBLISHED')."';";
         $str.= "\n         WHEN  1 THEN SET oval = '".JText::_('JPUBLISHED')."';";
         $str.= "\n         WHEN  2 THEN SET nval = '".JText::_('JARCHIVED')."';";
         $str.= "\n         WHEN -1 THEN SET oval = '".JText::_('JARCHIVED')."';";
         $str.= "\n         WHEN -2 THEN SET oval = '".JText::_('JTRASHED')."';";
//         $str.= "\n         WHEN -3 THEN SET nval = '".JText::_('JREPORT')."';";
         $str.= "\n      END CASE;";
         $str.= "\n   END IF;";
      }

      return $str;
   }


   /*
    * Method to remove a trigger.
    *
    */
   /**
    * @param $trig
    */
   public static function rem_trigger ($trig)
   {
      $db = JFactory::getDBO();

      $query= "DROP TRIGGER IF EXISTS `".$trig."`";
      $db->setQuery($query);
      $db->execute();
   }

  /*
    * Create the trigger in the database.
    *
    */
   /**
    * @param $data
    */
   static function applyTrigger( $data )
   {
      $db = JFactory::getDBO();
      $query = strip_tags($data['trigger_text']);
      if (empty($query) ) {
         $app = JFactory::getApplication();
         $app->enqueueMessage(JText::_('COM_ISSEUTRACKER_NO_TRIGGERTEXT_MSG'), 'error');
      }

      $trig = $data['trigger_name'];
      $db->setQuery("DROP TRIGGER IF EXISTS `".$trig."`");
      $db->execute();

      $db->setQuery($query);
      $db->execute();
   }
}