<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Blockurl.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Admin_Shorturl_Blockurl extends Engine_Form {

    protected $_field;

    public function init() {
        $this
                ->setMethod('post')
                ->setAttrib('class', 'global_form_box')
        ;
        $this->addElement('Text', 'bannedwords', array(
            'label' => 'Add URL',
            'description' => '',
            'allowEmpty' => false,
            'required' => true,
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Add URL',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onClick' => 'javascript:parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
    }

    public function setField($category) {
        $this->_field = $category;

        $this->bannedwords->setValue($category[0]['word']);
        $this->submit->setLabel('Save Changes');
        $this->bannedwords->setLabel('Edit URL');
    }

}
