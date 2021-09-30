<?php

 /**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblogpackage
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: Create.php 2020-03-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

class Sesblogpackage_Form_Admin_Package_Create extends Engine_Form {

  protected $_customFields;

  function getCustomFields($customFields) {
    return $this->_customFields;
  }

  public function setCustomFields($customFields) {
    return $this->_customFields = $customFields;
  }

  public function init() {
    $this
            ->setTitle('Create New Package')
            ->setDescription('Here, you can create a new package for blogs on your website. Make sure to enter correct information in this package as it can be deleted until someone has not created any blog under this package. Also, once this package is created, only the fields Description, Member Levels, Custom Fields, Highlight & Show in Upgrade, can be edited.');
    // Element: title
    $this->addElement('Text', 'title', array(
        'label' => 'Package Title',
        'required' => true,
        'allowEmpty' => false,
        'filters' => array(
            'StringTrim',
        ),
    ));
    // Element: title
    $this->addElement('Text', 'item_count', array(
        'label' => 'Blogs Count',
        'description' => 'Enter the maximum number of Blogs a member can create in this package. The field must contain an integer, use zero for unlimited. ',
        'required' => true,
        'allowEmpty' => false,
        'filters' => array(
            'StringTrim',
        ),
        'validators' => array(
            new Engine_Validate_AtLeast(0),
        ),
        'value' => '0',
    ));
    // Element: description
    $this->addElement('Textarea', 'description', array(
        'label' => 'Package Description',
        'description' => 'Enter the description for this package. This will be shown in the blog of packages on your website.',
        'validators' => array(
            array('StringLength', true, array(0, 250)),
        )
    ));
    // Element: level_id
    foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
      if ($level->type == 'public') {
        continue;
      }
      $multiOptions[$level->getIdentity()] = $level->getTitle();
    }
    $multiOptions = array_merge(array('0' => 'All Levels'), $multiOptions);
    $this->addElement('Multiselect', 'member_level', array(
        'label' => 'Member Level',
        'description' => 'Only the selected member levels will be allowed to view and purchase this package for creating blogs on your site.',
        'multiOptions' => $multiOptions,
        'value' => '0'
    ));
    // Element: price
    $this->addElement('Text', 'price', array(
        'label' => 'Price',
        'description' => 'Enter the amount to be charged from users for creating blogs under this package. This amount will be charged only once for one-time plans and at each billing cycle for recurring plans. Use ‘0’ to make this package Free.',
        'required' => true,
        'allowEmpty' => false,
        'validators' => array(
            array('Float', true),
            new Engine_Validate_AtLeast(0),
        ),
        'value' => '0.00',
    ));
    // Element: recurrence
    $this->addElement('Duration', 'recurrence', array(
        'label' => 'Billing Cycle',
        'description' => 'How often should members in this package be billed?',
        'required' => true,
        'allowEmpty' => false,
        'value' => array(1, 'month'),
    ));
    // Element: duration
    $this->addElement('Duration', 'duration', array(
        'label' => 'Billing Duration',
        'description' => 'When should this package expire? For one-time package, the package will expire after the period of time set here. For recurring plans, the user will be billed at the above billing cycle for the period of time specified here.',
        'required' => true,
        'allowEmpty' => false,
        'value' => array('0', 'forever'),
    ));
    // renew
    $this->addElement('Select', 'is_renew_link', array(
        'description' => 'Renew Link',
        'label' => 'Want to show renew link',
        'value' => 0,
        'multiOptions' => array('1' => 'Yes, show renew link', '0' => 'No, don\'t show renew link'),
        'onchange' => 'showRenewData(this.value);',
    ));
    $this->addElement('Text', 'renew_link_days', array(
        'label' => 'Days before show renew link',
        'description' => 'Show renewal link before how many days before expiry.',
        'required' => true,
        'allowEmpty' => false,
        'validators' => array(
            array('Int', true),
            new Engine_Validate_AtLeast(0),
        ),
        'value' => '0',
    ));

    $this->addElement('Radio', 'upload_mainphoto', array(
        'label' => 'Allow to Upload Blog Main Photo',
        'description' => 'Do you want to allow the users to upload main photo for their blogs created under this package? If set to No, then the default main photo will get displayed instead.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No',
        ),
        'value' => 1,
    ));

//     $this->addElement('Radio', 'blog_choose_style', array(
//         'label' => 'Enable Blog Profile Designs Selection',
//         'description' => 'Do you want to enable users to choose designs for their Blog Profiles for blogs created under this package? (If you choose No, then you can choose a default layout for the Blog Profile pages on your website.)',
//         'multiOptions' => array(
//             1 => 'Yes',
//             0 => 'No',
//         ),
//         'value' => 1,
//     ));
// 
//     $this->addElement('MultiCheckbox', 'blog_chooselayout', array(
//         'label' => 'Choose Blog Profile Design',
//         'description' => 'Choose design for the blog profile pages which will be available to users while creating or editing their blogs.',
//         'multiOptions' => array(
//             1 => 'Template 1',
//             2 => 'Template 2',
//             3 => 'Template 3',
//             4 => 'Template 4',
//         ),
//         'value' => array('1', '2', '3', '4'),
//     ));
// 
//     // Element: auth_view
//     $this->addElement('Radio', 'blog_style_type', array(
//         'label' => 'Default Blog Profile Design',
//         'description' => 'Choose default design for the blog profile page of the blogs created under this package.',
//         'multiOptions' => array(
//             '1' => 'Template 1',
//             '2' => 'Template 2',
//             '3' => 'Template 3',
//             '4' => 'Template 4'
//         )
//     ));
//     
    //New File System Code
    $default_photos = array('' => '');
    $files = Engine_Api::_()->getDbTable('files', 'core')->getFiles(array('fetchAll' => 1, 'extension' => array('gif', 'jpg', 'jpeg', 'png')));
    foreach( $files as $file ) {
      $default_photos[$file->storage_path] = $file->name;
    }
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $fileLink = $view->baseUrl() . '/admin/files/';
    
    $this->addElement('Radio', 'blog_approve', array(
        'description' => 'Do you want blogs created under this package to be auto-approved?',
        'label' => 'Auto Approve Blogs',
        'multiOptions' => array(
            1 => 'Yes, auto-approve blogs.',
            0 => 'No, do not auto-approve blogs.'
        ),
        'value' => 1,
    ));

    $information = array('featured' => 'Featured', 'sponsored' => 'Sponsored', 'verified' => 'Verified', 'hot' => 'Hot', 'custom_fields' => 'Custom Fields');
    $showinfo = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblogpackage.package.info', array_keys($information));
    if (in_array('featured', $showinfo)) {
      //element for blog featured
      $this->addElement('Radio', 'blog_featured', array(
          'description' => 'Do you want blogs created under this package to be automatically marked as Featured?',
          'label' => 'Automatically Mark Blogs as Featured',
          'multiOptions' => array(
              1 => 'Yes, automatically mark blogs as Featured',
              0 => 'No, do not automatically mark blogs as Featured',
          ),
          'value' => 0,
      ));
    }
    if (in_array('sponsored', $showinfo)) {
      //element for blog sponsored
      $this->addElement('Radio', 'blog_sponsored', array(
          'description' => 'Do you want blogs created under this package to be automatically marked as Sponsored?',
          'label' => 'Automatically Mark Blogs as Sponsored',
          'multiOptions' => array(
              1 => 'Yes, automatically mark blogs as Sponsored',
              0 => 'No, do not automatically mark blogs as Sponsored',
          ),
          'value' => 0,
      ));
    }
    if (in_array('verified', $showinfo)) {
      //element for blog verified
      $this->addElement('Radio', 'blog_verified', array(
          'description' => 'Do you want blogs created under this package to be automatically marked as Verified?',
          'label' => 'Automatically Mark Blogs as Verified',
          'multiOptions' => array(
              1 => 'Yes, automatically mark blogs as Verified',
              0 => 'No, do not automatically mark blogs as Verified',
          ),
          'value' => 0,
      ));
    }

    $this->addElement('Radio', 'blog_seo', array(
        'description' => 'Do you want to enable the "SEO" fields for the blogs created under this package?',
        'label' => 'Enable SEO Fields',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No',
        ),
        'value' => 1,
    ));

    $this->addElement('Radio', 'blog_contactinfo', array(
        'description' => 'Do you want to enable the "Contact Info" for the blogs created under this package?',
        'label' => 'Enable Contact Info',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No',
        ),
        'value' => 1,
    ));

    if (in_array('custom_fields', $showinfo)) {
      $this->addElement('Radio', 'custom_fields', array(
          'label' => 'Allow Custom Fields',
          'description' => 'Do you want to allow users to fill custom fields in the blogs created under this Package?',
          'value' => 0,
          'onclick' => 'customField(this.value)',
          'multiOptions' => array(
              1 => 'Yes, allow to fill all available custom fields.',
              0 => 'No, do not allow to fill custom fields.',
              2 => 'Yes, allow to fill selected custom fields only.',
          ),
          'value' => 1
      ));
      $this->addElement('Dummy', 'customfields', array(
          'ignore' => true,
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => '_customFields.tpl',
                      'class' => 'form element',
                      'customFields' => $this->_customFields,
                  )))
      ));
    }

    // Element: highlight
    $this->addElement('Select', 'highlight', array(
        'label' => 'Highlight Package',
        'description' => 'Do you want to highlight this package among all other packages on your website. If you choose yes, then this package will be shown with some highlight effect to users.',
        'multiOptions' => array(
            '1' => 'Yes, want to highlight this package.',
            '0' => 'No, don\'t want to highlight this package.',
        ),
        'value' => 0,
    ));
    // Element: highlight
    $this->addElement('Select', 'show_upgrade', array(
        'label' => 'Show In Upgrade?',
        'description' => 'Do you want to show this package in Upgrade Package section in the dashboard of the blogs? This package will show only for the blogs which are created under the package of lower price than this package.',
        'multiOptions' => array(
            '1' => 'Yes, want to show this package in upgrade section.',
            '0' => 'No, don\'t want to show this package in upgrade section.',
        ),
        'value' => 0,
    ));
    // Element: enabled
    $this->addElement('Select', 'enabled', array(
        'label' => 'Enabled?',
        'description' => 'Do you want to enable this package? (The existing members that were in that plan would stay in that plan until they pick another plan.)',
        'multiOptions' => array(
            '1' => 'Yes, users may select this plan.',
            '0' => 'No, users may not select this plan.',
        ),
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
        'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index')),
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
