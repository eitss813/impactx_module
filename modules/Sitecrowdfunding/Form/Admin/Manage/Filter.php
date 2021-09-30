<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Filter.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Admin_Manage_Filter extends Engine_Form {

    public function init() {

        $this
                ->setAttribs(array(
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