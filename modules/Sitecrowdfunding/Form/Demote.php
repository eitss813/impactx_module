<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Demote.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Demote extends Engine_Form {

    public function init() {
        $this
                ->setTitle('Remove as Admin')
                ->setDescription('Are you sure you want to demote this guest from admin?')
        ;

        //$this->addElement('Hash', 'token');

        $this->addElement('Button', 'submit', array(
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
            'label' => 'Remove as Admin',
        ));

        $this->addElement('Cancel', 'cancel', array(
            'prependText' => ' or ',
            'label' => 'cancel',
            'link' => true,
            'href' => '',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            ),
        ));

        $this->addDisplayGroup(array(
            'submit',
            'cancel'
                ), 'buttons');
    }

}