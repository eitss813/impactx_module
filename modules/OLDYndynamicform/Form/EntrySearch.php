<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 7/29/2016
 * Time: 2:40 PM
 */
class Yndynamicform_Form_EntrySearch extends Engine_Form
{
    public function init()
    {
        $this->setAttribs(array(
            'class' => 'global_form f1 yndform_my_entries',
            'id' => 'yndform_my_entries',
            ))
            ->setMethod('GET');

        $view = Zend_Registry::get('Zend_View');

        // Element Title
        $this->addElement('Text', 'keyword', array(
            'placeholder' => $view->translate('Form title')
        ));

        // Element Title
        $this->addElement('Text', 'entry_id', array(
            'placeholder' => $view->translate("ID or owner's entry")
        ));

        // Element From and To Date
        $date_validate = new Zend_Validate_Date("YYYY-MM-dd");
        $date_validate->setMessage("Please pick a valid day (yyyy-mm-dd)", Zend_Validate_Date::FALSEFORMAT);

        // From Date
        $start = new Engine_Form_Element_Text('start_date');
        $start -> setAttribs(array('placeholder' => $view->translate('Submission date (From)')));
        $start -> setAllowEmpty(true);
        $start -> addValidator($date_validate);
        $this -> addElement($start);

        // To Date
        $end = new Engine_Form_Element_Text('to_date');
        $end -> setAttribs(array('placeholder' => $view->translate('Submission date (To)')));
        $end -> setAllowEmpty(true);
        $end -> addValidator($date_validate);
        $this -> addElement($end);

        // Element Submit
        $this->addElement('Button', 'search', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));

        $this->addElement('Hidden','direction', array(
            'order' => '9999',
        ));
        $this->addElement('Hidden','fieldOrder', array(
            'order' => '9998',
            'value' => 'DESC',
        ));
    }
}