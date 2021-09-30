<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/31/2016
 * Time: 2:00 PM
 */
class Yndynamicform_Form_Admin_Import extends Engine_Form
{
    public function init()
    {
        // Init form
        $this
            ->setAttrib('name', 'yndform_import')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
        ;

        // Init path
        $this->addElement('File', 'file_import', array(
            'label' => 'Select File',
            'required' => true,
            'description' => 'Choose a file XML to import.'
        ));
        $this->file_import->addValidator('Extension', false, 'xml');

        $this->addElement('Button', 'submit', array(
            'label' => 'Import',
            'type'  => 'submit',
        ));
    }
}