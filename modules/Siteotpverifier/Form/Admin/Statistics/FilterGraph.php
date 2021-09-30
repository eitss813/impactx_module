<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecredit
 * @copyright  Copyright 2016-2017 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: FilterGraph.php 2017-03-08 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteotpverifier_Form_Admin_Statistics_FilterGraph extends Engine_Form {

  public function init() {
    $this
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this
            ->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
            ));

    // Init mode
    $this->addElement('Select', 'mode', array(
        'label' => 'See',
        'multiOptions' => array(
            'all' => 'All',
            'signup' => 'SMS sent for Signup ',
            'login' => 'SMS sent for Login',
            'forget' => 'SMS sent for Resetting Forget Password',
            'edit' => 'SMS sent for Editing Phone Number',
            'add' => 'SMS sent for Adding New Number',
            'admin_sent' => 'SMS sent by Admin',
        ),
        'onchange'=>'onModeChange()',
        'value' => 'all',
    ));

    $this->mode->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'));

    $this->addElement('Select', 'type', array(
        'label' => 'Service',
        'multiOptions' => array(
            'amazon' => 'Amazon',
            'twilio' => 'Twilio',
            'testmode' => 'Virtual SMS Client'
        ),
        'value' => 'amazon',
    ));

    $this->type->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'));

    // Init period
    $this->addElement('Select', 'period', array(
        'label' => 'Duration',
        'multiOptions' => array(         
            Zend_Date::WEEK => 'This Week',
            Zend_Date::MONTH => 'This Month',
            Zend_Date::YEAR => 'This Year',
        ),
        'value' => 'ww',
    ));
    $this->period->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'));
    // Init chunk
    $this->addElement('Select', 'chunk', array(
        'label' => 'Time Summary',
        'multiOptions' => array(
            Zend_Date::DAY => 'By Day',
            Zend_Date::WEEK => 'By Week',
            Zend_Date::MONTH => 'By Month'
        ),
        'value' => 'dd',
    ));
    $this->chunk->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'));
    // Init submit
    $this->addElement('Button', 'submit', array(
        'label' => 'Filter',
        'type' => 'submit',
        'onclick' => 'return processStatisticsFilter($(this).getParent("form"))',
    ));
  }

}