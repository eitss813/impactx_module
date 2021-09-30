<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 7/29/2016
 * Time: 2:40 PM
 */
class Yndynamicform_Form_Admin_EntrySearch extends Engine_Form
{
    public function init()
    {
        $this->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

        $this->setAttribs(array(
            'id' => 'filter_form',
            'class' => 'global_form_box',
            'method'=>'GET',
        ));

        $view = Zend_Registry::get('Zend_View');

        // Element Title
        $this->addElement('Text', 'entry_id', array(
            'placeholder' => $view->translate('ID')
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

        // Search
        $this->addElement('Checkbox', 'advsearch', array(
            'label' => 'Advanced',
            'onchange' => 'yndformToggleConditionalLogic(this)'
        ));

        // Element Submit
        $this->addElement('Button', 'search', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));


        $group = array(
            'entry_id',
            'start_date',
            'to_date',
            'advsearch',
            'search',
        );

        $this->addDisplayGroup($group, 'elements');
        $this->elements->getDecorator('HtmlTag')->removeOption('class');
        $this->elements->removeDecorator('FieldSet');

        //conditional logic
        $this->addElement('dummy', 'conditional_logic_tpl', array(
            'decorators' => array( array(
                'ViewScript',
                array(
                    'viewScript' => '_advanced_search.tpl',
                    'class' => 'form_element',
                )
            )),
        ));

        $this->addDisplayGroup(array('conditional_logic_tpl'), 'conditional_logic');
        $this->conditional_logic->getDecorator('HtmlTag')->removeOption('class');
        $this->conditional_logic->removeDecorator('FieldSet');

        $this->addElement('Hidden','direction', array(
            'order' => '9999',
        ));
        $this->addElement('Hidden','fieldOrder', array(
            'order' => '9998',
            'value' => 'DESC',
        ));
    }
}