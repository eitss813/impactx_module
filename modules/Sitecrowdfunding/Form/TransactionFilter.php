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
class Sitecrowdfunding_Form_TransactionFilter extends Engine_Form {
    protected $_searchForm;
    public function init() {

        $this->setAttrib('id', 'search_form')->setMethod('post');

        $this->addElement('Hidden', 'page', array('order' => '1'));

        $this->addElement('Text', 'user_name', array(
            'label' => 'Backer’s Name',
            'placeholder' => 'Start typing backer name....',
            'description' => '',
            'autocomplete' => 'off'));

        $this->addElement('Hidden', 'user_id', array('order' => '2'));

        $this->addElement('Text', 'project_name', array(
            'label' => 'Project',
            'placeholder' => 'Start typing name....',
            'description' => '',
            'autocomplete' => 'off'));

        $this->addElement('Hidden', 'project_id', array('order' => '3'));

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

        $this->addElement('select', 'sort_field', array(
            'label' => 'Sort By',
            'multiOptions' => array(
                '' => '',
                'transaction_id' => 'Transaction Id',
                'project_name' => 'Project Name',
                'user_name' => 'Backers Name',
                'transaction_amount' => 'Transaction Amount',
                'commission_amount' => 'Commission Amount',
                'gateway' => 'Gateway',
                'payment_status' => 'Payment Status',
                'date' => 'Date'
            )
        ));

        $this->addElement('select', 'sort_direction', array(
            'label' => 'Sort Direction',
            'multiOptions' => array(
                '' => '',
                'asc' => 'Ascending',
                'desc' => 'Descending'
            )
        ));

        $start_cal = new Engine_Form_Element_CalendarDateTime('start_cal');
        $start_cal->setLabel("Payment From");
        $this->addElement($start_cal);

        $end_cal = new Engine_Form_Element_CalendarDateTime('end_cal');
        $end_cal->setLabel("Payment To");
        $this->addElement($end_cal);

        $this->addDisplayGroup(array('start_cal', 'end_cal'), 'grp2');
        $button_group = $this->getDisplayGroup('grp2');
        $button_group->setDecorators(array(
            'FormElements',
            'Fieldset',
            array('HtmlTag', array('tag' => 'div', 'id' => 'time_group2', 'style' => 'width:100%;'))
        ));

        // location
        $this->addElement('Text', 'location', array(
            'label' => 'Where',
            'description' => 'Eg: Fairview Park, Berkeley, CA',
            'autocomplete' => 'off',
            'value' => '',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));
        $this->location->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
        $this->addElement('Hidden', 'locationParams', array('order' => 800000));
        include_once APPLICATION_PATH . '/application/modules/Seaocore/Form/specificLocationElement.php';

        // miles
        $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.proximity.search.kilometer', 0);
        if ($flage) {
            $locationLable = "Within Kilometers";
            $locationOption = array(
                '0' => '',
                '1' => '1 Kilometer',
                '2' => '2 Kilometers',
                '5' => '5 Kilometers',
                '10' => '10 Kilometers',
                '20' => '20 Kilometers',
                '50' => '50 Kilometers',
                '100' => '100 Kilometers',
                '250' => '250 Kilometers',
                '500' => '500 Kilometers',
                '750' => '750 Kilometers',
                '1000' => '1000 Kilometers',
            );
        } else {
            $locationLable = "Within Miles";
            $locationOption = array(
                '0' => '',
                '1' => '1 Mile',
                '2' => '2 Miles',
                '5' => '5 Miles',
                '10' => '10 Miles',
                '20' => '20 Miles',
                '50' => '50 Miles',
                '100' => '100 Miles',
                '250' => '250 Miles',
                '500' => '500 Miles',
                '750' => '750 Miles',
                '1000' => '1000 Miles',
            );
        }
        $this->addElement('Select', 'locationmiles', array(
            'label' => $locationLable,
            'multiOptions' => $locationOption,
            'value' => '0'
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

    }

}