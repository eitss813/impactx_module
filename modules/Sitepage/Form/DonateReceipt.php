<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Overview.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_DonateReceipt extends Engine_Form {

    public $_error = array();

    public function init() {
        $this->setTitle('Donate Receipt For Organization')
            ->setAttrib('name', 'sitepages_donate_receipt');

        // Decorators
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);


       //logo
        $this->addElement('File', 'donate_receipt_logo', array(
            'label' => 'Logo',
            'allowEmpty' => true,
            'required' => false,
            'onchange' => 'openChangeModal(event)'
        ));

        $this->donate_receipt_logo->addValidator('Extension', false, 'jpg,jpeg,png');

        //location
        $this->addElement('Text', 'donate_receipt_location', array(
            'label' => 'Location',
            'description' => 'Eg: Fairview Park, Berkeley, CA',
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )));
        $this->donate_receipt_location->getDecorator('Description')->setOption('placement', 'append');

        include_once APPLICATION_PATH.'/application/modules/Seaocore/Form/specificLocationElement.php';


        //description
        $this->addElement('Textarea', 'donate_receipt_desc', array(
            'label' => 'Description',
            'required' => true,
            'allowEmpty' => false,
            'filters' => array('StripTags'),
        ));

        $this->addElement('Button', 'save', array(
            'label' => 'Save',
            'type' => 'submit',
        ));
    }

}

?>
