<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 7/29/2016
 * Time: 3:38 PM
 */
class Yndynamicform_Form_Admin_NewForm extends Engine_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setTitle('Add New Form');

        $this->addElement('Hidden','id');

        // Form Name - Required
        $this->addElement('Text', 'title',array(
            'label'     => 'Form Title',
            'required'  => true,
            'allowEmpty'=> false,
            'autocomplete' => 'off',
            'filters' => array(
                new Engine_Filter_Censor(),
                'StripTags'
            ),
        ));

        // Form description
        $this->addElement('Textarea', 'description', array(
            'label' => 'Form Description',
            'filters' => array(
                new Engine_Filter_Censor(),
                'StripTags'
            ),
        ));

        // Element Categories   @todo hided the Category as client said to hide
//        $this->addElement('Select', 'category_id', array(
//            'label' => 'Category',
//        ));
//        $this->populateCategoryElement();

        // Form avatar
        $this->addElement('File', 'photo', array(
            'label' => 'Photo',
        ));
        $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

        // Enable Form
        $this->addElement('Checkbox', 'enable', array(
            'label' => 'Enable this form',
            'value' => '1',
            'description' => 'Status',
        ));

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Create',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

//        $this->addElement('Cancel', 'cancel', array(
//            'label' => 'cancel',
//            'link' => true,
//            'prependText' => ' or ',
//            'href' => '',
//            'onClick'=> 'javascript:parent.Smoothbox.close();',
//            'decorators' => array(
//                'ViewHelper'
//            )
//        ));
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            ),
        ));
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