<?php
/*
 *
 * @Version       $Id: default25.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.0
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

$db   = JFactory::getDBO();
$sql  = "SELECT version FROM ".$db->quoteName('#__it_meta')." WHERE type='component'";
$db->setQuery( $sql);
$version = $db->loadResult();

?>
<table class="adminlist">
  <tr>
    <td>
      <div class="docs">
         <h1>Issue Tracker</h1>
         <h2>Support site</h2>
<ol>
Details of the FAQ and other sources of information are provided upon the Support tab above.
</ol>
<h2>Summary</h2>
<br />
   <span class=h3>Issue Tracker</span> is a simple implementation of a mechanism to track issues
   upon projects.  In this initial implementation this Joomla component is mainly based upon three simple
   database tables, supplemented by additional resource definition tables.<br /><br />
   Issue Tracker is a support system component, of a type commonly also known as 'Helpdesk' or 'Customer Support Software'.  This tool assists webmasters,
   developers and support staff to organise incoming queries or issues in an efficient manner, helping to provide a swift and efficient response.<br /><br />
   A configurable option enables users of the service offered by the web site to be able to create anytime a new issue ticket so that they can report a
   complaint, request, suggestion, etc. <br /><br />
   The service may be restricted to registered users only or made available to unregistered users as well.  This issue ticket will be attended and replied
   by the web supporters (operators or administrators). <br /><br />
   An issue ticket behaves much like a forum thread of messages, except for the fact that every issue ticket can be made private and only visible to the
   user/customer who created it and the operators and authorized staff of the web.<br /><br />
   There are no Joomla core files altered by this component.  It is a standard component
   installed automatically.<br />

<h2>Documentation</h2>

<ol style="list-style-type: upper-roman;">
   <li>
   <h3>Introduction</h3>
   These are the main pieces of information required to use Issue Tracker. You can
   view this documentation again by selecting the Issue Tracker Documentation
   tab from the Issue Tracker - Control Panel.<br />
   </li>

   <li>
   <h3>Installation and setup</h3>
   <ol>
      <li><span class="h4">Install</span><br />
      <ul>
         Upload the zip file to Joomla using the component installer in the usual way.
         <li>Go to issue tracker main control panel, from the Components menu of the Joomla backend</li>
      </ul>
      </li>

      <li><span class="h4">Uninstall</span><br />
      <ul>
         Uninstall the component using the Joomla component uninstaller in the
         usual way.
         <li>Go to the Extension Menu</li>
         <li>Click on the Manage tab</li>
         <li>Scroll through the list until you find the Issue Tracker component or change the type scroll list to components and scroll through the now shorter list until you find the Issue Tracker component</li>
            <li>Click on the check box next to the Issue Tracker entry</li>
            <li>Click on the Uninstall image in the top right hand corner.</li>
      </ul>
      </li>

      <li><span class="h4">Upgrading</span><br />
        <ul>
            <li>Install the new version that you downloaded from our web site over the
            current one, using the Joomla installer. All settings,
            are preserved upon upgrading.</li>
            <li>Alternatively use the Live Update Feature from the component Control Panel</li>
        </ul>
        </li>
    </ol>
   </li>

   <li>
   <h3>Settings For Using Issue Tracker</h3>
   <h4>Configuration</h4>

      Configuring Issue Tracker is straightforward.  The Options icon in
      the top right hand side of the page is where the settings for the component
      are established.<br /><br />
      General Settings permit the specifying of component defaults.<br />
      <ol>
         <li> Default delete mode.  Disabled, Hard or Soft </li>
         <li> The user to whom all issues are assigned if a user is soft deleted from the system. </li>
         <li> The default project to be used IF the person raising an issue does not have a default defined. </li>
         <li> The person to whom all issues are assigned initially after creation, unless specifically specified.</li>
         <li> The default publishing state of a new issue.</li>
         <li> The default role assigned to new users.</li>
         <li> The default priority of a new issue.</li>
         <li> The default issue type for a new issue if not specified.</li>
         <li> Separate issue number prefix for site and back end created issues.</li>
         <li> Ability to suppress issue summary in Control Panel if supplied defaults not used.</li>
         <li> Ability to suppress display of request for product information for front end created issues.</li>
      </ol>
      <br /><br />
      Spam Security settings<br />
      <ol>
         <li> A count for the maximum number of possible embedded links in the issue description.</li>
         <li> A list of words which if included in the issue description would class the issue as spam.</li>
         <li> Whether captcha is to be applied to the front end issue raising.</li>
      </ol>
      <br /><br />
      Email Settings<br />
      <ol>
         <li> Configuration settings for administrator notifications.</li>
         <li> Configuration settings for customer update notifications.</li>
      </ol>
      <br /><br />
      Issue Rules<br />
      <ol>
         <li> Banned URL list.  A list of URL which we do not permit to raise issues.</li>
         <li> Banned email list.  The list of email addressed for which we do not permit issue raising.</li>
         <li> Banned IP list.  The list of banned IP addresses which we do not permit to raise issues.</li>
      </ol>
      <br /><br />
      Permissions<br />
      <ol>
        <li>The standard Joomla permissions setting for the various abilities to create, edit, delete etc. issues and projects.</li>
      </ol>
   </li>
</ol>
<br />

<div class="small" style="text-align: center;">Copyright &copy;
<?php echo date('Y');?> G S Chapman - Macrotone Consulting Ltd.<br />
Distributed under the terms of the GNU General Public License.</div>
    </div>

  </tr>
</table>

<form method="post" name="adminForm" id="adminForm">
    <input type="hidden" name="c" value="default" />
    <input type="hidden" name="view" value="default" />
    <input type="hidden" name="option" value="com_issuetracker" />
    <input type="hidden" name="task" value="" />
</form>
