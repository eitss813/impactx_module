<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Form_Album_Edit extends Engine_Form {

  protected $_defaultProfileId;
  protected $_item;

  public function getDefaultProfileId() {
    return $this->_defaultProfileId;
  }

  public function setDefaultProfileId($default_profile_id) {
    $this->_defaultProfileId = $default_profile_id;
    return $this;
  }

  public function getItem() {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item) {
    $this->_item = $item;
    return $this;
  }

  public function init() {
    $user = Engine_Api::_()->user()->getViewer();

    $this->setTitle('Edit Album Settings')
            ->setAttrib('name', 'albums_edit');

    $this->addElement('Text', 'title', array(
        'label' => 'Album Title',
        'required' => true,
        'notEmpty' => true,
        'validators' => array(
            'NotEmpty',
        ),
        'filters' => array(
            new Engine_Filter_Censor(),
            //new Engine_Filter_HtmlSpecialChars(),
            'StripTags',
            new Engine_Filter_StringLength(array('max' => '63'))
        )
    ));
    $this->title->getValidator('NotEmpty')->setMessage("Please specify an album title");

    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.category.enabled', 1)) {
      // prepare categories
      $categories = Engine_Api::_()->getDbtable('categories', 'sitealbum')->getCategories(array('fetchColumns' => array('category_id', 'category_name'), 'sponsored' => 0, 'cat_depandancy' => 1));
      if (count($categories) != 0) {
        $categories_prepared[0] = "";
        foreach ($categories as $category) {
          $categories_prepared[$category->category_id] = $category->category_name;
        }
      }
      
      if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {       
           $onChangeEvent = "showFields(this.value, 1); subcategories(this.value, '', '');";
           $categoryFiles = 'application/modules/Sitealbum/views/scripts/_formSubcategory.tpl';
      } else {
           $onChangeEvent = "showSMFields(this.value, 1);sm4.core.category.set(this.value, 'subcategory');";
           $categoryFiles = 'application/modules/Sitealbum/views/sitemobile/scripts/_subCategory.tpl';
      }
      if (count($categories) > 0) {
        $this->addElement('Select', 'category_id', array(
            'label' => 'Category',
            'multiOptions' => $categories_prepared,
            'onchange' => $onChangeEvent,
        ));
      }

      $this->addElement('Select', 'subcategory_id', array(
          'RegisterInArrayValidator' => false,
          'allowEmpty' => true,
          'required' => false,
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => $categoryFiles,
                      'class' => 'form element')))
      ));
    }

    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.category.enabled', 1)) {
      $defaultProfileId = "0_0_" . $this->getDefaultProfileId();

      $customFields = new Sitealbum_Form_Custom_Standard(array(
          'item' => $this->_item,
          'decorators' => array(
              'FormElements'
      )));

      $customFields->removeElement('submit');
      if ($customFields->getElement($defaultProfileId)) {
        $customFields->getElement($defaultProfileId)
                ->clearValidators()
                ->setRequired(false)
                ->setAllowEmpty(true);
      }

      $this->addSubForms(array(
          'fields' => $customFields
      ));
    }
    $this->addElement('Textarea', 'description', array(
        'label' => 'Album Description',
        'rows' => 2,
        'filters' => array(
            new Engine_Filter_Censor(),
            'StripTags',
            //new Engine_Filter_HtmlSpecialChars(),
            new Engine_Filter_EnableLinks(),
        )
    ));

    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.tags.enabled', 0)) {
      $this->addElement('Text', 'tags', array(
          'label' => 'Tags (Keywords)',
          'autocomplete' => 'off',
          'description' => Zend_Registry::get('Zend_Translate')->_('Separate tags with commas.'),
          'filters' => array(
                            'StripTags',
                             new Engine_Filter_Censor(),
                         ),
      ));
      $this->tags->getDecorator("Description")->setOption("placement", "append");
    }

    //NETWORK BASE ALBUM
    if (Engine_Api::_()->sitealbum()->albumBaseNetworkEnable()) {
      // Make Network List
      $table = Engine_Api::_()->getDbtable('networks', 'network');
      $select = $table->select()
              ->from($table->info('name'), array('network_id', 'title'))
              ->order('title');
      $result = $table->fetchAll($select);

      $networksOptions = array('0' => 'Everyone');
      foreach ($result as $value) {
        $networksOptions[$value->network_id] = $value->title;
      }
      if (count($networksOptions) > 0) {
        $viewPricavyEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.networkprofile.privacy', 0);
        if ($viewPricavyEnable) {
          $desc = 'Select the networks, members of which should be able to see your album. (Press Ctrl and click to select multiple networks. Applied privacy will be a combination of the privacy chosen above in "View Privacy" and the privacy chosen here.)';
        } else {
          $desc = 'Select the networks, members of which should be able to see your Album in browse and search albums. (Press Ctrl and click to select multiple networks. Applied privacy will be a combination of the privacy chosen above in "View Privacy" and the privacy chosen here.)';
        }
        $this->addElement('Multiselect', 'networks_privacy', array(
            'label' => 'Networks Selection',
            'description' => $desc,
            'multiOptions' => $networksOptions,
            'value' => array(0)
        ));
      }
    }

    // View
    $availableLabels = array(
        'everyone' => 'Everyone',
        'registered' => 'All Registered Members',
        'owner_network' => 'Friends and Networks',
        'owner_member_member' => 'Friends of Friends',
        'owner_member' => 'Friends Only',
        'owner' => 'Just Me'
    );

    // Element: auth_view
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $user, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

    if (!empty($viewOptions) && count($viewOptions) >= 1) {
      // Make a hidden field
      if (count($viewOptions) == 1) {
        $this->addElement('hidden', 'auth_view', array('order' => 869,'value' => key($viewOptions)));
        // Make select box
      } else {
        $this->addElement('Select', 'auth_view', array(
            'label' => 'Privacy',
            'description' => 'Who may see this album?',
            'multiOptions' => $viewOptions,
            'value' => key($viewOptions),
        ));
        $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
      }
    }
    
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $allowPasswordProtected = Engine_Api::_()->authorization()->getPermission($level_id, 'album', 'album_password_protected');
        if ($allowPasswordProtected) {
            // Element: password
            $this->addElement('Text', 'password', array(
                'label' => 'Password',
                'description' => "Protect this Photo Album with a password. [Leave it blank if you do not want password protection on this photo album.]",
                'required' => false,
                'allowEmpty' => true,
                'validators' => array(
                    array('NotEmpty', true),
                    array('StringLength', false, array(6, 32)),
                )
            ));
            $this->password->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
        }    

    // Element: auth_comment
    $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $user, 'auth_comment');
    $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

    if (!empty($commentOptions) && count($commentOptions) >= 1) {
      // Make a hidden field
      if (count($commentOptions) == 1) {
        $this->addElement('hidden', 'auth_comment', array('order' => 870,'value' => key($commentOptions)));
        // Make select box
      } else {
        $this->addElement('Select', 'auth_comment', array(
            'label' => 'Comment Privacy',
            'description' => 'Who may post comments on this album?',
            'multiOptions' => $commentOptions,
            'value' => key($commentOptions),
        ));
        $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    // Element: auth_tag
    $tagOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $user, 'auth_tag');
    $tagOptions = array_intersect_key($availableLabels, array_flip($tagOptions));

    if (!empty($tagOptions) && count($tagOptions) >= 1) {
      // Make a hidden field
      if (count($tagOptions) == 1) {
        $this->addElement('hidden', 'auth_tag', array('order' => 871,'value' => key($tagOptions)));
        // Make select box
      } else {
        $this->addElement('Select', 'auth_tag', array(
            'label' => 'Tagging',
            'description' => 'Who may tag photos in this album?',
            'multiOptions' => $tagOptions,
            'value' => key($tagOptions),
        ));
        $this->auth_tag->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    $this->addElement('Checkbox', 'search', array(
        'label' => "Show this album in search results",
    ));

    // Submit or succumb!
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper'
        )
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'decorators' => array(
            'ViewHelper'
        )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }

}
