<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Album.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Form_SitemobilePhoto_Album extends Engine_Form {

  public function init() {

    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id');
    $albumvalues['album_id'] = Engine_Api::_()->getItemTable('sitepage_album')->getDefaultAlbum($page_id)->album_id;
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK

    if ($sitepage->owner_id == $viewer_id || $can_edit == 1) {
      $defaultalbum_id = 0;
    } else {
      $defaultalbum_id = $albumvalues['album_id'];
    }

    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    $user = Engine_Api::_()->user()->getViewer();

    $this
            ->setTitle('Add New Photos')
            ->setDescription('Browse and choose photos on your system to add to this album.')
            ->setAttrib('id', 'form-upload')
            ->setAttrib('name', 'albums_create')
            ->setAttrib('class', 'global_form sitepage_form_upload')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
    ;

    $this->addElement('Select', 'album', array(
        'label' => 'Choose Album',
        'multiOptions' => array('0' => 'Create A New Album'),
        'onchange' => "updateTextFields()",
    ));

    //SET ALBUMS PARAMS
    $paramsAlbum = array();
    $paramsAlbum['page_id'] = $page_id;
    $paramsAlbum['getSpecialField'] = 1;

    $all_albums = Engine_Api::_()->getDbtable('albums', 'sitepage')->getAlbums($paramsAlbum);
    $album_options = Array();
    foreach ($all_albums as $all_album) {
      $album_options[$all_album->album_id] = htmlspecialchars_decode($all_album->getTitle());
    }

    $this->album->addMultiOptions($album_options);

    $this->addElement('Text', 'title', array(
        'label' => 'Album Title',
        'maxlength' => '40',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '63')),
        )
    ));

    $this->addElement('Hidden', 'page_id', array(
        'value' => $page_id,
        'order' => 333
    ));

    $this->addElement('Hidden', 'default_album_id', array(
        'value' => $defaultalbum_id,
        'order' => 334
    ));

    // Privacy
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manageadmin', 1))
      $ownerTitle = "Page Admins";
    else
      $ownerTitle = "Just Me";

    $user = Engine_Api::_()->user()->getViewer();
    $availableLabels = array(
        'registered' => 'All Registered Members',
        'owner_network' => 'Friends and Networks',
        'owner_member_member' => 'Friends of Friends',
        'owner_member' => 'Friends Only',
        'owner' => $ownerTitle
    );

    $tagOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_album', $user, 'auth_tag');

    $tagOptions = array_intersect_key($availableLabels, array_flip($tagOptions));
    if (!empty($tagOptions) && count($tagOptions) >= 1) {
      if (count($tagOptions) == 1) {
        $this->addElement('hidden', 'auth_tag', array( 'order' =>335,'value' => key($tagOptions)));
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

    // Init search
    $this->addElement('Checkbox', 'search', array(
        'label' => Zend_Registry::get('Zend_Translate')->_("Show this album in search results"),
        'value' => 1,
        'disableTranslator' => true
    ));
    // Init file
    $this->addElement('FancyUpload', 'file');
    
//    $fancyUpload = new Engine_Form_Element_FancyUpload('file');
//    $fancyUpload->clearDecorators()
//            ->addDecorator('FormFancyUpload')
//            ->addDecorator('viewScript', array(
//                'viewScript' => '_FancyUpload.tpl',
//                'placement' => '',
//            ));
//    Engine_Form::addDefaultDecorators($fancyUpload);
//    $this->addElement($fancyUpload);

//    $this->addElement('Hidden', 'fancyuploadfileids');
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Photos',
        'type' => 'submit',
    ));
  }

  public function clearAlbum() {

    $this->getElement('album')->setValue(0);
  }

  public function saveValues($values) {

    $set_cover = false;

    $viewer = Engine_Api::_()->user()->getViewer();

    $params = Array();
    $getPackageAlbum = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepagealbum');
    if ((empty($values['owner_id']))) {
      $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
    } else {
      $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
      throw new Zend_Exception("Non-user album owners not yet implemented");
    }
    if ((empty($values['album'])) && (empty($values['default_album_id'])) && !empty($getPackageAlbum)) {
      $params['title'] = $values['title'];
      if (empty($params['title'])) {
        $params['title'] = "Untitled Album";
      }
      $params['search'] = $values['search'];

      $default_album_id = Engine_Api::_()->getItemTable('sitepage_album')->getDefaultAlbum($values['page_id'])->album_id;

      if (empty($default_album_id)) {
        $params['default_value'] = 1;
      } else {
        $params['default_value'] = 0;
      }

      $params['page_id'] = $values['page_id'];
      $album = Engine_Api::_()->getDbtable('albums', 'sitepage')->createRow();
      $album->setFromArray($params);
      $album->view_count = 1;
      $album->save();
      $set_cover = true;

      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');

      if (empty($values['auth_tag'])) {
        $values['auth_tag'] = key($form->auth_tag->options);
        if (empty($values['auth_tag'])) {
          $values['auth_tag'] = 'registered';
        }
      }

      $tagMax = array_search($values['auth_tag'], $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
      }

      //COMMENT PRIVACY
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      $auth_comment = "everyone";
      $commentMax = array_search($auth_comment, $roles);
      foreach ($roles as $i => $role) {
        $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
      }
    } else {
      if (!empty($values['album'])) {
        $album = Engine_Api::_()->getItem('sitepage_album', $values['album']);
      } else {
        $album = Engine_Api::_()->getItem('sitepage_album', $values['default_album_id']);
      }
    }
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
    $content_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.photos-sitepage', $album->page_id, $layout);
//     $linked_album_title = '<a target = "_parent" href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'view', 'page_id' => $album->page_id, 'album_id' => $album->album_id, 'slug' => $album->getSlug(), 'tab' => $content_id), 'sitepage_albumphoto_general') . "><b>$album->title</b></a>";
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    $linked_album_title = $view->htmlLink($album->getHref(), $album->getTitle(), array('target' => '_parent'));
    if (count($values['file'] > 0)) {
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $sendFB_Activity = 0;
      $activityFeedType = null;
      if (Engine_Api::_()->sitepage()->isPageOwner($sitepage) && Engine_Api::_()->sitepage()->isFeedTypePageEnable()) {
        $activityFeedType = 'sitepagealbum_admin_photo_new';
        $sendFB_Activity = 1;
      } elseif ($sitepage->all_post || Engine_Api::_()->sitepage()->isPageOwner($sitepage)) {
        $activityFeedType = 'sitepagealbum_photo_new';
        $sendFB_Activity = 1;
      }

      if ($activityFeedType) {
        $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $sitepage, $activityFeedType, null, array('count' => count($values['file']), 'linked_album_title' => $linked_album_title));
        Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
      }

      //PAGE ALBUMS CREATE NOTIFICATION AND EMAIL WORK
			$sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
			if (!empty(Engine_Api::_()->seaocore()->checkVersion($sitepageVersion, '4.2.9p3'))) {
				Engine_Api::_()->sitepage()->sendNotificationEmail($album, $action, 'sitepagealbum_create', 'SITEPAGEALBUM_CREATENOTIFICATION_EMAIL', 'Pageevent Invite');
				
				$isPageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->isPageAdmins($viewer->getIdentity(), $album->page_id);
				if (!empty($isPageAdmins)) {
					//NOTIFICATION FOR ALL FOLLWERS.
					Engine_Api::_()->sitepage()->sendNotificationToFollowers($album, $action, 'sitepagealbum_create');
				}
      }

      if ($sendFB_Activity == 1) {
        //SENDING ACTIVITY FEED TO FACEBOOK.
        $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
        if (!empty($enable_Facebooksefeed)) {
          $facebooksefeedmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebooksefeed');
          $facebooksefeedmoduleversion = $facebooksefeedmodule->version;
          if (Engine_Api::_()->seaocore()->checkVersion($facebooksefeedmoduleversion, '4.1.6p2') == 1) {
            $album_array = array();
            $album_array['type'] = 'sitepagealbum_photo_new';
            $album_array['object'] = $album;
            $album_array['file_ids'] = $this->getValues();
            Engine_Api::_()->facebooksefeed()->sendFacebookFeed($album_array);
          }
        }
      }
      
    }

    $count = 0;
    foreach ($values['file'] as $photo_id) {
      $photo = Engine_Api::_()->getItem("sitepage_photo", $photo_id);
      if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
        continue;
      if ($set_cover) {
        $album->photo_id = $photo_id;
        $album->save();
        $set_cover = false;
      }
      $photo->album_id = $album->album_id;
      $photo->collection_id = $album->album_id;
      $photo->save();

      if ($action instanceof Activity_Model_Action && $count < 8) {
        $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
      }
      $count++;
    }
    return $album;
  }

}