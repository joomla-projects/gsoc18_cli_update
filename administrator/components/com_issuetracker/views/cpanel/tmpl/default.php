<?php
/*
 *
 * @Version       $Id: default.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.4
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

$user = JFactory::getUser();

$db   = JFactory::getDBO();
$sql  = "SELECT version FROM ".$db->quoteName('#__it_meta')." WHERE type='component'";
$db->setQuery( $sql);
$version = $db->loadResult();

// website root directory
$_root = JURI::root();
$lang = JFactory::getLanguage();

JHTML::_('behavior.framework');
JHtml::_('behavior.modal');
//Add css

$script = <<<ENDSCRIPT
window.addEvent( 'domready' ,  function() {
   $('btnchangelog').addEvent('click', showChangelog);
});

function showChangelog()
{
    SqueezeBox.fromElement(
        $('tracker-changelog'), {
            handler: 'adopt',
            size: {
                x: 550,
                y: 500
            }
        }
    );
}
ENDSCRIPT;

$document = JFactory::getDocument();
$document->addScriptDeclaration($script,'text/javascript');

$compparams = JComponentHelper::getParams('com_issuetracker');
$advaudit   = $compparams->get('enable_adv_audit', '0');

?>

<?php if (!empty( $this->sidebar)) : ?>
  <div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
  </div>
  <div id="j-main-container" class="span10">
<?php else : ?>
  <div id="j-main-container">
<?php endif;?>

   <div id="cpanel" class="row-fluid">
     <div class="span5">
        <h2><?php echo JText::_('COM_ISSUETRACKER_TOOLS') ?></h2>

        <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>; margin-<?php echo ($lang->isRTL()) ? 'left' : 'right'; ?>: 10px;">
          <div class="icon">
            <a href="index.php?option=com_issuetracker&amp;view=itissueslist">
            <div class="it-icon32 it-icon32-issues">&nbsp;</div>
            <span><?php echo JText::_('COM_ISSUETRACKER_ISSUES'); ?></span>
            </a>
          </div>
        </div>

        <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>; margin-<?php echo ($lang->isRTL()) ? 'left' : 'right'; ?>: 10px;">
          <div class="icon">
            <a href="index.php?option=com_issuetracker&amp;view=itpeoplelist">
            <div class="it-icon32 it-icon32-users">&nbsp;</div>
            <span><?php echo JText::_('COM_ISSUETRACKER_PEOPLE'); ?></span>
            </a>
          </div>
        </div>

        <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>; margin-<?php echo ($lang->isRTL()) ? 'left' : 'right'; ?>: 10px;">
          <div class="icon">
            <a href="index.php?option=com_issuetracker&amp;view=itprojectslist">
            <div class="it-icon32 it-icon32-projects">&nbsp;</div>
            <span><?php echo JText::_('COM_ISSUETRACKER_PROJECTS'); ?></span>
          </a>
        </div>
      </div>

      <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>; margin-<?php echo ($lang->isRTL()) ? 'left' : 'right'; ?>: 10px;">
        <div class="icon">
          <a href="index.php?option=com_issuetracker&view=dbtasks&task=syncusers" id="optimize">
          <div class="it-icon32 it-icon32-sync">&nbsp;</div>
          <span><?php echo JText::_('COM_ISSUETRACKER_SYNC_USERS'); ?></span>
          </a>
        </div>
      </div>

      <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>; margin-<?php echo ($lang->isRTL()) ? 'left' : 'right'; ?>: 10px;">
        <div class="icon">
          <a href="index.php?option=com_issuetracker&view=itloglist">
          <div class="it-icon32 it-icon32-logs">&nbsp;</div>
          <span><?php echo JText::_('COM_ISSUETRACKER_DISPLAY_LOG'); ?></span>
          </a>
        </div>
      </div>

      <?php if ( $this->params->get('enable_adv_audit', 0) && IssueTrackerHelper::check_db_priv('TRIGGER') ) { ?>
         <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>; margin-<?php echo ($lang->isRTL()) ? 'left' : 'right'; ?>: 10px;">
            <div class="icon">
               <a href="index.php?option=com_issuetracker&view=jtriggers">
                  <div class="it-icon32 it-icon32-triggers">&nbsp;</div>
                  <span><?php echo JText::_('COM_ISSUETRACKER_TRIGGERS'); ?></span>
               </a>
           </div>
         </div>
      <?php } ?>

      <div style="clear: both;"></div>

      <h2><?php echo JText::_('COM_ISSUETRACKER_UPDATES') ?></h2>

      <?php echo LiveUpdate::getIcon(); ?>

      <div style="clear: both;"></div>

      <?php if ( IssueTrackerHelper::check_proc_exists('#__add_it_sample_data') == 1 ) { ?>
         <h2><?php echo JText::_('COM_ISSUETRACKER_SDATA') ?></h2>

         <!-- ?php if($this->isMySQL): ? -->
         <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>; margin-<?php echo ($lang->isRTL()) ? 'left' : 'right'; ?>: 10px;">
            <div class="icon">
               <a href="index.php?option=com_issuetracker&view=dbtasks&task=addsampledata" id="optimize">
               <div class="it-icon32 it-icon32-addsdata">&nbsp;</div>
               <span><?php echo JText::_('COM_ISSUETRACKER_ADD_SDATA'); ?></span>
               </a>
            </div>
         </div>

         <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>; margin-<?php echo ($lang->isRTL()) ? 'left' : 'right'; ?>: 10px;">
            <div class="icon">
               <a href="index.php?option=com_issuetracker&view=dbtasks&task=remsampledata" id="optimize">
               <div class="it-icon32 it-icon32-delsdata">&nbsp;</div>
               <span><?php echo JText::_('COM_ISSUETRACKER_DEL_SDATA'); ?></span>
               </a>
            </div>
         </div>
      <?php } ?>
      <div class="clr"></div>
   </div>

   <div id="tabs" class="span6" >
      <?php echo JHtml::_('bootstrap.startTabSet', 'cpanelTab', array('active' => 'about')); ?>
         <?php if ($this->params->get('show_summary_rep', 0)) {
            echo JHtml::_('bootstrap.addTab', 'cpanelTab', 'summaryissuestab', JText::_('COM_ISSUETRACKER_SUMMARY_ISSUES', true));
            echo "<div>";
            $rows = &$this->summaryIssues;
            echo '<table style="width:100%; border-collapse: separate; border-spacing: 1px;" >';
            echo "<thead>";
            echo "<tr>";
            echo '<td style="padding:2px">'.JText::_('COM_ISSUETRACKER_PROJECT_NAME')."</td>";
            echo '<td style="padding:2px">'.JText::_('COM_ISSUETRACKER_FIRST_OPENED_DATE')."</td>";
            echo '<td style="padding:2px">'.JText::_('COM_ISSUETRACKER_LAST_CLOSED_DATE')."</td>";
            echo '<td style="padding:2px">'.JText::_('COM_ISSUETRACKER_TOTAL_ISSUES')."</td>";
            echo '<td style="padding:2px">'.JText::_('COM_ISSUETRACKER_OPEN_ISSUES')."</td>";
            echo '<td style="padding:2px">'.JText::_('COM_ISSUETRACKER_ONHOLD_ISSUES')."</td>";
            echo '<td style="padding:2px">'.JText::_('COM_ISSUETRACKER_INPROGRESS_ISSUES')."</td>";
            echo '<td style="padding:2px">'.JText::_('COM_ISSUETRACKER_CLOSED_ISSUES')."</td>";
            echo '<td style="padding:2px">'.JText::_('COM_ISSUETRACKER_OPEN_NOPRIOR')."</td>";
            echo '<td style="padding:2px">'.JText::_('COM_ISSUETRACKER_OPEN_HIGH')."</td>";
            echo '<td style="padding:2px">'.JText::_('COM_ISSUETRACKER_OPEN_MEDIUM')."</td>";
            echo '<td style="padding:2px">'.JText::_('COM_ISSUETRACKER_OPEN_LOW')."</td>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            foreach ( $rows as $row) { ?>
            <tr>
               <td style="padding:2px"><?php echo $row->project_name; ?></td>
               <td style="padding:2px"><?php echo $row->first_identified; ?></td>
               <td style="padding:2px"><?php echo $row->last_closed; ?></td>
               <td style="padding:2px"><?php echo $row->total_issues; ?></td>
               <td style="padding:2px"><?php echo $row->open_issues; ?></td>
               <td style="padding:2px"><?php echo $row->onhold_issues; ?></td>
               <td style="padding:2px"><?php echo $row->inprogress_issues; ?></td>
               <td style="padding:2px"><?php echo $row->closed_issues; ?></td>
               <td style="padding:2px"><?php echo $row->open_no_prior; ?></td>
               <td style="padding:2px"><?php echo $row->open_high_prior; ?></td>
               <td style="padding:2px"><?php echo $row->open_medium_prior; ?></td>
               <td style="padding:2px"><?php echo $row->open_low_prior; ?></td>
            </tr>
            <?php } echo "</tbody>";
            echo "</table>";
            echo "</div>";
            echo JHtml::_('bootstrap.endTab');
         }

         echo JHtml::_('bootstrap.addTab', 'cpanelTab', 'latestissuestab', JText::_('COM_ISSUETRACKER_LATEST_ISSUES', true));
         echo "<div>";
         $rows = &$this->latestIssues;
         echo '<table style="width:100%; border-collapse: separate; border-spacing: 1px;" >';
         echo "<thead>";
         echo "<tr>";
         echo '<td style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_IDENTIFIED_DATE')."</td>";
         echo '<td style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_ISSUE_SUMMARY')."</td>";
         echo '<td style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_PROJECT_NAME')."</td>";
         echo '<td style="padding: 2px;">'.JText::_('JPUBLISHED')."</td>";
         echo "</tr>";
         echo "</thead>";
         echo "<tbody>";
         foreach ( $rows as $row) {
         $link    = JRoute::_( 'index.php?option=com_issuetracker&task=itissues.edit&id='. $row->id ); ?>
         <tr>
            <td style="padding:2px"><?php echo $row->issuedate; ?></td>
            <td style="padding:2px"><?php echo "<a href='" . $link . "'>"; echo $row->issue_summary; echo "</a>"; ?></td>
            <td style="padding:2px"><?php echo $row->project_name; ?></td>
            <!-- td style="padding:2"><?php if ( $row->state) {
            echo "<img src='" . $_root . "administrator/templates/bluestork/images/admin/tick.png' width='16px' height='16px' />";
            } else {
            echo "<img src='" . $_root . "administrator/templates/bluestork/images/admin/publish_r.png' width='16px' height='16px' />";
            }
            ?>
            </td -->
            <td class="center">
              <?php echo JHtml::_('jgrid.published', $row->state, $row, 'itissueslist.', 0, 'cb'); ?>
            </td>
         </tr>
         <?php  } echo "</tbody>";
         echo "</table>";
         echo "</div>";
         echo JHtml::_('bootstrap.endTab' );

      echo JHtml::_('bootstrap.addTab', 'cpanelTab', 'overdueissuestab', JText::_('COM_ISSUETRACKER_OVERDUE_ISSUES', true));
      echo "<div>";
      $rows = &$this->overdueIssues;
      echo '<table style="width:100%; border-collapse: separate; border-spacing: 1px;" >';
      echo "<thead>";
      echo "<tr>";
      echo '<td style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_ASSIGNEE')."</td>";
      echo '<td style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_TARGET_DATE')."</td>";
      echo '<td style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_PROJECT_NAME')."</td>";
      echo '<td style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_PRIORITY')."</td>";
      echo '<td style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_ISSUE_SUMMARY')."</td>";
      echo "</tr>";
      echo "</thead>";
      echo "<tbody>";
      foreach ( $rows as $row ) {
        $link    = JRoute::_( 'index.php?option=com_issuetracker&task=itissues.edit&id='. $row->id ); ?>
      <tr>
        <td style="padding:2px"><?php echo $row->assignee; ?></td>
        <td style="padding:2px"><?php echo $row->target_resolution_date; ?></td>
        <td style="padding:2px"><?php echo $row->project_name; ?></td>
        <td style="padding:2px"><?php echo $row->priority; ?></td>
        <td style="padding:2px"><?php echo "<a href='" . $link . "'>"; echo $row->issue_summary; echo "</a>"; ?></td>
      </tr>
      <?php  } echo "</tbody>";
         echo "</table>";
         echo "</div>";
         echo JHtml::_('bootstrap.endTab' ); ?>

         <?php if ($this->params->get('show_unassigned_rep', 0)) {
            echo JHtml::_('bootstrap.addTab', 'cpanelTab', 'unassignedissuestab', JText::_('COM_ISSUETRACKER_UNASSIGNED_ISSUES', true));
            echo "<div>";
            $rows = &$this->unassignedIssues;
            echo '<table style="width:100%; border-collapse: separate; border-spacing: 1px;" >';
            echo "<thead>";
            echo "<tr>";
            echo '<td style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_ISSUE_SUMMARY')."</td>";
            echo '<td style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_PROJECT_NAME')."</td>";
            echo '<td style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_IDENTIFIEE')."</td>";
            echo '<td style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_TARGET_DATE')."</td>";
            echo '<td style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_PRIORITY')."</td>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            foreach ( $rows as $row) {
              $link    = JRoute::_( 'index.php?option=com_issuetracker&task=itissues.edit&id='. $row->id ); ?>
            <tr>
               <td style="padding: 2px;"><?php echo "<a href='" . $link . "'>"; echo $row->issue_summary; echo "</a>"; ?></td>
               <td style="padding: 2px;"><?php echo $row->project_name; ?></td>
               <td style="padding: 2px;"><?php echo $row->identifiee; ?></td>
               <td style="padding: 2px;"><?php echo $row->target_resolution_date; ?></td>
               <td style="padding: 2px;"><?php echo $row->priority; ?></td>
            </tr>
            <?php  } echo "</tbody>";
            echo "</table>";
            echo "</div>";
            echo JHtml::_('bootstrap.endTab');
         } ?>


      <?php echo JHtml::_('bootstrap.addTab', 'cpanelTab', 'about', JText::_('COM_ISSUETRACKER_ABOUT', true)); ?>
      <div style="text-align:center">
         <div style="margin: 10px 0 0 0">
            <h1>Issue Tracker</h1>
         </div>

         <div>
            <h2><?php echo JText::_('COM_ISSUETRACKER_VERSION') . " " . $version; ?></h2>
         </div>

         <div>
            <h3><?php echo JText::_('COM_ISSUETRACKER_BY'); ?></h3>
         </div>

         <div style="margin: 10px 0 10px 0">
            <a href="http://www.macrotoneconsulting.co.uk" title="Macrotone" target="_blank"><img alt="Macrotone Consulting Ltd." src="../media/com_issuetracker/images/system/macrotone.png" /></a>
         </div>

         <div>
            <br />
            G S Chapman
            <br /><br />
            <a href="#" id="btnchangelog" class="btn btn-info btn-mini">CHANGELOG</a>
            <br />
         </div>
         <div style="display:none;">
            <div id="tracker-changelog">
              <?php require_once dirname(__FILE__).'/coloriser.php';
                echo IssueTrackerChangelogColoriser::colorise(JPATH_COMPONENT_ADMINISTRATOR.'/CHANGELOG.php');
              ?>
            </div>
         </div>

         <div style="text-align: center;" >
            <br />
            <a target="_blank" title="Donate online to Macrotone" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=macrotone%40macrotone%2eco%2euk&lc=GB&item_name=Macrotone%2eco%2euk&item_number=Issue%20Tracker&no_note=0&currency_code=GBP&bn=PP%2dDonationsBF%3apaypal%2epng%3aNonHostedGuest">
            <img src="https://www.paypal.com/en_US/i/btn/x-click-butcc-donate.gif" alt="PayPal - The safer, easier way to donate online!"/></a>
         </div>

         <div>
            <br /><br />
            <?php echo JText::_('COM_ISSUETRACKER_CREDIT_TEXT4'); ?>
         </div>

         <div class="clr"></div>
      </div>
      <?php echo JHtml::_('bootstrap.endTab');
         echo JHtml::_('bootstrap.addTab', 'cpanelTab', 'credits', JText::_('COM_ISSUETRACKER_CREDITS', true));
         echo $this->loadTemplate('credits');
         echo JHtml::_('bootstrap.endTab');
         echo JHtml::_('bootstrap.endTabSet');
      ?>
    </div>
  </div>

  <div class="clr"></div>

<form method="post" name="adminForm" id="adminForm">
  <input type="hidden" name="c" value="default" />
  <input type="hidden" name="view" value="default" />
  <input type="hidden" name="option" value="com_issuetracker" />
  <input type="hidden" name="task" value="" />
</form>