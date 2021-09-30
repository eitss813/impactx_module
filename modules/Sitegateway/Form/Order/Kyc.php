<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Kyc.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegateway_Form_Order_Kyc extends Engine_Form {

    public function init() {

        $this->setTitle('Upload KYC document for your MangoPay Account');
        $this->setAttrib('enctype', 'multipart/form-data');
        
        $description  = '<div id="mangopay_kyc_upload" class="tip">';
        $description .= $this->getTranslator()->translate('SITEGATEWAY_FORM_ORDER_MANGOPAY_KYC_DESCRIPTION');
        $description  .= '</div>';
        $this->setDescription($description);
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);
            

        $this->addElement('Select', 'document_type', array(
            'label' => 'Type',
            'allowEmpty' => false,
            'required' => true,
            'multiOptions' => array('IDENTITY_PROOF'=>'Proof of identity','ADDRESS_PROOF'=>'Proof of address'),
        ));
        $this->addElement('Text', 'tag', array(
            'label' => 'Tag',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
        )));

        $this->addElement('File', 'page', array(
            'label' => 'Upload page',
            'allowEmpty' => false,
            'required' => true,
        ));
        $this->page->addValidator('Extension', false, 'jpg,jpeg,png,gif');

        $this->addElement('Button', 'execute', array(
            'label' => 'Upload',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

/*        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), "siteevent_general", true),
            'decorators' => array(
                'ViewHelper',
            ),
        ));*/

        $this->addDisplayGroup(array(
            'execute',
            //'cancel',
                ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));
    }

}