<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/1/2016
 * Time: 2:31 PM
 */
class Yndynamicform_Form_Admin_Widget_SingleForm extends Engine_Form
{
    /**
     * @inheritDoc
     */
    public function init()
    {
//        $this->setTitle(' ')
        $this->setMethod('post');
        $this->setTitle('YNC - Dynamic Form - Single Form');
        $this->setDescription('Display one selected form');

        // Form Name - Required
        $this->addElement('Text', 'title',array(
            'label'     => 'Title',
            'autocomplete' => 'off',
        ));

        // Form Name - Required
        $this->addElement('Select', 'form_id',array(
            'label'     => 'Form to be displayed',
            'required'  => true,
            'allowEmpty'=> false,
            'autocomplete' => 'off',
        ));
        $this->populateFormElement();

        // Enable Form on mobile
        $this->addElement('Select', 'nomobile', array(
            'label' => 'Hide on mobile site?',
            'multiOptions' => array(
                '1' => 'Yes, do not display on mobile site.',
                '0' => 'No, display on mobile site.'
            ),
            'value' => '0',
        ));

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Save',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onClick'=> 'javascript:parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    }

    public function populateFormElement()
    {
        $table = Engine_Api::_()->getDbTable('forms', 'yndynamicform');
        $select = $table->getFormsSelect(array('order' => 'title_asc'));
        $forms = $table->fetchAll($select);
        foreach ($forms as $item)
        {
            $this->form_id->addMultiOption($item['form_id'], $item['title']);
        }
    }
}
?>
