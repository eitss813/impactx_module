<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_Album_Edit extends Engine_Form {

  public function init() {

    $this->setTitle('Edit Album')
            ->setAttrib('name', 'albums_edit');

    $this->addElement('Text', 'title', array(
        'label' => 'Album Title',
        'maxlength' => '256',
        'required' => true,
        'notEmpty' => true,
        'validators' => array(
            'NotEmpty',
        ),
        'filters' => array(
            new Engine_Filter_Censor(),
            'StripTags',
            new Engine_Filter_StringLength(array('max' => '256'))
        )
    ));
    $this->title->getValidator('NotEmpty')->setMessage("Please specify an album title.");
    
    // Privacy
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manageadmin', 1))
      $ownerTitle = "Page Admins";
    else
      $ownerTitle="Just Me";
      
    $user = Engine_Api::_()->user()->getViewer();
//     $availableLabels = array(
//         'registered' => 'All Registered Members',
//         'owner_network' => 'Friends and Networks',
//         'owner_member_member' => 'Friends of Friends',
//         'owner_member' => 'Friends Only',
//         'owner' => $ownerTitle
//     );
    
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id');
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    $allowMemberInLevel = Engine_Api::_()->authorization()->getPermission($user_level, 'sitepage_page', 'smecreate');
    $allowMemberInthisPackage = false;
    $allowMemberInthisPackage = Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagemember");
    $sitepageMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
    
		$availableLabels = array(
			'registered' => 'All Registered Members',
			'owner_network' => 'Friends and Networks',
			'owner_member_member' => 'Friends of Friends',
			'owner_member' => 'Friends Only',
			'like_member' => 'Who Liked This Page',
		);
		if (!empty($sitepageMemberEnabled) && $allowMemberInthisPackage) {
			$availableLabels['member'] = 'Page Members Only';
		} elseif( !empty($sitepageMemberEnabled) && $allowMemberInLevel) {
			$availableLabels['member'] = 'Page Members Only';
		}
		$availableLabels['owner'] = $ownerTitle;
    

    $tagOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_album', $user, 'auth_tag');

    $tagOptions = array_intersect_key($availableLabels, array_flip($tagOptions));
    if (!empty($tagOptions) && count($tagOptions) >= 1) {
      if (count($tagOptions) == 1) {
        $this->addElement('hidden', 'auth_tag', array( 'order' => 900,'value' => key($tagOptions)));
      } else {
        $this->addElement('Select', 'auth_tag', array(
            'label' => 'Tag Post Privacy',
            'description' => 'Who may tag photos in this album?',
            'multiOptions' => $tagOptions,
            'value' => key($tagOptions),
        ));
        $this->auth_tag->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper'
        )
    ));
    
    // Init search
    $this->addElement('Checkbox', 'search', array(
      'label' => Zend_Registry::get('Zend_Translate')->_("Show this album in search results"),
      'value' => 1,
      'disableTranslator' => true
    ));
    
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => '',
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }

}

?>