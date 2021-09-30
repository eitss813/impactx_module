<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: DashboardTransactionFilter.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_DashboardTransactionFilter extends Engine_Form {
    
    protected $_projectDate;
    public function getProjectDate() {
        return $this->_projectDate;
    }

    public function setProjectDate($date) {
        $this->_projectDate = $date;
        return $this;
    }
    public function init() {

        // $this->setTitle("Transactions")
            //$this->setDescription("Here, you can view list of backers, respective backed amount, commission charged from the backers, payment gateway used, payment status and date of transaction. Entering criteria into the filter fields will help you find specific detail.")
            $this->setAttrib('id', 'search_form')
            ->setMethod('post');

        
        $this->addElement('Text', 'backer_name', array(
            'label' => 'Backer’s Name',
            'placeholder' => 'Start typing user name....',
            'description' => '',
            'autocomplete' => 'off'));

        $this->addElement('Hidden', 'user_id', array());

        $this->addElement('Text', 'project_name', array(
            'label' => 'Project',
            'placeholder' => 'Start typing name....',
            'description' => '',
            'autocomplete' => 'off'));

        $this->addElement('Hidden', 'project_id', array(
            'order' => '998',
        ));

        $this->addElement('Text', 'transaction_min_amount', array(
            'label' => 'Transaction Amount',
            'placeholder' => 'min',
            'autocomplete' => 'off'));

        $this->addElement('Text', 'transaction_max_amount', array(
            'placeholder' => 'max',
            'autocomplete' => 'off'));

         $this->addElement('Text', 'commission_min_amount', array(
            'label' => 'Commission Amount',
            'placeholder' => 'min',
            'autocomplete' => 'off'));

        $this->addElement('Text', 'commission_max_amount', array(
            'placeholder' => 'max',
            'autocomplete' => 'off'));

        // Element: state
        $multiOptions = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding')->getPaymentStates(true); 
        
        $multiOptions = array_merge(array('' => ''), $multiOptions);
        $this->addElement('Select', 'payment_status', array(
            'label' => 'Payment Status',
            'multiOptions' => $multiOptions,
        ));

        $start_cal = new Engine_Form_Element_CalendarDateTime('start_cal');
        $start_cal->setLabel("From");
        $start_cal->setValue($this->_projectDate);

        $this->addElement($start_cal);

        $end_cal = new Engine_Form_Element_CalendarDateTime('end_cal');
        $end_cal->setLabel("To");
        $end_cal->setValue(date("Y-m-d H:i:s"));

        $this->addElement($end_cal);

        $this->addDisplayGroup(array('start_cal', 'end_cal'), 'grp2');
        $button_group = $this->getDisplayGroup('grp2');
        $button_group->setDecorators(array(
            'FormElements',
            'Fieldset',
            array('HtmlTag', array('tag' => 'div', 'id' => 'time_group2', 'style' => 'width:100%;'))
        ));

        // Buttons
        $this->addElement('Button', 'search', array(
            'label' => 'Search',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Button', 'clear', array(
            'label' => 'Clear',
            'decorators' => array('ViewHelper')
        ));
        $this->addDisplayGroup(array('search', 'clear'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');

        // $this->setDecorators(array(
        //     'FormElements',
        //     array(
        //         array('data' => 'HtmlTag'),
        //         array('tag' => 'div', 'class' => 'form-wrapper transaction-search-form')
        //     ),
        //     'Form'
        // ));
    }

}