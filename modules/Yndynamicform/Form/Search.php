<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 7/29/2016
 * Time: 6:38 PM
 */
class Yndynamicform_Form_Search extends Engine_Form
{
    public function init()
    {
        $this->addAttribs(array(
                'class' => 'global_form_box',
                'id' => 'filter_form',
                ))->setMethod('GET');

        $view = Zend_Registry::get('Zend_View');

        // Title
        $this->addElement('Text', 'keyword', array(
            'label' => 'Form Search',
            'placeholder' => $view->translate('Enter form name'),
        ));

        // Browse By
        $this->addElement('Select', 'order', array(
            'label' => 'Browse By',
            'multiOptions' => array(
                'title_asc' => 'A-Z',
                'title_desc' => 'Z-A',
                'creation_date' => 'Recent Forms',
                'most_entries' => 'Most Entries',
            ),
            'value' => 'creation_date',
        ));

        // Category
        $this->addElement('Select', 'category_id', array(
            'label' => 'Category',
            'multiOptions' => array(
                'all' => 'All categories',
            )
        ));
        $this->populateCategoryElement();

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
}