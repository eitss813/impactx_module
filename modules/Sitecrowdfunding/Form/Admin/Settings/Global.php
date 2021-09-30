<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Admin_Settings_Global extends Engine_Form {

    // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
    public $_SHOWELEMENTSBEFOREACTIVATE = array(
        "submit_lsetting", "environment_mode"
    );

    public function init() {

        $this->setTitle('Global Settings')
                ->setDescription('These settings affect all members in your community.')
                ->setName('review_global');

        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $productType = 'sitecrowdfunding';
        // ELEMENT FOR LICENSE KEY
        $this->addElement('Text', $productType . '_lsettings', array(
            'label' => 'Enter License key',
            'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting($productType . '.lsettings'),
        ));

        if (APPLICATION_ENV == 'production') {
            $this->addElement('Checkbox', 'environment_mode', array(
                'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few stores of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
                'description' => 'System Mode',
                'value' => 1,
            ));
        } else {
            $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
        }

        $this->addElement('Button', 'submit_lsetting', array(
            'label' => 'Activate Your Plugin Now',
            'type' => 'submit',
            'ignore' => true
        ));
        $this->addElement('Text', 'sitecrowdfunding_slugsingular', array(
            'label' => 'Projects URL alternate text for "project"',
            'description' => 'Please enter the text below which you want to display in place of "project" in the URLs of this plugin.',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', true, array(3, 16)),
                array('Regex', true, array('/^[a-zA-Z0-9-_]+$/')),
            ),
            'value' => $coreSettings->getSetting('sitecrowdfunding.slugsingular', 'project'),
        ));

        $this->addElement('Text', 'sitecrowdfunding_slugplural', array(
            'label' => 'Projects URL alternate text for "projects"',
            'description' => 'Please enter the text below which you want to display in place of "projects" in the URLs of this plugin.',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', true, array(3, 16)),
                array('Regex', true, array('/^[a-zA-Z0-9-_]+$/')),
            ),
            'value' => $coreSettings->getSetting('sitecrowdfunding.slugplural', 'projects'),
        ));
        $this->addElement('Radio', 'sitecrowdfunding_network', array(
            'label' => 'Browse by Networks',
            'description' => "Do you want to show projects according to viewer's network if he has selected any? (If set to no, all the projects will be shown.)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showDefaultNetwork(this.value)',
            'value' => $coreSettings->getSetting('sitecrowdfunding.network', 0),
        ));

        $this->addElement('Radio', 'sitecrowdfunding_default_show', array(
            'label' => 'Set Only My Networks as Default in Search',
            'description' => 'Do you want to set "Only My Networks" option as default for Show field in the search form widget? (This widget appears on the projects browse and home pages, and enables users to search and filter projects.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showDefaultNetworkType(this.value)',
            'value' => $coreSettings->getSetting('sitecrowdfunding.default.show', 0),
        ));

        $this->addElement('Radio', 'sitecrowdfunding_networks_type', array(
            'label' => 'Network Selection for Projects',
            'description' => "You have chosen that viewers should only see Projects of their network(s). How should a Project's network(s) be decided?",
            'multiOptions' => array(
                0 => "Project owner's network(s). [If selected, only members belonging to project owner's network(s) will see the Projects.]",
                1 => "Selected networks. [If selected, project owner will be able to choose the networks of which members will be able to see their Project.]"
            ),
            'value' => $coreSettings->getSetting('sitecrowdfunding.networks.type', 0),
        ));

        $this->addElement('Radio', 'sitecrowdfunding_networkprofile_privacy', array(
            'label' => 'Network Based Project Viewing',
            'description' => "Do you want to show Projects according to viewer's network if he has selected any? (If set to no, all the projects will be shown.)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            // 'onclick' => 'showviewablewarning(this.value);',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.networkprofile.privacy', 0),
        ));
        $this->addElement('Radio', 'sitecrowdfunding_location', array(
            'label' => 'Location Field',
            'description' => 'Do you want the Location field to be enabled for Projects?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onClick' => 'showLocationSettings(this.value)',
            'value' => $coreSettings->getSetting('sitecrowdfunding.location', 1),
        ));
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $this->addElement('Dummy', "sitecrowdfunding_seaocore", array(
            'label' => 'Default Location for Searching Projects',
            'description' => "We have transferred some location related settings to other section. Please <a target='_blank' href='" . $view->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'map'), 'admin_default', true) . "'>click here</a> to change the settings.",
            'ignore' => true,
        ));
        $this->sitecrowdfunding_seaocore->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));

        $this->addElement('Radio', 'sitecrowdfunding_proximity_search_kilometer', array(
            'label' => 'Proximity Search',
            'description' => 'Select the unit of the distance you want to be available for the proximity search of Projects? (Proximity search will enable users to search for projects within a certain distance from a location.)',
            'multiOptions' => array(
                0 => 'Miles',
                1 => 'Kilometers'
            ),
            'value' => $coreSettings->getSetting('sitecrowdfunding.proximity.search.kilometer', 0),
        ));

        $this->addElement('Radio', 'sitecrowdfunding_map_sponsored', array(
            'label' => 'Sponsored Projects with a Bouncing Animation',
            'description' => 'Do you want the Sponsored Projects to be shown with a bouncing animation in the Map?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitecrowdfunding.map.sponsored', 1),
        ));

        $this->addElement('Dummy', 'otherHeading', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formElementsHeading.tpl',
                        'heading' => 'Other Settings',
                        'class' => 'form element'
                    ))),
        ));



        $this->addElement('Dummy', 'sitecrowdfunding_calender_format', array(
            'label' => 'Calendar Format',
            'description' => 'Please <a target=\'_blank\' href=\'admin/seaocore/settings\'>click here</a> to select a format for the calendar.',
        ));
        $this->getElement('sitecrowdfunding_calender_format')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));

        $this->addElement('Radio', 'sitecrowdfunding_tinymceditor', array(
            'label' => 'TinyMCE for Discussion',
            'description' => 'Do you want to allow TinyMCE for discussion message of Projects?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitecrowdfunding.tinymceditor', 1),
        ));

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = $coreSettings->getSetting('payment.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
        $this->addElement('Dummy', 'sitecrowdfunding_currency', array(
            'label' => 'Currency',
            'description' => "<b>" . $currencyName . "</b> <br class='clear' /> <a href='" . $view->url(array('module' => 'payment', 'controller' => 'settings'), 'admin_default', true) . "' target='_blank'>" . Zend_Registry::get('Zend_Translate')->_('edit currency') . "</a>",
        ));
        $this->getElement('sitecrowdfunding_currency')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));


        $this->addElement('Text', 'sitecrowdfunding_sponsoredcolor', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formImagerainbowSponsred.tpl',
                        'class' => 'form element'
                    )))
        ));
        $this->addElement('Text', 'sitecrowdfunding_featuredcolor', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formImagerainbowFeatured.tpl',
                        'class' => 'form element'
                    )))
        ));
        $this->addElement('Text', 'sitecrowdfunding_fundedcirclecolor', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formImagerainbowfunded.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Select', 'sitecrowdfunding_datetime_format', array(
            'label' => 'Default Date & Time Format',
            'description' => 'Choose default Date & Time format for the Projects on your site. [Note: These Date & Time format depends on the locale setting.]',
            'multiOptions' => array(
                'full' => 'Full (EEEE, MMMM d, y h:mm a zzzz) ',
                'long' => 'Long (MMMM d, y h:mm a z) ',
                'medium' => 'Medium (MMM d, y h:mm a) ',
                'short' => 'Short (M/d/yy h:mm a)'
            ),
            'onchange' => 'showTimezoneSetting(this.value)',
            'value' => $coreSettings->getSetting('sitecrowdfunding.datetime.format', 'medium'),
        ));

        $this->addElement('Radio', 'sitecrowdfunding_timezone', array(
            'label' => 'Display Timezone',
            'description' => 'Do you want to display Timezone along with the date and time?',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
            'value' => $coreSettings->getSetting('sitecrowdfunding.timezone', 1),
        ));

        $this->addElement('Text', 'sitecrowdfunding_navigationtabs', array(
            'label' => 'Tabs in Projects Navigation Bar',
            'allowEmpty' => false,
            'maxlength' => '2',
            'required' => true,
            'description' => 'How many tabs do you want to show on Projects main navigation bar by default? (Note: If number of tabs exceeds the limit entered by you then a "More" tab will appear, clicking on which will show the remaining hidden tabs. To choose the tab to be shown in this navigation menu, and to set their sequence, please visit: "Layout" > "Menu Editor")',
            'value' => $coreSettings->getSetting('sitecrowdfunding.navigationtabs', 6),
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));
        $this->addElement('Button', 'save', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
