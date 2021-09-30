<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Widget_VideoContentController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    $this->_mobileAppFile = true;
     //GET VIDEO ID AND OBJECT
    $video_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('video_id', $this->_getParam('video_id', null));
    $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $video_id);

    if (empty($sitepagevideo)) {
      return $this->setNoRender();
    }

    //GET TAB ID
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');

    $getPackagevideoView = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepagevideo');

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //IF THIS IS SENDING A MESSAGE ID, THE USER IS BEING DIRECTED FROM A CONVERSATION
    //CHECK IF MEMBER IS PART OF THE CONVERSATION
    $message_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('message');
    $message_view = false;
    if ($message_id) {
      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      if ($conversation->hasRecipient(Engine_Api::_()->user()->getViewer()))
        $message_view = true;
    }
    $this->view->message_view = $message_view;

    //SET SITEPAGE SUBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagevideo->page_id);

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo")) {
        return $this->setNoRender();
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');
    if (empty($isManageAdmin)) {
      $this->view->can_create = 0;
    } else {
      $this->view->can_create = 1;
    }
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

     //MAKE HIGHLIGHTED OR NOT
    $this->view->canMakeHighlighted = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.featured', 1);

    $this->view->allowView = false;
    if (!empty($viewer_id) && $viewer->level_id == 1) {
      $auth = Engine_Api::_()->authorization()->context;
      $this->view->allowView = $auth->isAllowed($sitepage, 'everyone', 'view') === 1 ? true : false ||$auth->isAllowed($sitepage, 'registered', 'view') === 1 ? true : false;
    } 

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'comment');
    if (empty($isManageAdmin)) {
      $this->view->can_comment = 0;
    } else {
      $this->view->can_comment = 1;
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = $this->view->can_edit = 0;
    } else {
      $can_edit = $this->view->can_edit = 1;
    }

    if ($viewer_id != $sitepagevideo->owner_id && $can_edit != 1 && ($sitepagevideo->status != 1 || $sitepagevideo->search != 1) || empty($getPackagevideoView)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK
    //GET VIDEO TAGS
    $this->view->videoTags = $sitepagevideo->tags()->getTagMaps();

    //CHECK IF EMBEDDING IS ALLOWED
    $can_embed = true;
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.embeds', 1)) {
      $can_embed = false;
    } else if (isset($sitepagevideo->allow_embed) && !$sitepagevideo->allow_embed) {
      $can_embed = false;
    }
    $this->view->can_embed = $can_embed;

    $this->view->videoEmbedded = $embedded = "";
    //INCREMENT IN NUMBER OF VIEWS
      $owner = $sitepagevideo->getOwner();
      if (!$owner->isSelf($viewer)) {
        $sitepagevideo->view_count++;
      }
      $sitepagevideo->save();
			if ($sitepagevideo->type != 3) {
				$this->view->videoEmbedded = $embedded = $sitepagevideo->getRichContent(true);
			}

    //SET PAGE-VIDEO SUBJECT
    if (Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->clearSubject();
    }
    Engine_Api::_()->core()->setSubject($sitepagevideo);

    //VIDEO FROM MY COMPUTER WORK
    if ($sitepagevideo->type == 3 && $sitepagevideo->status != 0) {
      $sitepagevideo->save();

      if (!empty($sitepagevideo->file_id)) {
        $storage_file = Engine_Api::_()->getItem('storage_file', $sitepagevideo->file_id);
        if ($storage_file) {
          $this->view->video_location = $storage_file->map();
          $this->view->video_extension = $storage_file->extension;
        }
      }
    }

    $this->view->rating_count = Engine_Api::_()->getDbTable('ratings', 'sitepagevideo')->ratingCount($sitepagevideo->getIdentity());
    $this->view->video = empty($getPackagevideoView) ? null : $sitepagevideo;
    $this->view->rated = Engine_Api::_()->getDbTable('ratings', 'sitepagevideo')->checkRated($sitepagevideo->getIdentity(), $viewer->getIdentity());

    //TAG WORK
    $this->view->limit_sitepagevideo = $total_sitepagevideos = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.tag.limit', 3);
    
    //VIDEO TABLE
    $videoTable = Engine_Api::_()->getDbtable('videos', 'sitepagevideo');

    //TOTAL VIDEO COUNT FOR THIS PAGE
    $this->view->count_video = $videoTable->getPageVideoCount($sitepagevideo->page_id);

    // Start: "Suggest to Friends" link work.
    $page_flag = 0;
    $is_suggestion_enabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
    $is_moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage');
    $isSupport = Engine_Api::_()->getApi('suggestion', 'sitepage')->isSupport();
    if (!empty($is_suggestion_enabled)) {
      // Here we are delete this video suggestion if viewer have.
      if (!empty($is_moduleEnabled)) {
        Engine_Api::_()->getApi('suggestion', 'sitepage')->deleteSuggestion($viewer_id, 'page_video', Zend_Controller_Front::getInstance()->getRequest()->getParam('video_id'), 'page_video_suggestion');
      }

      $SuggVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('suggestion')->version;
      $versionStatus = strcasecmp($SuggVersion, '4.1.7p1');
      if ($versionStatus >= 0) {
        $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('sitepagevideo', Zend_Controller_Front::getInstance()->getRequest()->getParam('video_id'), 1,null);
        if (!empty($modContentObj)) {
          $contentCreatePopup = @COUNT($modContentObj);
        }
      }

      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.enable', 1)) {
        if ($sitepage->expiration_date <= date("Y-m-d H:i:s")) {
          $page_flag = 1;
        }
      }
      if (!empty($contentCreatePopup) && !empty($isSupport) && empty($sitepage->closed) && !empty($sitepage->approved) && empty($sitepage->declined) && !empty($sitepage->draft) && empty($page_flag) && !empty($viewer_id) && !empty($is_suggestion_enabled)) {
        $this->view->videoSuggLink = Engine_Api::_()->suggestion()->getModSettings('sitepage', 'video_sugg_link');
      }
      // End: "Suggest to Friends" link work.
    }
  }

}
?>