<?php
/*
 *
 * @Version       $Id: default_credits.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.2
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

defined('_JEXEC') or die;
$db   = JFactory::getDBO();
$sql  = "SELECT version FROM ".$db->quoteName('#__it_meta')." WHERE type='component'";
$db->setQuery( $sql);
$version = $db->loadResult();
?>
<div style="text-align:center">`
   <div style="margin: 10px 0 0 0">
      <h1><?php echo JText::_('COM_ISSUETRACKER'); ?></h1>
   </div>

   <div>
      <h2><?php echo JText::_('COM_ISSUETRACKER_VERSION') . " " . $version; ?></h2>
   </div>

   <div>
      <h3><?php echo 'Translation Credits'; ?></h3>
   </div>

   <div>
      <table style="width: 80%; height: 248px; margin-left: auto; table-layout: fixed; border:1px solid #C3C3C3; margin-right: auto; font-size: 10pt;">
         <tbody>
            <tr style="border: 1px solid #C3C3C3; font-size: 12pt;">
               <td style="text-align: center;"><strong>Language</strong></td>
               <td style="text-align: center;"><strong>Code</strong></td>
               <td style="text-align: center;"><strong>Name</strong></td>
            </tr>
            <tr style="border: 1px solid #C3C3C3;">
               <td style="text-align: center;">Czech (Czech Republic)</td>
               <td style="text-align: center;">cs_CZ</td>
               <td style="text-align: center;">Vlastislav Sucharda<td>
            </tr>
            <tr style="border: 1px solid #C3C3C3;">
               <td style="text-align: center;">Danish (Denmark)</td>
               <td style="text-align: center;">fr_FR</td>
               <td style="text-align: center;">Ole Schelde (oschelde)<td>
            </tr>
            <tr style="border: 1px solid #C3C3C3;">
               <td style="text-align: center;">Dutch (Netherlands)</td>
               <td style="text-align: center;">nl_NL</td>
               <td style="text-align: center;"><a href="http://www.joomladownloads.nl" target="_blank">Gerard van Enschut</a><br />
                  <a href="http://www.byimke.nl" target="_blank" rel="nofollow">Imke Philipoom</a><td>
            </tr>
            <tr style="border: 1px solid #C3C3C3;">
               <td style="text-align: center;">French (France)</td>
               <td style="text-align: center;">fr_FR</td>
               <td style="text-align: center;">Pedwo51 (Pierre D)<br />Emmanuel Ruchon<br />Kèvin<br />Laurent Gougeon<td>
            </tr>
            <tr style="border: 1px solid #C3C3C3;">
               <td style="text-align: center;">German (Germany)<br /></td>
               <td style="text-align: center;">de_DE</td>
               <td style="text-align: center;"><a href="http://www.tboje.de">Thomas Boje</a>
                  <br />Stefan Gertmayr</td>
            </tr>
            <tr style="border: 1px solid #C3C3C3;">
               <td style="text-align: center;">Hungary (Hungarian)<br /></td>
               <td style="text-align: center;">hu_HU</td>
               <td style="text-align: center;">scheibj</td>
            </tr>
            <tr style="border: 1px solid #C3C3C3;">
               <td style="text-align: center;">Italian (Italy)</td>
               <td style="text-align: center;">it_IT</td>
               <td style="text-align: center;">Bobbix (Roberto Lenti)<br />Filippo Aceto<br />cbernocco</td>
            </tr>
            <tr style="border: 1px solid #C3C3C3;">
               <td style="text-align: center;">Latvian (Latvia)</td>
               <td style="text-align: center;">lv_LV</td>
               <td style="text-align: center;">zanc (Žanis Vuguls)</td>
            </tr>
            <tr style="border: 1px solid #C3C3C3;">
               <td style="text-align: center;">Polish (Poland)</td>
               <td style="text-align: center;">pl_PL</td>
               <td style="text-align: center;">kaliphast (Michal Kaminski)<br />mrgalwen</td>
            </tr>
            <tr style="border: 1px solid #C3C3C3;">
               <td style="text-align: center;">Portuguese (Brazilian)</td>
               <td style="text-align: center;">pt_BR</td>
               <td style="text-align: center;">Carlos Rodrigues de Souza</td>
            </tr>
            <tr style="border: 1px solid #C3C3C3;">
               <td style="text-align: center;">Portuguese (Portugal)</td>
               <td style="text-align: center;">pt_PT</td>
               <td style="text-align: center;">horus68 (Paulo Pereira)</td>
            </tr>
            <tr style="border: 1px solid #C3C3C3;">
               <td style="text-align: center;">Romanian (Romania)</td>
               <td style="text-align: center;">ro_RO</td>
               <td style="text-align: center;">extradragon (Catalin Dragomirescu)</td>
            </tr>
            <tr style="border: 1px solid #C3C3C3;">
               <td style="text-align: center;">Russian (Russia)</td>
               <td style="text-align: center;">ru_RU</td>
               <td style="text-align: center;">pikachurus (pikachu)<br />intrif</td>
            </tr>
            <tr style="border: 1px solid #C3C3C3;">
               <td style="text-align: center;">Serbian (Latin)</td>
               <td style="text-align: center;">sr_YU</td>
               <td style="text-align: center;">i0wi (Nikola Jovic)</td>
            </tr>
            <tr style="border: 1px solid #C3C3C3;">
               <td style="text-align: center;">Serbian (Serbia)</td>
               <td style="text-align: center;">sr_RS</td>
               <td style="text-align: center;">i0wi (Nikola Jovic)</td>
            </tr>
            <tr style="border: 1px solid #C3C3C3;">
               <td style="text-align: center;">Slovenian (Slovenia)</td>
               <td style="text-align: center;">sl_SL</td>
               <td style="text-align: center;">tajzel (Rok)</td>
            </tr>
            <tr style="border: 1px solid #C3C3C3;">
               <td style="text-align: center;">Spanish (Spain)</td>
               <td style="text-align: center;">es_ES</td>
               <td style="text-align: center;">mafiu2013 (Alexis Gustavo Valencia Valenzuela)<br />refrito (Federico Franco Jaramillo)<br />Skraw</td>
            </tr>
            <tr style="border: 1px solid #C3C3C3;">
               <td style="text-align: center;">Turkish (Turkey)</td>
               <td style="text-align: center;">tr_TR</td>
               <td style="text-align: center;"><a href="http://gulderenler.com/" target="_blank">Gulderenler Bilisim Ltd</a></td>
            </tr>
            <tr style="border: 1px solid #C3C3C3;">
               <td style="text-align: center;">Vietnamese</td>
               <td style="text-align: center;">vi_VN</td>
               <td style="text-align: center;">ppanhh (Phan Anh)</td>
            </tr>
         </tbody>
      </table>

      <p>&nbsp;</p>
      <p>Our apologies to anyone we may have unintentionally omitted from the above list.</p>

      <br />
      <?php echo JText::_('COM_ISSUETRACKER_CREDIT_TEXT2'); ?>
      <br /><br />
      <?php echo JText::_('COM_ISSUETRACKER_CREDIT_TEXT3'); ?>
   </div>
</div>
