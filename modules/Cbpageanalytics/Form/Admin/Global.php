<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    CB Page Analytics
 * @copyright  Copyright Consecutive Bytes
 * @license    https://consecutivebytes.com/agreement
 * @author     Consecutive Bytes
 */

/**
 * @category   Application_Extensions
 * @package    CB Page Analytics
 * @copyright  Copyright Consecutive Bytes
 * @license    https://consecutivebytes.com/agreement
 */
class Cbpageanalytics_Form_Admin_Global extends Engine_Form {

    public function init() {

        $this->setTitle('Global Settings')->setDescription('These settings affect all members in your community.');

        $this->addElement('Radio', 'cbpageanalytics_allow_plugin', array(
            'label' => 'Allow Plugin',
            'description' => 'Allow plugin to track page visits?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('cbpageanalytics.allow.plugin', 1),
        ));

        // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
