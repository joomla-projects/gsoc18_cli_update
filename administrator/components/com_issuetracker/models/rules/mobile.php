<?php
/*
 *
 * @Version       $Id: mobile.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.5
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.form.formrule');

/**
* Form Rule class for the Joomla Framework.
*
* @package        Joomla.Framework
* @since          1.6
*/
class JFormRuleMobile extends JFormRule
{
    /**
    * Method to test the username for uniqueness.
    *
    * @param    object    $element    The JXMLElement object representing the <field /> tag for the
    *                                 form field object.
    * @param    mixed     $value      The form field value to validate.
    * @param    string    $group      The field name group control value. This acts as as an array
    *                                 container for the field. For example if the field has name="foo"
    *                                 and the group value is set to "bar" then the full field name
    *                                 would end up being "bar[foo]".
    * @param    object    $input      An optional JRegistry object with the entire data set to validate
    *                                 against the entire form.
    * @param    object    $form       The form object for which the field is being tested.
    *
    * @return   boolean               True if the value is valid, false otherwise.
    * @since    1.6
    * @throws   JException on invalid rule.
    */
    public function test(& $element, $value, $group = null, & $input = null, & $form = null)
    {
        /*
         * Here we match the value with a specific format. You may also use any kind of validation,
         * If you need a value of another field as well from the same form then use the following method:
         * $userId = ($input instanceof JRegistry) ? $input->get('id') : '0';
         * this gived you the value of the Id field
         */
        return preg_match("/^\+{0,1}[0-9]{6,14}$/",$value);
    }
}
