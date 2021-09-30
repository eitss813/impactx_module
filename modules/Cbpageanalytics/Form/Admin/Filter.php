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
class Cbpageanalytics_Form_Admin_Filter extends Engine_Form {

    public function init() {

        $this->setAttribs(array(
            'id' => 'filter_form',
            'class' => 'global_form_box',
        ));

        $this->addElement('Hidden', 'order', array(
            'order' => 10001,
        ));

        $this->addElement('Hidden', 'order_direction', array(
            'order' => 10002,
        ));

        $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
    }

}
