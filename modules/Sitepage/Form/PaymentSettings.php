<?php



/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: EditRole.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_PaymentSettings extends Engine_Form {

    public function init()
    {

        $this->setTitle('Settings');

        $this->addElement('Text', 'payment_action_label', array(
            'label' => "Display name on the Call to Action Button. (e.g. Donate, Contribute)",
            'allowEmpty' => false,
            'required' => false
        ));

        $this->addElement('Radio', 'payment_is_tax_deductible', array(
            'label' => 'Are donations to projects in this initiative tax deductible ?',
            'multiOptions' => array("1"=>"Yes", "0"=>"No"),
            'required' => true,
            'allowEmpty' => false,
            'value' => 0,
            'onchange' => 'onChangeIsTaxDeductible(this.value);'
        ));

        $this->addElement('Text', 'payment_tax_deductible_label', array(
            'label' => "Text display to encourge funding ?",
            'allowEmpty' => false,
            'required' => false
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Submit',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

    }
}