<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Create.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitegateway_Form_Admin_Integration_Create extends Engine_Form {

    public function init() {

        $this->setDescription("Enter the name of your new payment gateway and download the skeleton of files with proper directory structure by clicking on 'Create Skeleton' button. New payment gateway name should be in lowercase and contain alphanumeric characters only.");

        $this->addElement('Text', 'name', array(
            'required' => true,
            'allowEmpty' => false,
            'autocomplete' => 'off',
            'onblur' => 'replaceSkeletonName(this);return false;',
            'validators' => array(
                array('Regex', true, array('/^[a-z][a-z0-9]+$/')),
            ),
        ));

        $this->addElement('Button', 'execute', array(
            'label' => 'Create Skeleton',
            'decorators' => array('ViewHelper'),
            'class' => 'mtop10',
            'type' => 'submit',
        ));
    }

}
