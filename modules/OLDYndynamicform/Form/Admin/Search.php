<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 7/29/2016
 * Time: 2:40 PM
 */
class Yndynamicform_Form_Admin_Search extends Engine_Form
{
    public function init()
    {
        $this->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

        $this->setAttribs(array(
            'class' => 'global_form_box',
            'id' => 'filter_form'))
            ->setMethod('GET');

        $view = Zend_Registry::get('Zend_View');

        // Element Title
        $this->addElement('Text', 'keyword', array(
            'placeholder' => $view->translate('Title')
        ));

        // Element Categories
        $this->addElement('Select', 'category_id', array(
            'placeholder' => 'Category',
            'multiOptions' => array(
                'all' => 'All categories'
            ),
        ));
        $this->populateCategoryElement();

        // Element From and To Date
        $date_validate = new Zend_Validate_Date("YYYY-MM-dd");
        $date_validate->setMessage("Please pick a valid day (yyyy-mm-dd)", Zend_Validate_Date::FALSEFORMAT);

        // From Date   
        $start = new Engine_Form_Element_Text('start_date');
        $start -> setAttribs(array('placeholder' => $view->translate('Creation Date (From)')));
        $start -> setAllowEmpty(true);
        $start -> addValidator($date_validate);
        $this -> addElement($start);

        // To Date
        $end = new Engine_Form_Element_Text('to_date');
        $end -> setAttribs(array('placeholder' => $view->translate('Creation Date (To)')));
        $end -> setAllowEmpty(true);
        $end -> addValidator($date_validate);
        $this -> addElement($end);

        // Element Status
        $this->addElement('Select', 'status', array(
            'placeholder' => 'Status',
            'multiOptions' => array(
                '1' => 'Enable',
                '0' => 'Disable',
            ),
        ));

        // Element Submit
        $this->addElement('Button', 'search', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));

        $this->search->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));

//        $this->loadDefaultDecorators();
    }

    public function populateCategoryElement()
    {
        $table = Engine_Api::_()->getDbTable('categories', 'yndynamicform');
        $categories = $table->getCategories();
        unset($categories[0]);
        foreach ($categories as $item)
        {
            $this->category_id->addMultiOption($item['category_id'], str_repeat('--', $item[level] - 1) . $item['title']);
        }
    }

    public function loadDefaultDecorators()
    {
        if( $this->loadDefaultDecoratorsIsDisabled() )
        {
            return;
        }

        $decorators = $this->getDecorators();
        if( empty($decorators) )
        {
            $this
                ->addDecorator('FormElements')
                ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'form-elements'))
                ->addDecorator('FormMessages', array('placement' => 'PREPEND'))
                ->addDecorator('FormErrors', array('placement' => 'PREPEND'))
                ->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'form-description'))
                ->addDecorator('FormTitle', array('placement' => 'PREPEND', 'tag' => 'h3'))
                ->addDecorator('FormWrapper', array('tag' => 'div'))
                ->addDecorator('FormContainer', array('tag' => 'div'))
                ->addDecorator('Form')
            ; //->addDecorator($decorator);
        }
    }
}