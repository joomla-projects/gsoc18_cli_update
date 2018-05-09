<?php
/*
 *
 * @Version       $Id: attachment.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.4
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// No direct access.
defined('_JEXEC') or die( 'Restricted access' );

JLoader::import('joomla.application.component.modeladmin');
JLoader::import('joomla.filesystem.folder');
JLoader::import('joomla.filesystem.file');
JLoader::import('joomla.utilities.date');

/**
 * Issuetracker Attachment model.
 */
class IssueTrackerModelAttachment extends JModelAdmin
{
   /**
    * @var     string   The prefix to use with controller messages.
    * @since   1.6
    */
   protected $text_prefix = 'COM_ISSUETRACKER';


    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param string $type
     * @param string $prefix A prefix for the table class name. Optional.
     * @param array $config Configuration array for model. Optional.
     * @internal param $type The table type to instantiate
     * @return  JTable   A database object
     * @since   1.6
     */
   public function getTable($type = 'Attachment', $prefix = 'IssueTrackerTable', $config = array())
   {
      return JTable::getInstance($type, $prefix, $config);
   }

   /**
    * Method to get the record form.
    *
    * @param   array $data    An optional array of data for the form to interogate.
    * @param   boolean  $loadData   True if the form is to load its own data (default case), false if not.
    * @return  JForm A JForm object on success, false on failure
    * @since   1.6
    */
   public function getForm($data = array(), $loadData = true)
   {
      // Initialise variables.
      // $app  = JFactory::getApplication();

      // Get the form.
      $form = $this->loadForm('com_issuetracker.attachment', 'attachment', array('control' => 'jform', 'load_data' => $loadData));
      if (empty($form)) {
         return false;
      }

      return $form;
   }

   /**
    * Method to get the data that should be injected in the form.
    *
    * @return  mixed The data for the form.
    * @since   1.6
    */
   protected function loadFormData()
   {
      // Check the session for previously entered form data.
      $data = JFactory::getApplication()->getUserState('com_issuetracker.edit.attachment.data', array());

      if (empty($data)) {
         $data = $this->getItem();
      }

      return $data;
   }

   /**
    * Method to get a single record.
    *
    * @param   integer  $pk The id of the primary key.
    *
    * @return  mixed Object on success, false on failure.
    * @since   1.6
    */
   public function getItem($pk = null)
   {
      if ($item = parent::getItem($pk)) {

         //Do any procesing on fields here if needed

      }

      return $item;
   }

   /**
    * Prepare and sanitise the table prior to saving.
    *
    * @since   1.6
    * @param JTable $table
    */
   protected function prepareTable($table)
   {
      JLoader::import('joomla.filter.output');

      if (empty($table->id)) {

         // Set ordering to the last item if not set
         if (@$table->ordering === '') {
            $db = JFactory::getDbo();
            $db->setQuery('SELECT MAX(ordering) FROM #__it_attachment');
            $max = $db->loadResult();
            $table->ordering = $max+1;
         }

      }
   }

   /**
    * Method to store a record.
    *
    * $ftitle is only used from the front end. The back end has already saved the appropriate
    * title information in the data array already.
    *
    * @access  public
    * @param array $data
    * @param null $ftitles
    * @return  boolean  True on success
    */
   public function save($data, $ftitles = null)
   {
      // $app = JFactory::getApplication();
      // $user = JFactory::getUser();
      // $this->_params = JComponentHelper::getParams( 'com_issuetracker' );

      $files = JFactory::getApplication()->input->files->get('attachedfile', '', 'files', 'array');
      $cntr    = 0;
      $fcntr   = 0;

      if ( $this->isMultiArray($files) ) {
         foreach ($files as $file) {
            if ( !empty($ftitles) ) {
               $ftitle = $ftitles[$fcntr];
            } else {
               $ftitle = null;
            }

            if ($this->savefile($file, $data, $ftitle))
                $cntr++;
            $fcntr++;            // Increment our file titles array.
         }
      } else {
         if ( !empty($ftitles) ) {
            $ftitle = $ftitles[0];
         } else {
            $ftitle = null;
         }
         if ( $this->savefile($files, $data, $ftitle))
            $cntr++;;
      }
      return $cntr;   // true;
   }

   /**
    * Method to check whether we have a multi dimensional array.
    * Used to minimise save routine code so it works on a simple array and on an
    * array of arrays from the front end save call.
    * @param $a
    * @return bool
    */
   function isMultiArray($a){
      foreach($a as $v) if(is_array($v)) return TRUE;
      return FALSE;
   }

   /**
    * Method to store a file record.
    * This is current a special for the front end. The main difference between
    * this and the default save routine is the handling of the array elements.
    * The back end will only have one file where as the frot end will have an array of file names.
    *
    * @access  public
    * @param $file
    * @param array $data
    * @param null $ftitle
    * @return  boolean  True on success
    */
   public function savefile($file, $data, $ftitle = null)
   {
      $app = JFactory::getApplication();
      // Get user details
      $user = JFactory::getUser();

      $emptyFile = true;
      if ( !empty($file) ) {
         if ( !empty($file['name'])) {
            $emptyFile = false;
         }
      }

      if ( !$emptyFile ) {
         $filedef = $this->uploadFile($file);
         // $result = false;
         if ( $filedef !== false ) {
            $jdate = new JDate();

            $data['filename']    = $filedef['filename'];
            $data['filepath']    = $filedef['filepath'];
            $data['filetype']    = $filedef['filetype'];
            $data['hashname']    = $filedef['hashname'];
            $data['created_by']  = $user->name;
            $data['created_on']  = $jdate->toSql();
            $data['uid']         = $user->id;
            $data['state']       = 1;
            $data['size']        = $file['size'];
            if ( !empty($ftitle) )
               $data['title']    = $ftitle;
         } else {
            $app->enqueueMessage( $this->getError(), 'error' );
            return false;
         }
      } else {
         if ( empty ( $data['filename'] ) ) {
            // Filename not specified.
            $this->setError(JText::_('COM_ISSUETRACKER_ERROR_NOFILESPECIFIED'));
         }
         return false;
      }

      JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'tables');
      $row = $this->getTable('attachment','IssueTrackerTable');

      // Bind the form fields to the table
      if (!$row->bind($data)) {
         $this->setError($row->getError());
         return false;
      }

      // Make sure the record is valid
      if (!$row->check()) {
         $this->setError($row->getError());
         return false;
      }

      // Store record in the database
      if (!$row->store()) {
         $this->setError( $row->getError() );
         return false;
      }

      $this->setState($this->getName() . '.id', $row->id);

      if ( array_key_exists('id', $data) ) {
         // Ensure it is checked in.
         $pk = $data['id'];
         $this->checkin($pk);
      }

      return true;
   }


   /**
    * Returns the currently set ID
    * @return int
    */
   public function getId()
   {
      $id   = JFactory::getApplication()->input->getInt('id', 0);
      return $id;
   }


   /**
    * Moves an uploaded file to the media://com_issuetracker/attachments directory
    * under a random name and returns a full file definition array, or false if
    * the upload failed for any reason.
    *
    * @param array $file The file descriptor returned by PHP
    * @return array|bool
    */
   public function uploadFile($file)
   {
      if (isset($file['name'])) {
         // Can we upload this file type?
         if ( !class_exists('MediaHelper') ) {
            require_once(JPATH_ADMINISTRATOR.'/components/com_media/helpers/media.php');
         }

         $paths = array(JPATH_ROOT, JPATH_ADMINISTRATOR);
         $jlang = JFactory::getLanguage();
         $jlang->load('com_media', $paths[0], 'en-GB', true);
         $jlang->load('com_media', $paths[0], null, true);
         $jlang->load('com_media', $paths[1], 'en-GB', true);
         $jlang->load('com_media', $paths[1], null, true);

         if ( !$this->canUpload($file, $err)) {
            $err = JText::_($err);
            $this->setError(JText::_('COM_ISSUETRACKER_ATTACHMENTS_ERR_MEDIAHELPERERROR').' '.$err);
            return false;
         }

         // Get a randomised name
         if(version_compare(JVERSION, '3.0', 'ge')) {
            $serverkey = JFactory::getConfig()->get('secret','');
         } else {
            $serverkey = JFactory::getConfig()->getValue('secret','');
         }

         $sig = $file['name'].microtime().$serverkey;
         if(function_exists('sha256')) {
            $mangledname = sha256($sig);
         } elseif(function_exists('sha1')) {
            $mangledname = sha1($sig);
         } else {
            $mangledname = md5($sig);
         }

         // Set up access to default parameters
         $this->_params = JComponentHelper::getParams( 'com_issuetracker' );

         // Get default settings
         $def_path = $this->_params->get('attachment_path', 'media/com_issuetracker/attachments');
         if ( substr($def_path, 0, 1) != '/')   $def_path = '/'.$def_path;
         if ( substr($def_path, -1) != '/')     $def_path = $def_path . '/';

         // ...and its full path
         $filepath = JPath::clean(JPATH_ROOT . $def_path . $mangledname);

         // Check directory exists and has a valid index.html file.
         if ( !$this->checkuploadDir ($def_path) ) {
            // Error message set in method.
            return false;
         }

         // If we have a name clash, abort the upload
         if (JFile::exists($filepath)) {
            $this->setError(JText::_('COM_ISSUETRACKER_ATTACHMENTS_ERR_NAMECLASH'));
            return false;
         }

         // Do the upload
         if (!JFile::upload($file['tmp_name'], $filepath)) {
            $this->setError(JText::_('COM_ISSUETRACKER_ATTACHMENTS_ERR_CANTJFILEUPLOAD'));
            return false;
         }

         // Get the MIME type
         if(function_exists('mime_content_type')) {
            $mime = mime_content_type($filepath);
         } elseif(function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $filepath);
         } else {
            $mime = 'application/octet-stream';
         }

         // Return the file info
         return array(
            'filename'  => $file['name'],
            'hashname'  => $mangledname,
            'filetype'  => $mime,
            'filepath'  => $filepath
         );
      } else {
         $this->setError(JText::_('COM_ISSUETRACKER_ATTACHMENTS_ERR_NOFILE'));
         return false;
      }
   }

   /**
    * @param $file
    * @param $err
    * @return bool
    */
   function canUpload($file, &$err)
   {
      $params = JComponentHelper::getParams('com_issuetracker');

      $format = JFile::getExt($file['name']);

      $allowable = explode(',', $params->get('upload_extensions'));

      if (!in_array($format, $allowable)) {
         $err = JText::_('COM_ISSUETRACKER_ERROR_WARNFILETYPE');
         return false;
      }

      $maxSize = (int) ($params->get('max_file_size', 0) * 1024 * 1024);

      if ($maxSize > 0 && (int) $file['size'] > $maxSize) {
         $err = JText::_('COM_ISSUETRACKER_ERROR_WARNFILETOOLARGE');
         return false;
      }

      if ( (int) $file['size'] == 0 ) {
         $err = JText::_('COM_ISSUETRACKER_ERROR_WARNFILEISZERO');
         return false;
      }

      return true;
   }

   /**
    * @param null $id
    */
   function DownloadFile($id = null)
   {
      $item = $this->getItem($id);

      // Calculate the Etag
      $etagContent = $item->hashname.$item->filetype.$item->filename.$item->created_on.$item->created_by;
      if(function_exists('sha1')) {
         $eTag = sha1($etagContent);
      } else {
         $eTag = md5($etagContent);
      }

      // Do we have an If-None-Match header?
      $inm = '';
      if(function_exists('apache_request_headers')) {
         $headers = apache_request_headers();
         if(array_key_exists('If-None-Match', $headers)) $inm = $headers['If-None-Match'];
      }
      if(empty($inm)) {
         if(array_key_exists('HTTP-IF-NONE-MATCH', $_SERVER)) $inm = $_SERVER['HTTP-IF-NONE-MATCH'];
      }
      if($inm == $eTag) {
         while (@ob_end_clean());
         header('HTTP/1.0 304 Not Modified');
         jexit();
      }

      $filepath = JPath::clean(JPATH_ROOT.'/media/com_issuetracker/attachments/'.$item->hashname);
      $basename = $item->filename;

      if(!JFile::exists($filepath)) {
         header('HTTP/1.0 404 Not Found');
         JError::raiseError(404, "File not found on file system.");
         // jexit();
      }

      JFactory::getApplication()->input->set('format', 'raw');

      // Disable error reporting and error display
      if(function_exists('error_reporting')) {
         $oldErrorReporting = error_reporting(0);
      }
      if(function_exists('ini_set')) {
         @ini_set('display_error', 0);
      }

      // Clear cache
      while (@ob_end_clean());

      // Fix IE bugs
      if (isset($_SERVER['HTTP_USER_AGENT']) && strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
         $header_file = preg_replace('/\./', '%2e', $basename, substr_count($basename, '.') - 1);

         if (ini_get('zlib.output_compression'))  {
            ini_set('zlib.output_compression', 'Off');
         }
      }
      else {
         $header_file = $basename;
      }

      @clearstatcache();

      // Disable caching for regular attachment disposition
      if($this->getState('disposition','attachment') !== 'attachment') {
         header("Pragma: public");
         header("Expires: 0");
         header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
         header("Cache-Control: public", false);
      }

      // Send a Date header
      $jDate = new JDate($item->created_on);
      header('Date: '.$jDate->toRFC822());

      // Send an Etag
      header('Etag: '.$eTag);

      // Send MIME headers
      header("Content-Description: File Transfer");
      if(empty($item->mime_type)) {
         header('Content-Type: application/octet-stream');
      } else {
         header('Content-Type: '.$item->mime_type);
      }
      header("Accept-Ranges: bytes");
      if($this->getState('disposition','attachment') != 'attachment') {
         header('Content-Disposition: inline; filename="'.$header_file.'"');
      } else {
         header('Content-Disposition: attachment; filename="'.$header_file.'"');
      }
      header('Content-Transfer-Encoding: binary');

      // Notify of filesize, if this info is available
      $filesize = @filesize($filepath);
      if($filesize > 0) header('Content-Length: '.(int)$filesize);

      // Disable time limits
      if ( ! ini_get('safe_mode') ) {
         set_time_limit(0);
      }

      // Use 1M chunks for echoing the data to the browser
      @flush();
      $chunksize = 1024*1024; //1M chunks
      $buffer = '';
      /**/
      $handle = @fopen($filepath, 'rb');
      if($handle !== false)
      {
         while (!feof($handle)) {
            $buffer = fread($handle, $chunksize);
            echo $buffer;
            @ob_flush();
            flush();
         }
         @fclose($handle);
      }
      else
      {
         @readfile($filepath);
         @flush();
      }
      /**/
      // Ungraceful application exit -- so that any plugins won't screw up the
      // download...
      jexit(0);
   }

   /**
    * @param $file
    */
   public function removeFile($file)
   {
      // Remove file from filesystem
      if ( JFile::exists($file) ) JFile::delete($file);
   }

   /*
    * Public method to check if the specified directory exists.
    * If it does check if an index.html file exists.
    * If not create one.
    *
    * If directory does not exist create it and the index.html file.
    */
   /**
    * @param $dir
    * @return bool
    */
   public function checkuploadDir( $dir )
   {
      $ndir = JPATH_ROOT.DS.$dir;
      if (!JFolder::exists( $ndir )) {
         if (JFolder::create( $ndir, 0755 )) {
            $data = "<!DOCTYPE html><title></title>";
            if (!JFile::write($ndir.DS."index.html", $data) ) {
               $this->setError(JText::_('COM_ISSUETRACKER_ATTACHMENTS_CANNOT_CREATE_INDEXFILE'));
               return false;
            }
         } else {
            $this->setError(JText::_('COM_ISSUETRACKER_ATTACHMENTS_CANNOT_CREATE_DIRECTORY'));
            return false;
         }
      } else {
         if ( !JFile::exists ( $ndir.DS."index.html")) {
            $data = "<!DOCTYPE html><title></title>";
            if (!JFile::write($ndir.DS."index.html", $data)) {
               $this->setError(JText::_('COM_ISSUETRACKER_ATTACHMENTS_CANNOT_CREATE_INDEXFILE'));
               return false;
            }
         }
      }
      return true;
   }

   /**
    * @return null|string
    */
   public function getReturnPage()
   {
      $app  = JFactory::getApplication();
      $val  = $app->input->get('return', null, 'base64');
      $return = base64_decode($val);

      if (!empty($return) ) {
         return $return;
      } else {
         return null;
      }
   }
}