<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Cancel.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Package_Cancel extends Engine_Form {

    public function init() {
        $this
                ->setTitle('Cancel Package')
                ->setDescription(Zend_Registry::get('Zend_Translate')->_('Note: this will attempt to cancel the recurring payment regardless of current package status.'))
                ->setAttrib('class', 'global_form_popup')
        ;

        // Token
        $this->addElement('Hash', 'token');

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Cancel Package',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
    }

}
