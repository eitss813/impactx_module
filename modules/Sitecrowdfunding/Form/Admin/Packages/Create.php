<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Admin_Packages_Create extends Engine_Form {

    public function init() {

        $this->setTitle('Create Project Package')
                ->setDescription('Create a new project package over here. Below, you can configure various settings for this package like videos, overview, etc. Please note that payment parameters (Price, Duration) cannot be edited after creation. If you wish to change these, you will have to create a new package and disable the existing one.');

        // Element: title
        $this->addElement('Text', 'title', array(
            'label' => 'Package Name',
            'required' => true,
            'allowEmpty' => false,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '128')),
            ),
        ));

        // Element: description
        $this->addElement('Textarea', 'description', array(
            'label' => 'Package Description',
            'validators' => array(
                array('StringLength', true, array(0, 150)),
            )
        ));

        // Element: level_id
        $multiOptions = array('0' => 'All Levels');
        foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
            if ($level->type == 'public') {
                continue;
            }
            $multiOptions[$level->getIdentity()] = $level->getTitle();
        }
        $this->addElement('Multiselect', 'level_id', array(
            'label' => 'Member Levels',
            'description' => 'Select the Member Levels to which this Package should be available. Only users belonging to the selected Member Levels will be able to create project of this package.',
            'attribs' => array('style' => 'max-height:100px; '),
            'multiOptions' => $multiOptions,
            'value' => array('0')
        ));


        // Element: price
        $this->addElement('Text', 'price', array(
            'label' => 'Price',
            'description' => 'The amount to charge from the project owner. Setting this to zero will make this a free package.',
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('Float', true),
                new Engine_Validate_AtLeast(0),
            ),
            'value' => '0.00',
        ));

        // Element: recurrence @ todo

        $this->addElement('Duration', 'recurrence', array(
            'label' => 'Billing Cycle',
            'description' => 'How often should Projects of this package be billed? (You can choose the payment for this package to be one-time or recurring.)',
            'required' => true,
            'allowEmpty' => false,
            'value' => array(1, 'month'),
        ));

        // Element: duration
        $this->addElement('Duration', 'duration', array(
            'label' => 'Billing Duration',
            'description' => 'When should this package expire? For one-time packages, the package will expire after the period of time set here. For recurring plans, the user will be billed at the above billing cycle for the period of time specified here.',
            'required' => true,
            'allowEmpty' => false,
            'value' => array('0', 'forever'),
        ));

        // renew
        $this->addElement('Checkbox', 'renew', array(
            'description' => 'Renew Link',
            'label' => 'Project creators will be able to renew their projects of this package before expiry. (Note: Renewal link after expiry will only be shown for projects of paid packages, i.e., packages having a non-zero value of Price above.)',
            'value' => 0,
            'onclick' => 'javascript:setRenewBefore();',
        ));

        $this->addElement('Text', 'renew_before', array(
            'label' => 'Renewal Frame before Project Expiry',
            'description' => 'Show project renewal link these many days before expiry.',
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('Int', true),
                new Engine_Validate_AtLeast(0),
            ),
            'value' => '0',
        ));

        // auto aprove
        $this->addElement('Checkbox', 'approved', array(
            'description' => "Auto-Approve",
            'label' => 'Auto-Approve projects of this package. These projects will not need admin moderation approval before going live.',
            'value' => 0,
        ));

        // Element:sponsored
        $this->addElement('Checkbox', 'sponsored', array(
            'description' => "Sponsored",
            'label' => 'Make projects of this package as Sponsored. (Note: A change in this setting later on will only apply on new projects that are created in this package.)',
            'value' => 0,
        ));

        // Element:featured
        $this->addElement('Checkbox', 'featured', array(
            'description' => "Featured",
            'label' => 'Make projects of this package as Featured. (Note: A change in this setting later on will only apply on new projects that are created in this package.)',
            'value' => 0,
        ));

        // Element: overview
        $this->addElement('Checkbox', 'overview', array(
            'description' => "Overview",
            'label' => 'Enable Overview for projects of this package. (Using this, users will be able to create rich profiles for their projects using WYSIWYG editor.)',
            'value' => 0,
        ));

        // Element : video
        $this->addElement('Radio', 'video', array(
            'label' => 'Videos',
            'description' => "Enable Videos for projects of this package. (Note: This setting will only work if 'SEAO - Advanced Videos / Channels / Playlists' is installed and integrated in your website.)",
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
            'value' => 1,
            'onclick' => 'showVideoOption(this.value)',
        ));


        // Element : video_count
        $this->addElement('text', 'video_count', array(
            'label' => 'Maximum Allowed Videos',
            'value' => 10,
            'description' => 'Please enter the number of videos which you want to be uploaded in the projects of this package. (Note: Enter 0 for unlimited videos.)'
        ));

        // Element : photo
        $this->addElement('Radio', 'photo', array(
            'label' => 'Photos',
            'description' => 'Enable Photos for projects of this package.',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
            'value' => 1,
            'onclick' => 'showPhotoOption(this.value)',
        ));

        // Element : photo_count
        $this->addElement('text', 'photo_count', array(
            'label' => 'Maximum Allowed Photos',
            'value' => 10,
            'description' => 'Please enter the number of photos which you want to be uploaded in the projects of this package. (Note: Enter 0 for unlimited photos.)'
        ));
        $this->addElement('Radio', 'lifetime', array(
            'label' => 'Project Duration (Life Time)',
            'description' => "Do you want to let members create projects with life time duration under this package? [Note: Limit of life time duration is 5 years.]",
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
            'value' => 1,
        ));

        $this->addElement('Checkbox', 'update_list', array(
            'description' => 'Show in "Other available Packages" List',
            'label' => "Show this package in the list of 'Other available Packages' which gets displayed to the users for upgrading the package of a Project at Project dashboard. (This will be useful in case you are creating a free package or a test package and you want it to be used by the users only once for a limited period of time and do not want to show it during package upgrdation.)",
            'value' => 1,
        ));


        $this->addElement('Select', 'commission_handling', array(
            'label' => 'Commission Type',
            'description' => 'Select the type of commission. This commission will be applied on all the projects created after selection of this package.',
            'multiOptions' => array(
                1 => 'Percent',
                0 => 'Fixed'
            ),
            'value' => 1,
            'onchange' => 'showcommissionType();'
        ));

        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
        $this->addElement('Text', 'commission_fee', array(
            'label' => 'Commission Value (' . $currencyName . ')',
            'description' => 'Enter the value of the commission. (If you do not want to apply any commission, then simply enter 0.)',
            'allowEmpty' => false,
            'value' => 1,
        ));

        $this->addElement('Text', 'commission_rate', array(
            'label' => 'Commission Value (%)',
            'description' => 'Enter the value of the commission. (Do not add any symbol. For 10% commission, enter commission value as 10. You can only enter commission percentage between 0 and 100.)',
            'validators' => array(
                array('Between', true, array('min' => 0, 'max' => 100, 'inclusive' => true)),
            ),
            'value' => 1,
        ));

        // Element: enabled
        $this->addElement('hidden', 'enabled', array(
            'value' => 1,
        ));

        // Element: execute
        $this->addElement('Button', 'execute', array(
            'label' => 'Create Package',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
        ));

        // Element: cancel
        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'prependText' => ' or ',
            'ignore' => true,
            'link' => true,
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index', 'package_id' => null)),
            'decorators' => array('ViewHelper'),
        ));

        // DisplayGroup: buttons
        $this->addDisplayGroup(array('execute', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            )
        ));
    }

}
