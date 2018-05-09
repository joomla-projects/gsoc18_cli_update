<?php
/*
 *
 * @Version       $Id: script.php 2275 2016-03-21 16:09:48Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.11
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-03-21 16:09:48 +0000 (Mon, 21 Mar 2016) $
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Required for Joomla 3.0
if (!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}

//Import filesystem libraries. Perhaps not necessary, but does not hurt
JLoader::import( 'joomla.filesystem.file' );
JLoader::import("joomla.filesystem.folder");

/**
 * Class com_issuetrackerInstallerScript
 */
class com_issuetrackerInstallerScript
{
   /*
    * The release value would ideally be extracted from <version> in the manifest file,
    * but at preflight, the manifest file exists only in the uploaded temp folder.
    */
   private $release  = '1.6.11';     // Main release version
//   private $prelease = '1.6.10';     // Plugin release version
//   private $pname = 'com_issuetracker';

   /* @var array Obsolete files and folders to remove from the release */
   private $oldFiles = array(
      'files'  => array(
         // Old smart search file.
         'administrator/language/en-GB/en-GB/Smart Search - Issue Tracker.ini',
         'components/com_issuetracker/views/form/tmpl/edit_product_details.php',
         'components/com_issuetracker/views/form/tmpl/edit_captcha.php',
         'administrator/components/com_issuetracker/views/itissues/tmpl/edit25_audit_details.php',
         'administrator/components/com_issuetracker/views/itissues/tmpl/default_audit_details.php',
         'administrator/components/com_issuetracker/views/itpeople/tmpl/edit25_audit_details.php',
         'administrator/components/com_issuetracker/views/itpeople/tmpl/default_audit_details.php',
         'administrator/components/com_issuetracker/views/itprojects/tmpl/edit25_audit_details.php',
         'administrator/components/com_issuetracker/views/itprojects/tmpl/default_audit_details.php',
         'administrator/components/com_issuetracker/views/attachment/tmpl/edit25_audit_details.php',
         'administrator/components/com_issuetracker/views/attachment/tmpl/default_audit_details.php',
         'administrator/components/com_issuetracker/views/email/tmpl/edit25_audit_details.php',
         'administrator/components/com_issuetracker/views/email/tmpl/default_audit_details.php',
         'administrator/components/com_issuetracker/views/customfield/tmpl/edit25_audit_details.php',
         'administrator/components/com_issuetracker/views/customfield/tmpl/default_audit_details.php',
         'administrator/components/com_issuetracker/views/customfield/tmpl/default25_audit_details.php',
         'administrator/components/com_issuetracker/views/customfieldgroup/tmpl/edit25_audit_details.php',
         'administrator/components/com_issuetracker/views/customfieldgroup/tmpl/default_audit_details.php',
         'administrator/components/com_issuetracker/views/customfieldgroup/tmpl/default25_audit_details.php',
         'administrator/components/com_issuetracker/views/itpriority/tmpl/edit25_audit_details.php',
         'administrator/components/com_issuetracker/views/itpriority/tmpl/default_audit_details.php',
         'administrator/components/com_issuetracker/views/itroles/tmpl/edit25_audit_details.php',
         'administrator/components/com_issuetracker/views/itroles/tmpl/default_audit_details.php',
         'administrator/components/com_issuetracker/views/itstatus/tmpl/edit25_audit_details.php',
         'administrator/components/com_issuetracker/views/itstatus/tmpl/default_audit_details.php',
         'administrator/components/com_issuetracker/views/ittypes/tmpl/edit25_audit_details.php',
         'administrator/components/com_issuetracker/views/ittypes/tmpl/default_audit_details.php',
         'administrator/components/com_issuetracker/views/paction/tmpl/edit25_audit_details.php',
         'administrator/components/com_issuetracker/views/paction/tmpl/default_audit_details.php',
         'administartor/components/com_issuetracker/views/cpanel/tmpl/desktop.ini',
         // Old Hathor files.
         'administrator/components/com_issuetracker/hathor/attachment/default_audit_details.php',
         'administrator/components/com_issuetracker/hathor/customfield/default_audit_details.php',
         'administrator/components/com_issuetracker/hathor/customfield/edit_audit_details.php',
         'administrator/components/com_issuetracker/hathor/customfieldgroup/default_audit_details.php',
         'administrator/components/com_issuetracker/hathor/email/default_audit_details.php',
         'administrator/components/com_issuetracker/hathor/itissues/default_audit_details.php',
         'administrator/components/com_issuetracker/hathor/itissues/edit_audit_details.php',
         'administrator/components/com_issuetracker/hathor/itpeople/default_audit_details.php',
         'administrator/components/com_issuetracker/hathor/itpriority/default_audit_details.php',
         'administrator/components/com_issuetracker/hathor/itprojects/default_audit_details.php',
         'administrator/components/com_issuetracker/hathor/itroles/default_audit_details.php',
         'administrator/components/com_issuetracker/hathor/itstatus/default_audit_details.php',
         'administrator/components/com_issuetracker/hathor/ittypes/default_audit_details.php',
         'administrator/components/com_issuetracker/hathor/paction/edit_audit_details.php',
         // In Hathor template itself
         'administrator/templates/hathor/html/com_issuetracker/attachment/default_audit_details.php',
         'administrator/templates/hathor/html/com_issuetracker/customfield/default_audit_details.php',
         'administrator/templates/hathor/html/com_issuetracker/customfield/edit_audit_details.php',
         'administrator/templates/hathor/html/com_issuetracker/customfieldgroup/default_audit_details.php',
         'administrator/templates/hathor/html/com_issuetracker/email/default_audit_details.php',
         'administrator/templates/hathor/html/com_issuetracker/itissues/default_audit_details.php',
         'administrator/templates/hathor/html/com_issuetracker/itissues/edit_audit_details.php',
         'administrator/templates/hathor/html/com_issuetracker/itpeople/default_audit_details.php',
         'administrator/templates/hathor/html/com_issuetracker/itpriority/default_audit_details.php',
         'administrator/templates/hathor/html/com_issuetracker/itprojects/default_audit_details.php',
         'administrator/templates/hathor/html/com_issuetracker/itroles/default_audit_details.php',
         'administrator/templates/hathor/html/com_issuetracker/itstatus/default_audit_details.php',
         'administrator/templates/hathor/html/com_issuetracker/ittypes/default_audit_details.php',
         'administrator/templates/hathor/html/com_issuetracker/paction/edit_audit_details.php',
     ),
      'folders' => array(
         // Old plugins folder directory
         'administrator/components/com_issuetracker/plugins',
         // Redundant git directory
         'administrator/components/com_issuetracker/views/cpanel/tmpl/.git',
      )
   );

   private $compCliScripts = array(
      'issuetracker_email_fetch.php',
      'issuetracker_email_summary.php',
      'issuetracker_email_overdue.php',
      'issuetracker_autoclose.php',
   );

   private $hathorDirs = array(
      'attachment','attachments','common','email','emails',
      'itissues','itissueslist','itloglist',
      'itpeople','itpeoplelist','itpriority','itprioritylist',
      'itprojects','itprojectslist','itroles','itroleslist',
      'itstatus','itstatuslist','ittypes','ittypeslist',
      'jchanges','jchange','jtrigger','jtriggers',
      'support','cpanel','dbtasks','documentation',
      'customfield','customfields','customfieldgroup',
      'customfieldgroups','paction','pactions'
   );

   /**
    * $parent is the class calling this method.
    * $type is the type of change (install, update or discover_install, not uninstall).
    * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
    * If preflight returns false, Joomla will abort the update and undo everything already done.
    *
    *
    * @param $type
    * @param $parent
    * @return bool
    */
   function preflight( $type, $parent ) {
      // this version does not work with Joomla releases prior to 2.5
      // abort if the current Joomla release is older
      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '2.5', 'lt' ) ) {
         $app = JFactory::getApplication();
         $app->enqueueMessage('Installation was unsuccessful because the version of Joomla is not supported by this version of Issue Tracker.', 'error');
         return false;
      }

      // abort if the release being installed is not newer than the currently installed version
      if ( $type == 'update' ) {
         $oldRelease = $this->getParam('version');
         $rel = ' from ' . $oldRelease . ' to ' . $this->release;
         if ( version_compare( $this->release, $oldRelease, 'lt' ) ) {
            Jerror::raiseWarning(null, 'Incorrect version sequence. Cannot upgrade ' . $rel);
            return false;
         }
      }
      else { $rel = ' Version ' .$this->release; }
      echo '<p>' . JText::_('COM_ISSUETRACKER_PREFLIGHT_' . strtoupper($type) . '_TEXT') . $rel . '</p>';
      return true;
   }

   /**
    * $parent is the class calling this method.
    * install runs after the database scripts are executed.
    * If the extension is new, the install method is run.
    * If install returns false, Joomla will abort the install and undo everything already done.
    *
    *
    * @param $parent
    */
   function install( $parent ) {
      echo '<p>' . JText::_('COM_ISSUETRACKER_INSTALL_TEXT') . ' to version: ' . $this->release . '</p>';

      $manifest = $parent->get("manifest");
      $parent = $parent->getParent();
      $source = $parent->getPath("source");

      $installer = new JInstaller();

      // Install plugins
      foreach($manifest->plugins->plugin as $plugin) {
         $attributes = $plugin->attributes();
         $plg = $source . DS . $attributes['folder'].DS.$attributes['plugin'];
         // $plgname = $attributes['plugin'];
         $pname = $attributes['name'];
         $installer->install($plg);
         echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_PLUGIN_INSTALL_TEXT') . $pname. '</p>';
         $this->enable_plugin($pname);
      }

      // Install modules
      foreach($manifest->modules->module as $module) {
         $attributes = $module->attributes();
         $mod = $source . DS . $attributes['folder'].DS.$attributes['module'];
         $modname = $attributes['module'];
         $installer->install($mod);
         echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_MODULE_INSTALL_TEXT') . $modname. '</p>';
      }

      // Install CLI files.
      $this->_copyCliFiles($parent);

      $this->createDBobjects();

      // Since this is a fresh install create the default project and person.
      $this->checkDefEntries();

      $jversion = new JVersion();
      if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         // Create hathor template overrides.
         $this->cp_hathor_overrides();
         // Remove hathor src templates
         $this->rm_hathor_src();
      }

      // Since this is a fresh install set the Super user to be an issues_admin and a staff member so that
      // they avoid any db messages if they try and save an issue before setting real staff members.
      $this->set_admin_staff();

      // You can have the backend jump directly to the newly installed component configuration page
      // $parent->getParent()->setRedirectURL('index.php?option=com_issuetracker');
   }

   /*
    * $parent is the class calling this method.
    * update runs after the database scripts are executed.
    * If the extension exists, then the update method is run.
    * If this returns false, Joomla will abort the update and undo everything already done.
    */
   /**
    * @param $parent
    */
   function update( $parent ) {
      $manifest = $parent->get("manifest");
      $parent = $parent->getParent();
      $source = $parent->getPath("source");
      // $db = JFactory::getDbo();
      $installer = new JInstaller();

      // Install plugins
      foreach($manifest->plugins->plugin as $plugin) {
         $attributes = $plugin->attributes();
         $plg = $source . DS . $attributes['folder'].DS.$attributes['plugin'];
         // $plgname = $attributes['plugin'];
         $pname = $attributes['name'];
         $installer->install($plg);
         echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_PLUGIN_INSTALL_TEXT') . $pname. '</p>';
         $this->enable_plugin($pname);
      }

      // Install modules
      foreach($manifest->modules->module as $module) {
         $attributes = $module->attributes();
         $mod = $source . DS . $attributes['folder'].DS.$attributes['module'];
         $modname = $attributes['module'];
         $installer->install($mod);
         echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_MODULE_INSTALL_TEXT') . $modname. '</p>';
       }

      // Install CLI files.
      $this->_copyCliFiles($parent);

      // $this->check_itissues_constraints();

//      if ( $this->release == '1.2.0') $this->update_people();
      if ( version_compare( $this->release, '1.2.0', 'ge' ) ) $this->update_people();

      if ( version_compare( $this->release, '1.3.0', 'ge' ) ) $this->convertTable('#__it_projects','title');

      $jversion = new JVersion();
      if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         // Create hathor template overrides.
         $this->cp_hathor_overrides();
         // Remove hathor src templates
         $this->rm_hathor_src();
      }

      // Rerun the creation of create the default project and person.
      $this->checkDefEntries();

      $this->createDBobjects();

      // Populate progress table
      $this->populate_progress();

      echo '<p>' . JText::_('COM_ISSUETRACKER_UPDATE_TEXT') . ' version: ' . $this->release . '</p>';
   }

   /**
    * $parent is the class calling this method.
    * $type is the type of change (install, update or discover_install, not uninstall).
    * postflight is run after the extension is registered in the database.
    *
    *
    * @param $type
    * @param $parent
    */
   function postflight( $type, $parent ) {
      // set initial values for component parameters
      $params['comp_version'] = 'Component version ' . $this->release;
      $params['imap_site_base'] = JURI::root();
      $this->setParams( $params );

      // Update for 3.x schema tags
      $this->update_3xschema();

      // Update meta table
      $db = JFactory::getDbo();
      $query   = "INSERT INTO `#__it_meta` (id, version, type) VALUES(1, '". $this->release . "', 'component')";
      $query  .= " ON DUPLICATE KEY UPDATE version = '" . $this->release . "', type = 'component' ";
      $db->setQuery($query);
      $db->execute();

      // Remove obsolete files and folders
      $oldFiles = $this->oldFiles;
      $this->_removeOldFilesAndFolders($oldFiles);

      echo '<p>' . JText::_('COM_ISSUETRACKER_POSTFLIGHT_' . strtoupper($type) . '_TEXT') . ' version: ' . $this->release . '</p>';
      echo '<p style="color: #0000FF;">' . JText::_('COM_ISSUETRACKER_POSTFLIGHT_COMPLETION_UPDATE_TEXT'). '</p>';
   }

   /**
    * $parent is the class calling this method
    * uninstall runs before any other action is taken (file removal or database processing).
    *
    * @param $parent
    */
   function uninstall( $parent ) {
      $manifest = $parent->get("manifest");
      // $parent = $parent->getParent();
      // $source = $parent->getPath("source");

      $this->dropDBobjects();

      $installer = new JInstaller();
      $db = JFactory::getDbo();

      // Uninstall modules
      // echo "<PRE>";var_dump($manifest->modules);echo "</PRE>";
      foreach($manifest->modules->module as $module) {
         $attributes = $module->attributes();
         // $mod = $source . DS . $attributes['folder'].DS.$attributes['module'];
         $modname = $attributes['module'];
         $db->setQuery("SELECT `extension_id` FROM #__extensions WHERE `type` = 'module' AND `element` = '".$modname."'");
         $id = $db->loadResult();
         $installer->uninstall('module',$id);
         echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_MODULE_UNINSTALL_TEXT') . $modname. '</p>';
      }

      // Uninstall plugins
      // echo "<PRE>";var_dump($manifest->plugins);echo "</PRE>";
      foreach($manifest->plugins->plugin as $plugin) {
         $attributes = $plugin->attributes();
         $pname = $attributes['name'];
         $db->setQuery("SELECT `extension_id` FROM #__extensions WHERE `type` = 'plugin' AND `element` = 'issuetracker' AND name ='".$pname."'");
         $id = $db->loadResult();
         $installer->uninstall('plugin',$id);
         echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_PLUGIN_UNINSTALL_TEXT') . $pname.'</p>';
      }

      // Remove CLI files.
      $this->_remCliFiles($parent);


      // Remove CLI files.
      $this->_remCliFiles($parent);

      echo '<p>' . JText::_('COM_ISSUETRACKER_UNINSTALL_TEXT') . ' version: ' . $this->release . '</p>';

      $jversion = new JVersion();
      if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         $this->rm_hathor_overrides();
         $this->rem_3xschema();
      }
   }

   /**
    * get a variable from the manifest file (actually, from the manifest cache).
    *
    *
    * @param $name
    * @return mixed
    */
   function getParam( $name ) {
      $db = JFactory::getDbo();
      $db->setQuery('SELECT manifest_cache FROM #__extensions WHERE element = "com_issuetracker" AND type="component"');
      $manifest = json_decode( $db->loadResult(), true );
      return $manifest[ $name ];
   }

   /**
    * sets parameter values in the component's row of the extension table
    *
    *
    * @param $param_array
    */
   function setParams($param_array) {
      if ( count($param_array) > 0 ) {
         // read the existing component value(s)
         $db = JFactory::getDbo();
         $db->setQuery('SELECT params FROM #__extensions WHERE type= "component" AND element = "com_issuetracker"');
         $params = json_decode( $db->loadResult(), true );
         // add the new variable(s) to the existing one(s)
         foreach ( $param_array as $name => $value ) {
            $params[ (string) $name ] = (string) $value;
         }
         // store the combined new and existing values back as a JSON string
         $paramsString = json_encode( $params );
         $db->setQuery('UPDATE #__extensions SET params = ' .
            $db->quote( $paramsString ) . ' WHERE type="component" AND element = "com_issuetracker"' );
            $db->execute();
      }
   }

   /**
    * @param $plgname
    */
   function enable_plugin($plgname)
   {
      // Check if plugin installed.
      $db = JFactory::getDBO();
      $db->setQuery("SELECT extension_id, enabled FROM #__extensions WHERE type='plugin' AND element LIKE '%issuetracker' AND name='".$plgname."' ");
      $row = $db->loadObject();

      $plugin_id = $row->extension_id;
      $plugin_enabled = $row->enabled;
      if ( $plugin_id && !$plugin_enabled ){
         $db->setQuery( "UPDATE #__extensions SET enabled='1', access='1' WHERE extension_id='$plugin_id' ");
         $db->execute();
      }
      echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_PLUGIN_ENABLED_TEXT') . $plgname . '</p>';
   }

   function set_admin_staff()
   {
      // Update people table.  Only used for a new install.
      $db      = JFactory::getDbo();
      $query   = "UPDATE #__it_people SET staff=1, issues_admin=1 WHERE person_name='Super User'";
      $db->setQuery($query);
      $db->execute();
   }

   function update_people()
   {
      // Only applied to release 1.2.0 and above
      // Update people table if we need to.
      $db = JFactory::getDbo();
      $query  = "select count(*) from #__it_people where registered = '1'";
      $db->setQuery($query);
      $cnt = $db->loadResult();

      // Check if we have any registered entries in our people table.  If we do then this must be an existing install
      // so we do not have to do anything.
      if ( $cnt == '0' ) {
         $db->setQuery('UPDATE #__it_people SET registered = "1" WHERE id > 20 ');
         $db->execute();

         $db->setQuery('UPDATE #__it_people SET user_id = id WHERE registered = "1" ');
         $db->execute();

         // Add check to see if any staff already
         $query  = "select count(*) from #__it_people where staff = '1'";
         $db->setQuery($query);
         $cnts = $db->loadResult();

         if ( $cnts == '0') {
            $db->setQuery('UPDATE #__it_people SET staff = 1 WHERE user_id IN (SELECT distinct assigned_to_person_id FROM #__it_issues)');
            $db->execute();
         }

         // $app = JFactory::getApplication();
         // $prefix = $app->getCfg('dbprefix');
         // $prefix = $app->get('dbprefix');
         $prefix = $db->getPrefix();
         $table = $prefix . 'it_people';

         // Add check to see if UQ exists
         $query   = "select count(*) from information_schema.TABLE_CONSTRAINTS ";
         $query  .= "where table_name = '".$table."' ";
         $query  .= "and constraint_name = '".$table."_userid_uk"."'";
         $db->setQuery($query);
         $cntu    = $db->loadResult();

         if ( $cntu == '0') {
            $db->setQuery("ALTER TABLE #__it_people ADD UNIQUE KEY `#__it_people_userid_uk` (`user_id`)");
            $db->execute();
         }
      }
   }

   /**
    * Removes obsolete files and folders
    *
    * @param array $oldFiles
    */
   private function _removeOldFilesAndFolders($oldFiles)
   {
      // Remove files
      JLoader::import('joomla.filesystem.file');
      if(!empty($oldFiles['files'])) foreach($oldFiles['files'] as $file) {
         $f = JPATH_ROOT.'/'.$file;
         if(!JFile::exists($f)) continue;
         JFile::delete($f);
      }

      // Remove folders
      JLoader::import('joomla.filesystem.file');
      if(!empty($oldFiles['folders'])) foreach($oldFiles['folders'] as $folder) {
         $f = JPATH_ROOT.'/'.$folder;
         if(!JFolder::exists($f)) continue;
         JFolder::delete($f);
      }
   }


   private function update_3xschema()
   {
      // Needed since we have to perform some specific updates if this is a Joomla 3.x install/update.

      $db = JFactory::getDbo();

      $jversion = new JVersion();
      if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         // If entries do not exist in #__content_types add them
         $query  = "SELECT COUNT(*) FROM `#__content_types` ";
         $query .= " WHERE type_title IN ('Issuetracker Issue','Issuetracker Project') ";
         $db->setQuery($query);
         $cnt = $db->loadResult();

         if ( $cnt == 0  ) {
            $query  = 'INSERT INTO `#__content_types` ';
            $query .= '(`type_title`, `type_alias`, `table`, `rules`, `field_mappings`,`router`) VALUES ';
            $query .= "('Issuetracker Issue','com_issuetracker.itissue',";
            $query .= '\'{"special":{"dbtable":"#__it_issues","key":"id","type":"Issue","prefix":"IssueTrackerTable","config":"array()"}}\',';
            $query .= "'',";
            $query .= '\'{"common":{"core_content_item_id":"id","core_title":"issue_summary","core_state":"state","core_alias":"alias","core_created_by_alias":"created_by","core_created_time":"created_time","core_modified_time":"modified_time","core_body":"issue_description", "core_hits":"hits","core_publish_up":"null","core_publish_down":"null","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"null", "core_images":"null", "core_urls":"null", "core_version":"null", "core_ordering":"ordering", "core_metakey":"null", "core_metadesc":"null", "core_catid":"null", "core_xreference":"null", "asset_id":"asset_id"}, "special":{}}\',';
            $query .= "'IssuetrackerHelperRoute::getIssueRoute'),";
            $query .= "('Issuetracker Project', 'com_issuetracker.itproject',";
            $query .= '\'{"special":"{dbtable":"#__it_projects","key":"id","type":"Project","prefix":"IssueTrackerTable","config":"array()"}}\',';
            $query .= "'',";
            $query .= '\'{"common":{"core_content_item_id":"id","core_title":"title","core_state":"state","core_alias":"alias","core_created_by_alias":"created_by","core_created_time":"created_on","core_modified_time":"modified_on","core_body":"description", "core_hits":"null","core_publish_up":"null","core_publish_down":"null","core_access":"access","core_params":"params","core_featured":"null","core_metadata":"metadata","core_language":"null","core_images":"null","core_urls":"null","core_version":"null","core_ordering":"ordering","core_metakey":"null","core_metadesc":"null","core_catid":"null","core_xreference":"null","asset_id":"asset_id"},"special": {"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"path","start_date":"start_date"}}\',';
            $query .= "'IssuetrackerHelperRoute::getProjectRoute');";
            $db->setQuery($query);
            $db->execute();

            echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_3XSCHEMA_UPDATED') . '</p>';
         }
      } else {
         // Nothing yet
      }
   }

   private function rem_3xschema()
   {
      // Needed since we have to perform some specific updates if this is a Joomla 3.x uninstall.

      $db = JFactory::getDbo();

      $jversion = new JVersion();
      if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         // If entries do not exist in #__content_types add them

         $query  = "DELETE FROM `#__ucm_content` WHERE `core_type_alias` IN ('com_issuetracker.itissue', 'com_issuetracker.itproject')";
         $db->setQuery($query);
         $db->execute();

         $query  = "DELETE FROM `#__contentitem_tag_map` WHERE `type_alias` IN ('com_issuetracker.itissue', 'com_issuetracker.itproject')";
         $db->setQuery($query);
         $db->execute();

         $query  = "DELETE a FROM `#__ucm_base` AS a INNER JOIN `#__content_types` AS b ON a.ucm_type_id = b.type_id WHERE b.type_alias IN ('com_issuetracker.itissue', 'com_issuetracker.itproject')";
         $db->setQuery($query);
         $db->execute();

         $query  = "DELETE FROM `#__content_types` WHERE type_alias IN ('com_issuetracker.itissue','com_issuetracker.itproject')";
         $db->setQuery($query);
         $db->execute();

         echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_3XSCHEMA_REMOVED') . '</p>';

      } else {
         // Nothing yet
      }
   }

/*
   function check_itissues_constraints()
   {
      // Needed if we ran a 1.0.0 or 1.0.1 version of the application.
      // If so we need to rename two it_issues constraints.
      $app = JFactory::getApplication();
      // Get database prefix.
      $prefix = $app->getCfg('dbprefix');
      $table = $prefix . 'it_issues';

      $db = JFactory::getDbo();
      $query  = "select count(*) from information_schema.REFERENTIAL_CONSTRAINTS ";
      $query .= "where table_name = '".$table."' ";
      $query .= "and SUBSTRING(constraint_name, 1, 15) != '".$table."'";
      $db->setQuery($query);
      $cnt = $db->loadResult();

      if ($cnt > 0 ) {
         // Get old prefix.  Needed in case used Akeeba was used and the old constraint prefix was different.
         // Assumes a 6 letter prefix including the underscore.
         $query  = 'select distinct substring(constraint_name,1,6) ';
         $query .= 'from information_schema.REFERENTIAL_CONSTRAINTS ';
         $query .= "where table_name = '".$table."' ";
         $query .= "and SUBSTRING(constraint_name, 1, 15) != '".$table."'";
         $db->setQuery($query);
         $oprefix = $db->loadResult();

         // Remove misnamed constraints.
         $query  = 'ALTER TABLE `#__it_issues` ';
         $query .= "DROP FOREIGN KEY ".$oprefix."it_people_priority_fk,";
         $query .= "DROP FOREIGN KEY ".$oprefix."it_people_status_fk";
         $db->setQuery($query);
         $db->execute();

         // Add the constraints back in
         $query  = 'ALTER TABLE `#__it_issues` ';
         $query .= ' ADD CONSTRAINT `#__it_issues_priority_fk` FOREIGN KEY (priority) REFERENCES `#__it_priority` (id) ON UPDATE RESTRICT ON DELETE RESTRICT,';
         $query .= ' ADD CONSTRAINT `#__it_issues_status_fk` FOREIGN KEY (status) REFERENCES `#__it_status` (id) ON UPDATE RESTRICT ON DELETE RESTRICT';
         $db->setQuery($query);
         $db->execute();

         echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_CONSTRAINTS_RENAMED') . '</p>';

      }
   }
*/
   /**
    * @param null $tname
    * @param string $type
    * @param string $event
    */
   function createAuditTrigger( $tname = NULL, $type = 'BEFORE', $event = 'INSERT' )
   {
      $events = array('INSERT','UPDATE','DELETE');
      $types   = array('BEFORE','AFTER');
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

      /* Create database trigger. */
      $trig = $tname;
      if ( $type = 'BEFORE' ) {
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
         switch ($event){
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
      IF ($tname == '#__it_projects') {
         $query.= "\n   IF (NEW.ACTUAL_END_DATE = '0000-00-00 00:00:00') THEN";
         $query.= "\n      SET NEW.ACTUAL_END_DATE := NULL;";
         $query.= "\n   END IF;";
      }

      IF ( $event == 'INSERT' ) {
         $query.= "\n   IF (NEW.ID IS NULL) THEN";
         $query.= "\n      SET NEW.ID := 0;";
         $query.= "\n   END IF;";
         $query.= "\n   IF (NEW.CREATED_ON IS NULL OR NEW.CREATED_ON = '0000-00-00 00:00:00') THEN";
         $query.= "\n      SET NEW.CREATED_ON := UTC_TIMESTAMP();";
         $query.= "\n   END IF; ";
         $query.= "\n   IF (NEW.CREATED_BY IS NULL OR NEW.CREATED_BY = '') THEN";
         $query.= "\n      SET NEW.CREATED_BY := USER();";
         $query.= "\n   END IF; ";
      } elseif ( $event == 'UPDATE' ) {
         $query.= "\n   IF (NEW.MODIFIED_ON IS NULL OR NEW.MODIFIED_ON = '0000-00-00 00:00:00') THEN";
         $query.= "\n      SET NEW.MODIFIED_ON := UTC_TIMESTAMP();";
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
    * Method to create a views
    * Need it here in case the Joomla admin user cannot create database views.
    *
    */
    function create_views()
    {
       $db = JFactory::getDbo();

      // Check if we can use views
      $res = $this->check_db_priv('CREATE VIEW');
      if ( ! $res ) {
         echo '<p style="color: #FF0000;">' . JText::_('COM_ISSUETRACKER_SKIPPING CREATION_DBVIEW_TEXT') . '</p>';
      } else {
         $query = "DROP VIEW IF EXISTS `#__it_project_view1`";
         $db->setQuery($query);
         $db->execute();

         $query = "CREATE VIEW `#__it_project_view1` AS ";
         $query.= "SELECT B.id, B.parent_id AS pid, C.level, C.title, C.access ";
         $query.= "FROM `#__it_projects` AS B, `#__it_projects` AS C ";
         $query.= "WHERE (B.lft BETWEEN C.lft AND C.rgt) ";
         $query.= "AND C.level != 0 ";
         $query.= "ORDER BY B.lft, C.lft ";
         $db->setQuery($query);
         $db->execute();

         $query = "DROP VIEW IF EXISTS `#__it_project_view2`";
         $db->setQuery($query);
         $db->execute();

         $query = "CREATE VIEW `#__it_project_view2` AS ";
         $query.= "SELECT id, GROUP_CONCAT(title ORDER BY level ASC SEPARATOR ' - ') AS title, access ";
         $query.= "FROM `#__it_project_view1` GROUP BY id ";
         $db->setQuery($query);
         $db->execute();

         echo '<p>' . JText::_('COM_ISSUETRACKER_VIEWS_CREATED_TEXT') . '</p>';
      }
   }

   /**
    * Method to populate the new progress table from the existing issues table.
    *
    *
    */
    function populate_progress()
    {
       // $user = JFactory::getUser();
       $db = JFactory::getDbo();
       // $query  = $db->getQuery(true);

       // Check if we have already populated the progress table.
       $query = "SELECT count(*) from `#__it_progress` ";
       $db->setQuery($query);
       $nrows = $db->loadResult();
       if ( $nrows > 0 ) {
          echo '<p>' . JText::_('COM_ISSUETRACKER_PROGRESS_TABLE_ALREADY_POPULATED') . '</p>';
          return;
       }

       // Set state to unpublished and get private/public state from the issue itself.
       // $query  = $db->getQuery(true);
       $query  = "insert into `#__it_progress` (issue_id, alias, progress, public, state, lineno, access, created_by, created_on, modified_by, modified_on)";
       $query .= " select id, alias, progress, public, 0, 1, access, created_by, created_on, modified_by, modified_on ";
       $query .= " from `#__it_issues` ";
       $query .= " where progress IS NOT NULL and progress != '' ";
       $db->setQuery($query);
       $db->execute();

       $query = "SELECT count(*) from `#__it_progress` ";
       $db->setQuery($query);
       $nrows = $db->loadResult();
       echo '<p>' . JText::sprintf('COM_ISSUETRACKER_PROGRESS_TABLE_POPULATED',$nrows) . '</p>';

       // Ensure that created_by has a valid entry  set it to system.
       $query ="UPDATE `#__it_progress` SET created_by = 'system' WHERE created_by = ''";
       $db->setQuery($query);
       $db->execute();


       // Now clean out it_issues table progress field.
       $query ="UPDATE `#__it_issues` SET progress = NULL";
       $db->setQuery($query);
       $db->execute();
       echo '<p>' . JText::_('COM_ISSUETRACKER_PROGRESS_ENTRY_RESET') . '</p>';

   }

   /**
    * Method to drop database objects
    *
    */
    function dropDBobjects()
    {
      $db = JFactory::getDbo();

      // Check if we can use routines
      $res = $this->check_db_priv('CREATE ROUTINE');
      if ( $res ) {
         $query = "DROP PROCEDURE IF EXISTS `#__add_it_sample_data`";
         $db->setQuery($query);
         $db->execute();

         $query = "DROP PROCEDURE IF EXISTS `#__create_sample_issues`";
         $db->setQuery($query);
         $db->execute();

         $query = "DROP PROCEDURE IF EXISTS `#__create_sample_people`";
         $db->setQuery($query);
         $db->execute();

         $query = "DROP PROCEDURE IF EXISTS `#__create_sample_projects`";
         $db->setQuery($query);
         $db->execute();

         $query = "DROP PROCEDURE IF EXISTS `#__remove_it_sample_data`";
         $db->setQuery($query);
         $db->execute();
      }

      $res = $this->check_db_priv('CREATE VIEW');
      if ( $res ) {
         $query = "DROP VIEW IF EXISTS `#__it_project_view1`";
         $db->setQuery($query);
         $db->execute();
         $query = "DROP VIEW IF EXISTS `#__it_project_view2`";
         $db->setQuery($query);
         $db->execute();
      }
   }

   function createDBobjects()
   {
      $db = JFactory::getDbo();
      $db->setQuery('UPDATE #__it_meta SET version = "'. $this->release . '", type ="component" ');
      $db->execute();

      // Check if we can use triggers
      $res = $this->check_db_priv('TRIGGER');
      if ( ! $res ) {
         echo '<p style="color: #FF0000;">' . JText::_('COM_ISSUETRACKER_SKIPPING CREATION_DBTRIG_TEXT') . '</p>';
      } else {
         /*
          * Create database triggers.
          */
         $tables = array(
            '#__it_projects','#__it_issues','#__it_people','#__it_roles',
            '#__it_status','#__it_types','#__it_priority','#__it_emails',
            '#__it_attachment', '#__it_progress', '#__it_custom_field',
            '#__it_custom_field_group'
         );

         foreach ($tables as $tab) {
            $this->createAuditTrigger ( $tab, 'BEFORE', 'INSERT');
            $this->createAuditTrigger ( $tab, 'BEFORE', 'UPDATE');
         }
      }

      // Create views
      $this->create_views();

      // Check if we can use database procedures
      $res = $this->check_db_priv('CREATE ROUTINE');
      if ( ! $res ) {
         echo '<p style="color: #FF0000;">' . JText::_('COM_ISSUETRACKER_SKIPPING CREATION_DBPROCS_TEXT') . '</p>';
      } else {
         /*
          * Create procedures to handle sample data.
          */

         $query="DROP PROCEDURE IF EXISTS `#__create_sample_projects`;";
         $db->setQuery($query);
         $db->execute();

         $query ="create procedure `#__create_sample_projects`()";
         $query.= "\nbegin";

         $query.= "\nDECLARE rid INT; ";
         $query.= "\nDECLARE rtop INT; ";
         $query.= "\nDECLARE ilft INT; ";
         $query.= "\nDECLARE irgt INT; ";
         $query.= "\nSELECT id, rgt from `#__it_projects` WHERE title = 'Root' INTO rid, rtop; ";

         $query.= "\nSET ilft=rtop; ";
         $query.= "\nSET irgt=ilft+1; ";
         $query.= "\ninsert into `#__it_projects` (id, title, description, parent_id, lft, rgt, level, start_date, target_end_date)";
         $query.= "\nvalues (3, 'New Payroll Rollout', 'New Payroll Rollout', rid, ilft, irgt, 1, date_sub(now(), interval 150 day), date_add(now(), interval 15 day));";

         $query.= "\nSET ilft=irgt+1; ";
         $query.= "\nSET irgt=irgt+2;";
         $query.= "\ninsert into `#__it_projects` (id, title, description, parent_id, lft, rgt, level, start_date, target_end_date)";
         $query.= "\nvalues (4, 'Email Integration', 'Email Integration', rid, ilft, irgt, 1, date_sub(now(), interval 120 day), date_sub(now(), interval 60 day));";

         $query.= "\nSET ilft=irgt+1; ";
         $query.= "\nSET irgt=irgt+2;";
         $query.= "\ninsert into `#__it_projects` (id, title, description, parent_id, lft, rgt, level, start_date, target_end_date)";
         $query.= "\nvalues (5, 'Public Website Operational', 'Public Website Operational', rid, ilft, irgt, 1, date_sub(now(), interval 60 day), date_add(now(), interval 30 day));";

         $query.= "\nSET ilft=irgt+1; ";
         $query.= "\nSET irgt=irgt+2; ";
         $query.= "\ninsert into `#__it_projects` (id, title, description, parent_id, lft, rgt, level, start_date, target_end_date)";
         $query.= "\nvalues (6, 'Employee Satisfaction Survey', 'Employee Satisfaction Survey', rid, ilft, irgt, 1, date_sub(now(), interval 30 day), date_add(now(), interval 60 day));";

         $query.= "\nSET ilft=irgt+1; ";
         $query.= "\nSET irgt=irgt+2; ";
         $query.= "\ninsert into `#__it_projects` (id, title, description, parent_id, lft, rgt, level, start_date, target_end_date)";
         $query.= "\nvalues (7, 'Internal Infrastructure', 'Internal Infrastructure', rid, ilft, irgt, 1, date_sub(now(), interval 150 day), date_sub(now(), interval 30 day));";

         $query.= "\nSET rtop=irgt+1; ";
         $query.= "\nUPDATE `#__it_projects` SET rgt = rtop WHERE id = rid; ";

         $query.= "\n UPDATE `#__it_projects` SET access = 1, state = 1 WHERE id > 2 AND id < 8;";

         $query.= "\ncommit;";
         $query.= "\nend;";
         $db->setQuery($query);
         $db->execute();

         $query="DROP PROCEDURE IF EXISTS `#__create_sample_people`;";
         $db->setQuery($query);
         $db->execute();

         $query="create procedure `#__create_sample_people`()";
         $query.= "\nbegin";
         $query.= "\ninsert into `#__it_people` (id, person_name, person_email, registered, person_role, username, assigned_project)";
         $query.= "\nvalues (2, 'Thomas Cobley', 'tom.cobley@bademail.com', '0', '1', 'tcobley', null), ";
         $query.= "\n (3, 'Harry Hawke', 'harry.hawke@bademail.com', '0', '4', 'hhawke', null), ";
         $query.= "\n (4, 'Tom Pearce', 'tom.pearce@bademail.com', '0', '4', 'tpearce', null), ";
         $query.= "\n (5, 'Bill Brewer', 'bill.brewer@bademail.com', '0', '3', 'bbrewer', 7), ";
         $query.= "\n (6, 'Jan Stewer', 'jan.stewer@bademail.com', '0', '3', 'jstewer', 3), ";
         $query.= "\n (7, 'Peter Gurney', 'peter.gurney@bademail.com', '0', '3', 'pgurney', 4), ";
         $query.= "\n (8, 'Peter Davy', 'peter.davy@bademail.com', '0', '3', 'pdavy', 5), ";
         $query.= "\n (9, 'Daniel Whiddon', 'daniel.whiddon@bademail.com', '0', '3', 'dwhiddon', 6), ";
         $query.= "\n (10, 'Jack London', 'jack.london@bademail.com', '0', '5', 'jlondon', 7), ";
         $query.= "\n (11, 'Mark Tyne', 'mark.tyne@bademail.com', '0', '5', 'mtyne', 7), ";
         $query.= "\n (12, 'Jane Kerry', 'jane.kerry@bademail.com', '0', '5', 'jkerry', 6), ";
         $query.= "\n (13, 'Olive Pope', 'olive.pope@bademail.com', '0', '5','opope', 3), ";
         $query.= "\n (14, 'Russ Sanders', 'russ.sanders@bademail.com', '0', '5', 'rsanders', 4), ";
         $query.= "\n (15, 'Tucker Uberton', 'tucker.uberton@bademail.com', '0', '5', 'ruberton', 4), ";
         $query.= "\n (16, 'Vicky Mitchell', 'vicky.mitchell@bademail.com', '0', '5', 'vmitchell', 5), ";
         $query.= "\n (17, 'Scott Tiger', 'scott.tiger@bademail.com', '0', '5', 'stiger', 5),";
         $query.= "\n (18, 'John Gilpin', 'john.gilpin@bademail.com', '0', '5', 'jgilpin', 5);";
         $query.= "\ncommit;";
         $query.= "\nend;";
         $db->setQuery($query);
         $db->execute();

         $query= "DROP PROCEDURE IF EXISTS `#__create_sample_issues`;";
         $db->setQuery($query);
         $db->execute();

         // The issues samples changed in release 1.2.0 since the assigned_to field has to now be to a registered user.
         $query= "create procedure `#__create_sample_issues`()";
         $query.= "\nbegin";
         $query.= "\ninsert into `#__it_issues`";
         $query.= "\n(id, issue_summary, issue_description,alias,issue_type,";
         $query.= "\nidentified_by_person_id, identified_date,";
         $query.= "\nrelated_project_id, assigned_to_person_id, status, priority,";
         $query.= "\ntarget_resolution_date, actual_resolution_date, resolution_summary)";
         $query.= "\nvalues";
         $query.= "\n(1, 'Midwest call center servers have no failover due to Conn Creek plant fire',null,'DAAAAAAAA1','1',";
         $query.= "\n6, date_sub(now(), interval 80 day),";
         $query.= "\n4, null, '1', '3', date_sub(now(), interval 73 day), date_sub(now(), interval 73 day), null),";
         $query.= "\n(2, 'Timezone ambiguity in some EMEA regions is delaying bulk forwarding to mirror sites',null,'DAAAAAAAA2','1',";
         $query.= "\n6, date_sub(now(), interval 100 day),";
         $query.= "\n4, null, '4', '2', date_sub(now(), interval 80 day),null,null),";
         $query.= "\n(3, 'Some vendor proposals lack selective archiving and region-keyed retrieval sections',null,'DAAAAAAAA3','1',";
         $query.= "\n6, date_sub(now(), interval 110 day),";
         $query.= "\n4, null, '1', '3', date_sub(now(), interval 90 day), date_sub(now(), interval 95 day), null),";
         $query.= "\n(4, 'Client software licenses expire for Bangalore call center before cutover',null,'DAAAAAAAA4','1',";
         $query.= "\n1, date_sub(now(), interval 70 day),";
         $query.= "\n4, null, '1', '1', date_sub(now(), interval 60 day), date_sub(now(), interval 66 day),'Worked with HW, applied patch set.'),";
         $query.= "\n(5, 'Holiday coverage for DC1 and DC3 not allowed under union contract, per acting steward at branch 745',null,'DAAAAAAAA5','1',";
         $query.= "\n1, date_sub(now(), interval 100 day),";
         $query.= "\n4, null, '1', '1', date_sub(now(), interval 90 day), date_sub(now(), interval 95 day), 'Worked with HW, applied patch set.'),";
         $query.= "\n(6, 'Review rollout schedule with HR VPs/Directors',null,'DAAAAAAAA6','1',";
         $query.= "\n8, date_sub(now(), interval 30 day),";
         $query.= "\n6, null, '1', '3', date_sub(now(), interval 15 day), date_sub(now(), interval 20 day),null),";
         $query.= "\n(7, 'Distribute translated categories and questions for non-English regions to regional team leads',null,'DAAAAAAAA7','1',";
         $query.= "\n8, date_sub(now(), interval 2 day),";
         $query.= "\n6, null, '4', '3', date_add(now(), interval 10 day), null,null),";
         $query.= "\n(8, 'Provide survey FAQs to online newsletter group',null,'DAAAAAAAA8','1',";
         $query.= "\n1, date_sub(now(), interval 10 day),";
         $query.= "\n6, null, '4', '3', date_add(now(), interval 20 day), null,null),";
         $query.= "\n(9, 'Need better definition of terms like work group, department, and organization for categories F, H, and M-W',null,'DAAAAAAAA9','1',";
         $query.= "\n1, date_sub(now(), interval 8 day),";
         $query.= "\n6, null, '4', '2', date_add(now(), interval 15 day), null,null),";
         $query.= "\n(10, 'Legal has asked for better definitions on healthcare categories for Canadian provincial regs compliance',null,'DAAAAAAA10','1',";
         $query.= "\n1, date_sub(now(), interval 10 day),";
         $query.= "\n6, null, '1', '3', date_add(now(), interval 20 day), date_sub(now(), interval 1 day),null),";
         $query.= "\n(11, 'Action plan review dates conflict with effectivity of organizational consolidations for Great Lakes region',null,'DAAAAAAA11','1',";
         $query.= "\n1, date_sub(now(), interval 9 day),";
         $query.= "\n6, null, '4', '3', date_add(now(), interval 45 day), null,null),";
         $query.= "\n(12, 'Survey administration consulting firm requires indemnification release letter from HR SVP',null,'DAAAAAAA12','1',";
         $query.= "\n1, date_sub(now(), interval 30 day),";
         $query.= "\n6, null, '1', '2', date_sub(now(), interval 15 day), date_sub(now(), interval 17 day), null),";
         $query.= "\n(13, 'Facilities, Safety health-check reports must be signed off before capital asset justification can be approved',null,'DAAAAAAA13','1',";
         $query.= "\n4, date_sub(now(), interval 145 day),";
         $query.= "\n7, null, '1', '3', date_sub(now(), interval 100 day), date_sub(now(), interval 110 day),null),";
         $query.= "\n(14, 'Cooling and Power requirements exceed 90% headroom limit -- variance from Corporate requested',null,'DAAAAAAA14','1',";
         $query.= "\n4, date_sub(now(), interval 45 day),";
         $query.= "\n7, null, '1', '1', date_sub(now(), interval 30 day), date_sub(now(), interval 35 day),null),";
         $query.= "\n(15, 'Local regulations prevent Federal contracts compliance on section 3567.106B',null,'DAAAAAAA15','1',";
         $query.= "\n4, date_sub(now(), interval 90 day),";
         $query.= "\n7, null, '1', '1', date_sub(now(), interval 82 day), date_sub(now(), interval 85 day),null),";
         $query.= "\n(16, 'Emergency Response plan failed county inspector''s review at buildings 2 and 5',null,'DAAAAAAA16','1',";
         $query.= "\n4, date_sub(now(), interval 35 day),";
         $query.= "\n7, null, '4', '1', date_sub(now(), interval 5 day), null,null),";
         $query.= "\n(17, 'Training for call center 1st and 2nd lines must be staggered across shifts',null,'DAAAAAAA17','1',";
         $query.= "\n5, date_sub(now(), interval 8 day),";
         $query.= "\n3, null, '1', '3', date_add(now(), interval 10 day), date_sub(now(), interval 1 day),null),";
         $query.= "\n(18, 'Semi-monthly ISIS feed exceeds bandwidth of Mississauga backup site',null,'DAAAAAAA18','1',";
         $query.= "\n5, date_sub(now(), interval 100 day),";
         $query.= "\n3, null, '3', '3', date_sub(now(), interval 30 day), null,null),";
         $query.= "\n(19, 'Expat exception reports must be hand-reconciled until auto-post phaseout complete',null,'DAAAAAAA19','1',";
         $query.= "\n5, date_sub(now(), interval 17 day),";
         $query.= "\n3, null, '1', '1', date_add(now(), interval 4 day), date_sub(now(), interval 4 day),null),";
         $query.= "\n(20, 'Multi-region batch trial run schedule and staffing plan due to directors by end of phase review',null,'DAAAAAAA20','1',";
         $query.= "\n5, now(),";
         $query.= "\n3, null, '4', '1', date_add(now(), interval 15 day), null,null),";
         $query.= "\n(21, 'Auditors'' signoff requires full CSB compliance report',null,'DAAAAAAA21','1',";
         $query.= "\n5, date_sub(now(), interval 21 day),";
         $query.= "\n3, null, '4', '1', date_sub(now(), interval 7 day), null,null),";
         $query.= "\n(22, 'Review security architecture plan with consultant',null,'DAAAAAAA22','1',";
         $query.= "\n1, date_sub(now(), interval 60 day),";
         $query.= "\n5, null, '1', '1', date_sub(now(), interval 45 day), date_sub(now(), interval 40 day),null),";
         $query.= "\n(23, 'Evaluate vendor load balancing proposals against capital budget',null,'DAAAAAAA23','1',";
         $query.= "\n7, date_sub(now(), interval 50 day),";
         $query.= "\n5, null, '1', '1', date_sub(now(), interval 45 day), date_sub(now(), interval 43 day),null),";
         $query.= "\n(24, 'Some preferred domain names are unavailable in registry',null,'DAAAAAAA24','1',";
         $query.= "\n7, date_sub(now(), interval 55 day),";
         $query.= "\n5, null, '1', '3', date_sub(now(), interval 45 day), date_sub(now(), interval 50 day),null),";
         $query.= "\n(25, 'Establish grid management capacity-expansion policies with ASP',null,'DAAAAAAA25','1',";
         $query.= "\n7, date_sub(now(), interval 20 day),";
         $query.= "\n5, null, '4', '3', date_sub(now(), interval 5 day), null,null),";
         $query.= "\n(26, 'Access through proxy servers blocks some usage tracking tools',null,'DAAAAAAA26','1',";
         $query.= "\n7, date_sub(now(), interval 10 day),";
         $query.= "\n5, null, '1', '1', date_sub(now(), interval 5 day), date_sub(now(), interval 1 day),null),";
         $query.= "\n(27, 'Phase I stress testing cannot use production network',null,'DAAAAAAA27','1',";
         $query.= "\n7, date_sub(now(), interval 11 day),";
         $query.= "\n5, null, '4', '1', sysdate(), null,null),";
         $query.= "\n(28, 'DoD clients must have secure port and must be blocked from others',null,'DAAAAAAA28','1',";
         $query.= "\n7, date_sub(now(), interval 20 day),";
         $query.= "\n5, null, '3', '1', sysdate(), null,null);";
         $query.= "\ncommit;";
         $query.= "\n UPDATE `#__it_issues` SET access = 1 WHERE id < 29;";
         $query.= "\ncommit;";

         $query.="\nINSERT INTO `#__it_progress`";
         $query.= "\n(id, lineno, issue_id, alias, public, state, access, progress, created_by, created_on)";
         $query.= "\nvalues";
         $query.= "\n(1, 1, 1,  'DAAAAAAAA1', 0, 0, 2, 'Making steady progress.','admin',NOW()),";;
         $query.= "\n(2,  1, 7,  'DAAAAAAAA7', 0, 0, 2, 'currently beta testing new look and feel','admin',NOW()),";
         $query.= "\n(3, 1, 18, 'DAAAAAAA18', 0, 0, 2, 'pending info from supplier','admin',NOW()),";
         $query.= "\n(4, 1, 28, 'DAAAAAAA28', 0, 0, 2, 'Waiting on Security Consultant, this may drag on.','admin',NOW());";
         $query.= "\ncommit;";
         $query.= "\nend;";
         $db->setQuery($query);
         $db->execute();


         $query= "DROP PROCEDURE IF EXISTS `#__add_it_sample_data`;";
         $db->setQuery($query);
         $db->execute();

         $query= "create procedure `#__add_it_sample_data`()";
         $query.= "\nBEGIN";
         $query.= "\n   CALL `#__create_sample_projects`();";
         $query.= "\n   CALL `#__create_sample_people`();";
         $query.= "\n   CALL `#__create_sample_issues`();";
         $query.= "\nend;";
         $db->setQuery($query);
         $db->execute();

         $query= "DROP PROCEDURE IF EXISTS `#__remove_it_sample_data`;";
         $db->setQuery($query);
         $db->execute();

         $query= "create procedure `#__remove_it_sample_data`()";
         $query.= "\nBEGIN";
         $query.= "\n   delete from `#__it_progress` where id < 11;";
         $query.= "\n   delete from `#__it_issues` where id < 29;";
         $query.= "\n   delete from `#__it_people` where id >1 AND id < 19;";
         $query.= "\n   delete from `#__it_projects` where id > 2 AND id < 8;";
         $query.= "\n   commit;";
         $query.= "\nend;";
         $db->setQuery($query);
         $db->execute();
      }
   }

   /*
    * Procedure to create a default person and a default project both with an id of zero.
    * Also synchronise with the Joomla users table.
    */

   function checkDefEntries()
   {
      $user = JFactory::getUser();

      $db   = JFactory::getDbo();

      // Check to see if the Root node exists
      $query   = "SELECT id from `#__it_projects` WHERE title ='Root'";
      $db->setQuery($query);
      $r_id    = $db->loadResult();

      if ( empty ($r_id) ) {
         // Check if we have id of 1 in use. If we do move it.
         $db->setQuery("SELECT title from `#__it_projects` WHERE id = 1");
         $id_title = $db->loadResult();

         if ( ! empty($id_title) ) {
            // id 10 should be free if not use 9.
            $db->setQuery("SELECT title from `#__it_projects` WHERE id = 10");
            $check_id_title = $db->loadResult();

            if ( empty($check_id_title) ) {
               $n_id = 10;
            } else {
               $n_id = 9;
            }

            // Move Id of 1 to id of 10.
            $db->setQuery("SET foreign_key_checks = 0");
            $db->execute();

            $db->setQuery("UPDATE `#__it_projects` set id = ".$n_id." where id = 1");
            $db->execute();

            $db->setQuery("UPDATE `#__it_projects` set parent_id = ".$n_id." where parent_id = 1");
            $db->execute();

            $db->setQuery("UPDATE `#__it_issues` set related_project_id = ".$n_id." where related_project_id = 1");
            $db->execute();

            $db->setQuery("UPDATE `#__it_people` set assigned_project = ".$n_id." where assigned_project = 1");
            $db->execute();

            $db->setQuery("SET foreign_key_checks = 1");
            $db->execute();
         }

         $query = "INSERT IGNORE INTO `#__it_projects` (id, title, description, parent_id, lft, rgt, level, alias, start_date, state, created_by, created_on)";
         $query.= "\nvalues (1, 'Root', 'Root', 0, 0, 3, 0, 'root', now(), 1, '".$user->username."', now());";
         $db->setQuery($query);
         $db->execute();

         $db->setQuery("UPDATE `#__it_projects` set parent_id = 1 where parent_id = 0 AND id != 1");
         $db->execute();

          $r_id = 1;   // Set up now we have inserted.
      } elseif ( $r_id == 1 ) {
         // Just update any entries pointing to a 0 parent.
         $db->setQuery("UPDATE `#__it_projects` set parent_id = 1 where parent_id = 0 AND id != 1");
         $db->execute();
      } elseif ( $r_id != 1 ) {
         // Have a root entry so move the root entry to be id no 1.
         $db->setQuery("SET foreign_key_checks = 0");
         $db->execute();

         // Check if id of 1 is currently in use.
         $db->setQuery("SELECT title from `#__it_projects` WHERE id = 1");
         $id_title = $db->loadResult();

         if ( empty($id_title) ) {
            $db->setQuery("UPDATE `#__it_projects` set id = 1 where id = ".$r_id);
            $db->execute();

            $db->setQuery("UPDATE `#__it_projects` set parent_id = 1 where parent_id = ".$r_id);
            $db->execute();

         } else {
            // id 10 should be free if not use 9.
            $db->setQuery("SELECT title from `#__it_projects` WHERE id = 10");
            $check_id_title = $db->loadResult();

            if ( empty($check_id_title) ) {
               $n_id = 10;
            } else {
               $n_id = 9;
            }

            $db->setQuery("UPDATE `#__it_projects` set id = ".$n_id." where id = 1");
            $db->execute();

            $db->setQuery("UPDATE `#__it_projects` set parent_id = ".$n_id." where parent_id = 1");
            $db->execute();

            $db->setQuery("UPDATE `#__it_issues` set related_project_id = ".$n_id." where related_project_id = 1");
            $db->execute();

            $db->setQuery("UPDATE `#__it_people` set assigned_project = ".$n_id." where assigned_project = 1");
            $db->execute();

            $db->setQuery("UPDATE `#__it_projects` set id = 1 where id = ".$r_id);
            $db->execute();

            $db->setQuery("UPDATE `#__it_projects` set parent_id = 1 where parent_id = ".$r_id);
            $db->execute();
          }

          $db->setQuery("SET foreign_key_checks = 1");
          $db->execute();
      }

      // Check to see if the Unspecified Project node exists
      $query = "SELECT id from `#__it_projects` WHERE title ='Unspecified Project' AND description LIKE '%Unspecified Project%'";
      $db->setQuery($query);
      $usp_id = $db->loadResult();

      if ( empty ($usp_id) ) {
         $query = "INSERT IGNORE INTO `#__it_projects` (id, title, description, parent_id, lft, rgt, level, start_date, state, created_by, created_on, access)";
         $query.= "\nvalues (10, 'Unspecified Project', 'Unspecified Project','".$r_id."', 1, 2, 1, now(), 1, '".$user->username."', now(), 1);";
         $db->setQuery($query);
         $db->execute();

         $usp_id = 10;
      }

      // Check to see if the Super user is using the id of 1
      $query = "SELECT id from `#__users` WHERE name ='Super User'";
      $db->setQuery($query);
      $super_id = $db->loadResult();

      if ( $super_id == 1 ) {
         $query=  "INSERT IGNORE INTO `#__it_people` (id, person_name, username, person_email, registered, person_role, created_by, created_on, assigned_project)";
         $query.= "\nvalues (2, 'Anonymous', 'anon', 'anonymous@bademail.com', '0', '6', '".$user->username."', now(), '".$usp_id."');";
         $db->setQuery($query);
         $db->execute();
      } else {
         $query=  "INSERT IGNORE INTO `#__it_people` (id, person_name, username, person_email, registered, person_role, created_by, created_on, assigned_project)";
         $query.= "\nvalues (1, 'Anonymous', 'anon', 'anonymous@bademail.com', '0', '6', '".$user->username."', now(), '".$usp_id."');";
         $db->setQuery($query);
         $db->execute();
      }

      // Check to see if we need to synchronise with users table.
      $query = "SELECT count(*) FROM `#__it_people`";
      $db->setQuery($query);
      $p_id = $db->loadResult();

      if ( $p_id == 1 ) {
         $query = "INSERT IGNORE INTO `#__it_people` (user_id, person_name, username, person_email, registered, person_role, assigned_project, created_by, created_on)";
         $query.= "\n   SELECT id, name, username, email, '1', '6', '".$usp_id."', '".$user->username."', registerDate FROM `#__users`";
         $db->setQuery($query);
         $db->execute();
      }
   }

   /**
    *Routines for rebuilding projects under a Nested table rather a heirarchical.
    *
    *
    * @param $tname
    * @param $colname
    */
   function convertTable($tname, $colname)
   {
      $db = JFactory::getDbo();

      // See if there is anything to do!
      $query = "SELECT count(lft) FROM `".$tname."` WHERE lft > 0 AND id > 10 ";
      $db->setQuery($query);
      $cnt = $db->loadResult();

      if ( $cnt > 0 ) {
         echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_PROJECTS_ALREADY_NEST_TEXT') . '</p>';
         return;
      }

      // Populate title field
      $query = "UPDATE `".$tname."` SET title = ".$colname." ";
      $db->setQuery($query);
      $db->execute();

      echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_POPULATING_PROJECTS_NEST_TEXT') . '</p>';

      // Now Update the levels in the table.
      // Ensure we have a Root entry. If not create one.
      $query = "SELECT id FROM `".$tname."` WHERE ".$colname." = 'Root' ";
      $db->setQuery($query);
      $r_id = $db->loadResult();
      if ( empty($r_id) ) {
         $query = "INSERT into `".$tname."` (lft, rgt, level, description, alias, ".$colname.") VALUES(0,1,0,'Root','root','Root')";
         $db->setQuery($query);
         $db->execute();
      }

      // First set level 1
      $query = "UPDATE `".$tname."` SET level=1 WHERE parent_id = '".$r_id."' AND ".$colname." != 'Root' ";
      $db->setQuery($query);
      $res = $db->execute();

      if ( $res ) {
         $cnt = $db->getAffectedRows($res);
      }

      // Get level 1 results in an array.
      $query = "SELECT id FROM `".$tname."` WHERE level = 1 AND parent_id = '".$r_id."'";
      $db->setQuery($query);
      $Ids = $db->loadResultArray();

      for ($lvl=2; $lvl<=10; $lvl++) {
         if (count($Ids) > 0 ) {
            // Now level
            $query = "UPDATE `".$tname."` SET level=".$lvl." WHERE parent_id IN ('".implode("','",$Ids)."') ";
            $db->setQuery($query);
            $db->execute();

            // Get level results in an array.
            $query = "SELECT id FROM `".$tname."` WHERE level = ".$lvl;
            $db->setQuery($query);
            $Ids = $db->loadResultArray();
            $cnt2 = count($Ids);
            if ( $cnt2 == 0) {
               break;
            }
         }
      }

      // build a complete copy of the table in memory.  Fine for our purposes.
      $query = "SELECT `id`,`parent_id` FROM `".$tname."` WHERE ".$colname." != 'Root' ";
      $db->setQuery($query);
      $a_rows = $db->loadAssocList();

      $a_link = array();
      foreach($a_rows as $a_row) {
         $i_parent_id = $a_row['parent_id'];
         $i_child_id = $a_row['id'];
         if (!array_key_exists($i_parent_id, $a_link)) {
            $a_link[$i_parent_id]=array();
         }
         $a_link[$i_parent_id][]=$i_child_id;
      }

      $o_tree_transformer = new tree_transformer($a_link);
      $o_tree_transformer->traverse($tname, $colname, 0);

      // Finally update the root node.
      $query = "SELECT max(rgt) from `".$tname."`";
      $db->setQuery($query);
      $val = $db->loadResult();
      $val = $val + 1;

      $query = "UPDATE ".$db->quoteName($tname)." SET lft=0, rgt=".$val." WHERE ".$db->quoteName($colname)." = 'Root' ";
      $db->setQuery($query);
      $db->execute();
   }

   /*
    Routine to copy over hathor template overrides for Joomla 3.x
   */
   private function cp_hathor_overrides()
   {
      // Required for Joomla 3.0
      if(!defined('DS')){
         define('DS',DIRECTORY_SEPARATOR);
      }

      $tmplSrc = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'hathor'.DS.'com_issuetracker';
      $tmplDst = JPATH_ADMINISTRATOR.DS.'templates'.DS.'hathor';
      if(! JFolder::exists($tmplDst)){
         // Directory does not exist assume template not installed.
         return;
      }

      $tmplDst .= DS.'html'.DS.'com_issuetracker';
      if(! JFolder::exists($tmplDst)){
         mkdir($tmplDst);
      }

      // For loop for all directories/
      foreach ($this->hathorDirs as &$dir) {
         $dDst = $tmplDst.DS.$dir;
         $sSrc = $tmplSrc.DS.$dir;

         if ( JFolder::exists($sSrc) ) {
            if(! JFolder::exists($dDst)){
               // Directory does not exist - create it.
               mkdir($dDst);
            }

            $sSrc .= DS;
            $dDst .= DS;
            if ( JFile::exists($sSrc.'default.php') )                { JFile::copy($sSrc.'default.php', $dDst.'default.php'); }
            if ( JFile::exists($sSrc.'default.xml') )                { JFile::copy($sSrc.'default.xml', $dDst.'default.xml'); }
            if ( JFile::exists($sSrc.'coloriser.php') )              { JFile::copy($sSrc.'coloriser.php', $dDst.'coloriser.php'); }
            if ( JFile::exists($sSrc.'edit.php') )                   { JFile::copy($sSrc.'edit.php', $dDst.'edit.php'); }
            if ( JFile::exists($sSrc.'edit_attachment.php') )        { JFile::copy($sSrc.'edit_attachment.php', $dDst.'edit_attachment.php'); }
            if ( JFile::exists($sSrc.'edit_attachments.php') )       { JFile::copy($sSrc.'edit_attachments.php', $dDst.'edit_attachments.php'); }
            if ( JFile::exists($sSrc.'edit_audit_details.php') )     { JFile::copy($sSrc.'edit_audit_details.php', $dDst.'default_audit_details.php'); }
            if ( JFile::exists($sSrc.'edit_custom.php') )            { JFile::copy($sSrc.'edit_custom.php', $dDst.'edit_custom.php'); }
            if ( JFile::exists($sSrc.'edit_progress.php') )          { JFile::copy($sSrc.'edit_progress.php', $dDst.'edit_progress.php'); }
            if ( JFile::exists($sSrc.'default_audit_details.php') )  { JFile::copy($sSrc.'default_audit_details.php', $dDst.'default_audit_details.php'); }
            if ( JFile::exists($sSrc.'default_credits.php') )        { JFile::copy($sSrc.'default_credits.php', $dDst.'default_credits.php'); }
            if ( JFile::exists($sSrc.'index.html') )                 { JFile::copy($sSrc.'index.html', $dDst.'index.html'); }
         }
      }
      // Now remove template source directory used for the copy.

      echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_HATHOR_OVERRIDES_CREATED_TEXT') . '</p>';
   }

   /*
    Routine to remove hathor template overrides for Joomla 3.x
   */
   private function rm_hathor_overrides()
   {
      // Required for Joomla 3.0
      if(!defined('DS')){
         define('DS',DIRECTORY_SEPARATOR);
      }

      // Now remove files
      $tmplDst = JPATH_ADMINISTRATOR.DS.'templates'.DS.'hathor';
      if(! JFolder::exists($tmplDst)){
         // Directory does not exist assume template not installed.
         return;
      }

      $nDst = $tmplDst.DS.'html'.DS.'com_issuetracker';
      if (! JFolder::exists($nDst)){
         // Directory does not exist assume template overrides not installed.
         return;
      }

      // For loop for all directories/
      foreach ($this->hathorDirs as &$dir) {
         $tDst = $nDst.DS.$dir;
         if ( JFolder::exists($tDst) ) {
            if ( JFile::exists($tDst.DS.'default.php') )                { JFile::delete($tDst.DS.'default.php'); }
            if ( JFile::exists($tDst.DS.'default.xml') )                { JFile::delete($tDst.DS.'default.xml'); }
            if ( JFile::exists($tDst.DS.'coloriser.php') )              { JFile::delete($tDst.DS.'coloriser.php'); }
            if ( JFile::exists($tDst.DS.'edit.php') )                   { JFile::delete($tDst.DS.'edit.php'); }
            if ( JFile::exists($tDst.DS.'edit_attachment.php') )        { JFile::delete($tDst.DS.'edit_attachment.php'); }
            if ( JFile::exists($tDst.DS.'edit_attachments.php') )       { JFile::delete($tDst.DS.'edit_attachments.php'); }
            if ( JFile::exists($tDst.DS.'edit_audit_details.php') )     { JFile::delete($tDst.DS.'edit_audit_details.php'); }
            if ( JFile::exists($tDst.DS.'edit_custom.php') )            { JFile::delete($tDst.DS.'edit_custom.php'); }
            if ( JFile::exists($tDst.DS.'edit_progress.php') )          { JFile::delete($tDst.DS.'edit_progress.php'); }
            if ( JFile::exists($tDst.DS.'default_audit_details.php') )  { JFile::delete($tDst.DS.'default_audit_details.php'); }
            if ( JFile::exists($tDst.DS.'default_credits.php') )        { JFile::delete($tDst.DS.'default_credits.php'); }
            if ( JFile::exists($tDst.DS.'index.html') )                 { JFile::delete($tDst.DS.'index.html'); }
            rmdir($tDst);
         }
      }
      // Now remove parent directory;
      rmdir ($nDst);

      echo '<p style="color: #5F9E30;">' . JText::_('COM_ISSUETRACKER_HATHOR_OVERRIDES_REMOVED_TEXT') . '</p>';
   }

   /*
    Routine to remove hathor template overrides for Joomla 3.x
   */
   private function rm_hathor_src()
   {
      // Required for Joomla 3.0
      if(!defined('DS')){
         define('DS',DIRECTORY_SEPARATOR);
      }

      // Now remove files
      $tmplDst = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'hathor';
      if(! JFolder::exists($tmplDst)){
         // Directory does not exist assume template not installed.
         return;
      }

      $nDst = $tmplDst.DS.'com_issuetracker';
      if (! JFolder::exists($nDst)){
         // Directory does not exist assume template overrides not installed.
         return;
      }

      // For loop for all directories/
      foreach ($this->hathorDirs as &$dir) {
         $tDst = $nDst.DS.$dir;
         if ( JFolder::exists($tDst) ) {
            if ( JFile::exists($tDst.DS.'default.php') )                { JFile::delete($tDst.DS.'default.php'); }
            if ( JFile::exists($tDst.DS.'default.xml') )                { JFile::delete($tDst.DS.'default.xml'); }
            if ( JFile::exists($tDst.DS.'coloriser.php') )              { JFile::delete($tDst.DS.'coloriser.php'); }
            if ( JFile::exists($tDst.DS.'edit.php') )                   { JFile::delete($tDst.DS.'edit.php'); }
            if ( JFile::exists($tDst.DS.'edit_attachment.php') )        { JFile::delete($tDst.DS.'edit_attachment.php'); }
            if ( JFile::exists($tDst.DS.'edit_attachments.php') )       { JFile::delete($tDst.DS.'edit_attachments.php'); }
            if ( JFile::exists($tDst.DS.'edit_audit_details.php') )     { JFile::delete($tDst.DS.'edit_audit_details.php'); }
            if ( JFile::exists($tDst.DS.'edit_custom.php') )            { JFile::delete($tDst.DS.'edit_custom.php'); }
            if ( JFile::exists($tDst.DS.'edit_progress.php') )          { JFile::delete($tDst.DS.'edit_progress.php'); }
            if ( JFile::exists($tDst.DS.'default_audit_details.php') )  { JFile::delete($tDst.DS.'default_audit_details.php'); }
            if ( JFile::exists($tDst.DS.'default_credits.php') )        { JFile::delete($tDst.DS.'default_credits.php'); }
            if ( JFile::exists($tDst.DS.'index.html') )                 { JFile::delete($tDst.DS.'index.html'); }
            // $this->deleteDirectory ($tDst);
            rmdir($tDst);
         }
      }

      if ( JFile::exists($nDst.DS.'index.html') )                 { JFile::delete($nDst.DS.'index.html'); }
      // Now remove parent directories;
      rmdir ($nDst);
      // $this->deleteDirectory ($nDst);

      if ( JFile::exists($tmplDst.DS.'index.html') )                 { JFile::delete($tmplDst.DS.'index.html'); }
      rmdir ( $tmplDst );
      // $this->deleteDirectory ($tmplDst);
   }

   /**
    * method to delete a non-empty directory.
    * @param $dir
    * @return bool
    */
    function deleteDirectory($dir) {
       if (!file_exists($dir))   return true;
       if (!is_dir($dir))        return unlink($dir);
       foreach (scandir($dir) as $item) {
           if ($item == '.' || $item == '..') continue;
           if (!self::deleteDirectory($dir.DIRECTORY_SEPARATOR.$item)) return false;
       }
       return rmdir($dir);
   }

   /**
    * method to check database privileges
    * @param $priv
    * @return bool|mixed
    */
    function check_db_priv($priv)
    {
      $db = JFactory::getDbo();

      $config = JFactory::getConfig();
      // $dbname = $config->get('db');
      $dbuser = $config->get('user');
      // $host    = $config->get('host');   // Ignore host for now.
      $cstring = "'" . $dbuser . "'@'";
      // GRANTEE is stored a single quotes around the dbuser and host and the @ sign in between.

      $query = " SELECT MAX(CNT) FROM ( ";
      $query.= "SELECT COUNT(DISTINCT PRIVILEGE_TYPE) AS CNT FROM INFORMATION_SCHEMA.USER_PRIVILEGES";
      $query.= " WHERE PRIVILEGE_TYPE ='".$priv."'";
      $query.= ' AND GRANTEE LIKE "'.$cstring.'%"';
      $query.= " UNION ALL ";
      $query.= "SELECT COUNT(DISTINCT PRIVILEGE_TYPE) AS CNT FROM INFORMATION_SCHEMA.SCHEMA_PRIVILEGES";
      $query.= " WHERE PRIVILEGE_TYPE ='".$priv."'";
      $query.= ' AND GRANTEE LIKE "'.$cstring.'%"';
      $query.= ") AS A ";
      $db->setQuery($query);
      $res = $db->loadResult();

      if ( $priv == 'CREATE ROUTINE') {
         // Check for presence of ALTER as well at the same time
         // for the situations where we can create but not remove the object.
         // May be rare and would probably indicates a bad database configuration but
         // we should check anyway.
         // Safe to assume we will be feed the priv as CREATE XXXXXX
         $query2 = str_replace('CREATE', 'ALTER', $query);
         $db->setQuery($query2);
         $ress = $db->loadResult();
         $result = $res && $ress;
      } else {
         $result = $res;
      }

      // Extra checks for log_bin setting turned on, SUPER privilege and log_bin_trust_function_creators setting.
      if ( $priv != 'CREATE VIEW' ) {
         // Only applies to TRIGGER and ROUTINE get log_bin setting.
         $query = "SELECT variable_value from INFORMATION_SCHEMA.GLOBAL_VARIABLES WHERE VARIABLE_NAME ='LOG_BIN'";
         $db->setQuery($query);
         $res = $db->loadResult();

         // If binary logging off we do not need any further checks.
         if ( $res != 'OFF' ) {
            // Check if trust setting used.
            $query = "SELECT variable_value from INFORMATION_SCHEMA.GLOBAL_VARIABLES WHERE VARIABLE_NAME = 'LOG_BIN_TRUST_FUNCTION_CREATORS'";
            $db->setQuery($query);
            $trust = $db->loadResult();

            // Check if SUPER privilege granted.
            $query = " SELECT MAX(CNT) FROM ( ";
            $query.= "SELECT COUNT(DISTINCT PRIVILEGE_TYPE) AS CNT FROM INFORMATION_SCHEMA.USER_PRIVILEGES";
            $query.= " WHERE PRIVILEGE_TYPE ='SUPER'";
            $query.= ' AND GRANTEE LIKE "'.$cstring.'%"';
            $query.= " UNION ALL ";
            $query.= "SELECT COUNT(DISTINCT PRIVILEGE_TYPE) AS CNT FROM INFORMATION_SCHEMA.SCHEMA_PRIVILEGES";
            $query.= " WHERE PRIVILEGE_TYPE ='SUPER'";
            $query.= ' AND GRANTEE LIKE "'.$cstring.'%"';
            $query.= ") AS A ";
            $db->setQuery($query);
            $res = $db->loadResult();

            if ( $res || $trust != 'OFF') {
              $result = true;
            } else {
               $result = false;
            }
         }
      }

      return $result;
   }
   /**
    * Copies the CLI scripts into Joomla!'s cli directory
    *
    * @param JInstaller $parent
    */
   private function _copyCliFiles($parent)
   {
      // $src = $parent->getParent()->getPath('source');  // Do not need to call getParent since it was done in the calling routine.
      $src = $parent->getPath('source');

      if(empty($this->compCliScripts)) {
         print("No CLI files present<p>");
         return;
      }

      foreach($this->compCliScripts as $script) {
         if(JFile::exists(JPATH_ROOT.'/cli/'.$script)) {
            JFile::delete(JPATH_ROOT.'/cli/'.$script);
         }
         if(JFile::exists($src.'/cli/'.$script)) {
            JFile::move($src.'/cli/'.$script, JPATH_ROOT.'/cli/'.$script);
         }
      }
   }

   /**
    * Remove CLI scripts from Joomla!'s cli directory
    *
    * @param JInstaller $parent
    */
   private function _remCliFiles($parent)
   {
      if(empty($this->compCliScripts)) {
         return;
      }

      foreach($this->compCliScripts as $script) {
         if(JFile::exists(JPATH_ROOT.'/cli/'.$script)) {
            JFile::delete(JPATH_ROOT.'/cli/'.$script);
         }
      }
   }
}

/**
 * Class tree_transformer
 */
class tree_transformer
{
   private $countr;
   private $a_link;

   /**
    * @param $a_link
    * @throws Exception
    */
   public function __construct($a_link)
   {
      if(!is_array($a_link)) throw new Exception ("Parameter should be an array. Instead, it was type '".gettype($a_link)."'");
      $this->countr = 0;
      $this->a_link= $a_link;
   }

   /**
    * @param $tname
    * @param $colname
    * @param $id
    */
   public function traverse($tname, $colname, $id)
   {
      $lft = $this->countr;
      $this->countr++;

      $children = $this->get_children($id);
      if ($children) {
         foreach($children as $a_child) {
            $this->traverse($tname, $colname, $a_child);
         }
      }
      $rgt=$this->countr;
      $this->countr++;
      $this->update($tname, $colname, $lft, $rgt, $id);
   }

   /**
    * @param $id
    * @return array
    */
   private function get_children($id)
   {
      if (array_key_exists($id, $this->a_link)) {
         return $this->a_link[$id];
      } else {
         return false;
      }
   }

   /**
    * @param $tname
    * @param $colname
    * @param $lft
    * @param $rgt
    * @param $id
    * @throws Exception
    */
   private function update($tname, $colname, $lft, $rgt, $id)
   {
      $db = JFactory::getDbo();

      // Now fetch the remaining data
      $query = "SELECT * FROM `".$tname."` WHERE `id`  = '".$id."'";
      $db->setQuery($query);

      $a_source = $db->loadAssocArray();

      // root node?  label it unless already labeled in source table
      if ( $lft == 0 && empty($a_source['$colname']) ) {
         $a_source['$colname'] = 'Root';
      }

      // insert into the new nested tree table
      if ( $id != 0 ) {
         $query = "UPDATE `".$tname."` SET lft = '".$lft."', rgt = '".$rgt."' WHERE id = '".$id."'";
         // print("Update query $query<p>");
         $db->setQuery($query);
         $i_result = $db->execute();

         if (!$i_result) {
            echo "<pre>Error: $query</pre>\n";
            throw new Exception($db->getErrorMsg());
         }
      }
   }
}
