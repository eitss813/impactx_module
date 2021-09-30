<?php

class YounetCore_Form_Admin_Settings_Global extends Engine_Form
{
    public function init()
    {
        $this -> setTitle('Global Settings');
        $this -> setDescription('These settings affect all members in your community.');

        // Google API Key
        $this -> addElement('Text', 'yncore_google_api_key', array(
            'label' => 'Google API Key',
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.google.api.key', 'AIzaSyB3LowZcG12R1nclRd9NrwRgIxZNxLMjgc'),
            'description' => 'Please refer to this guide to get Google API Key: <a href = "https://developers.google.com/places/web-service/get-api-key">https://developers.google.com/places/web-service/get-api-key</a>'
        ));
        $this -> yncore_google_api_key ->getDecorator('Description')->setOption('escape', false);
        $this -> yncore_google_api_key->getDecorator('Description')->setOption('placement', 'APPEND');

        // AddThis public ID
        $this -> addElement('Text', 'yncore_addthis_pub', array(
            'label' => 'AddThis - Public ID',
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.pub', 'younet'),
            'description' => 'Please refer to this guide to get AddThis Public ID: <a href = "https://www.addthis.com/academy/using-profiles-in-your-addthis-account">https://www.addthis.com/academy/using-profiles-in-your-addthis-account</a>'
        ));
        $this -> yncore_addthis_pub ->getDecorator('Description')->setOption('escape', false);
        $this -> yncore_addthis_pub->getDecorator('Description')->setOption('placement', 'APPEND');

        // Addthis HTML code
        $this -> addElement('Textarea', 'yncore_addthis_buttons', array(
            'label' => 'AddThis - Inline Share Buttons',
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.buttons', '<!-- Go to www.addthis.com/dashboard to customize your tools --> <div class="addthis_sharing_toolbox"></div>'),
            'description' => 'Please go to this link to customize your tools: <a href = "https://www.addthis.com/dashboard#gallery">https://www.addthis.com/dashboard#gallery</a>'
        ));
        $this -> yncore_addthis_buttons ->getDecorator('Description')->setOption('escape', false);
        $this -> yncore_addthis_buttons->getDecorator('Description')->setOption('placement', 'APPEND');

        // Unit measure
        $this -> addElement('Select', 'yncore_unit_measure', array(
            'label' => 'Unit Measure',
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.unit.measure', 'mi'),
            'description' => 'Please choose unit measure will be used in your website',
            'multiOptions' => array(
                'km' => 'Kilometer (km)',
                'mi' => 'Mile (mi)'
            ),
        ));
        $this -> yncore_unit_measure ->getDecorator('Description')->setOption('escape', false);
        $this -> yncore_unit_measure->getDecorator('Description')->setOption('placement', 'APPEND');

        // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }
}