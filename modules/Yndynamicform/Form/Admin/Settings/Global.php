<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 8/10/2016
 * Time: 5:54 PM
 */
class Yndynamicform_Form_Admin_Settings_Global extends Engine_Form
{
    public function init()
    {
        $this -> setTitle('Global Settings');
        $this -> setDescription('These settings affect all members in your community.');

        $this -> addElement('Text', 'yndynamicform_google_api_key', array(
            'label' => 'Google API Key',
            'description' => 'Please refer to this guide to get Google API Key: 
                            <a href="https://developers.google.com/places/web-service/get-api-key" />https://developers.google.com/places/web-service/get-api-key</a>',
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yndynamicform.google.api.key', 'AIzaSyB3LowZcG12R1nclRd9NrwRgIxZNxLMjgc'),
        ));
        $this->yndynamicform_google_api_key->getDecorator('Description')->setEscape(false);

        $this -> addElement('Text', 'yndynamicform_number_entries_per_page', array(
            'label' => 'Number of entries per page',
            'description' => 'Number of entries will be displayed in "My Entries" or "View Entries"?' ,
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yndynamicform.number.entries.per.page', 10),
        ));

        // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }
}