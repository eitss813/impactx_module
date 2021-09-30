<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagevideo_Plugin_Menus {

  public function canViewVideos() {

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagevideo.video.show.menu', 1)) {
      return false;
    }

    $table = Engine_Api::_()->getDbtable('videos', 'sitepagevideo');
    $rName = $table->info('name');
    $table_pages = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $rName_pages = $table_pages->info('name');
    $select = $table->select()
                    ->setIntegrityCheck(false)
                    ->from($rName_pages, array('photo_id', 'title as sitepage_title'))
                    ->join($rName, $rName . '.page_id = ' . $rName_pages . '.page_id')
                    ->where($rName . '.status = ?', '1')
                    ->where($rName .'.search = ?', 1);

    $select = $select
                    ->where($rName_pages . '.closed = ?', '0')
                    ->where($rName_pages . '.approved = ?', '1')
                    ->where($rName_pages . '.search = ?', '1')
                    ->where($rName_pages . '.declined = ?', '0')
                    ->where($rName_pages . '.draft = ?', '1');
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $select->where($rName_pages . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    $row = $table->fetchAll($select);
    $count = count($row);
    if (empty($count)) {
      return false;
    }
    return true;
  }

  //SITEMOBILE PAGE VIDEO MENUS
  public function onMenuInitialize_SitepagevideoAdd($row) {
    $subject = Engine_Api::_()->core()->getSubject();

    $video_id = $subject->getIdentity();

    $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $video_id);
    $page_id = $sitepagevideo->page_id;
    if (empty($sitepagevideo)) {
      return false;
    }
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');
    if (empty($isManageAdmin)) {
      $can_create = 0;
    } else {
      $can_create = 1;
    }

    if (empty($can_create)) {
      return false;
    }
    return array(
        'label' => 'Add Video',
        'route' => 'sitepagevideo_create',
        'class' => 'ui-btn-action',
        'params' => array(
            'page_id' => $page_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitepagevideoEdit($row) {
    $subject = Engine_Api::_()->core()->getSubject();

    $video_id = $subject->getIdentity();

    $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $video_id);
    $page_id = $sitepagevideo->page_id;
    if (empty($sitepagevideo)) {
     return false;
    }

    $check = $this->commonChecks();
    if (empty($check)) {
      return false;
    }
    return array(
        'label' => 'Edit Video',
        'route' => 'sitepagevideo_edit',
        'class' => 'ui-btn-action',
        'params' => array(
            'video_id' => $video_id,
            'page_id' => $page_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitepagevideoDelete($row) {
    $subject = Engine_Api::_()->core()->getSubject();

    $video_id = $subject->getIdentity();

    $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $video_id);
    $page_id = $sitepagevideo->page_id;
    if (empty($sitepagevideo)) {
      return false;
    }

    $check = $this->commonChecks();
    if (empty($check)) {
      return false;
    }
    return array(
        'label' => 'Delete Video',
        'route' => 'sitepagevideo_delete',
        'class' => 'ui-btn-danger',
        'params' => array(
            'video_id' => $video_id,
            'page_id' => $page_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function commonChecks() {
    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();

    $video_id = $subject->getIdentity();

    $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $video_id);
    $page_id = $sitepagevideo->page_id;
    if (empty($sitepagevideo)) {
      return false;
    }
    $getPackagevideoView = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepagevideo');
    $video = empty($getPackagevideoView) ? null : $sitepagevideo;

    $owner_id = $video->owner_id;
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    if ($owner_id != $viewer_id && empty($can_edit)) {
      return false;
    } else {
      return true;
    }
  }
  
   public function onMenuInitialize_SitepagevideoReport(){
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if(!$viewer_id){
     return false;    
    }
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitepagevideo_video') {
      throw new Video_Model_Exception('Whoops, not a video!');
    }

    return array(
        'label' => 'Report',
        'route' => 'default',
        'params' => array(
            'controller' => 'report',
            'action' => 'create',
            'subject' => $subject->getGuid(),
            'module' => 'core',
            'format' => 'smoothbox'
        )
    );
  }
  
   public function onMenuInitialize_SitepagevideoShare(){
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if(!$viewer_id){
     return false;
    }
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitepagevideo_video') {
      throw new Video_Model_Exception('Whoops, not a video!');
    }

    return array(
        'label' => 'Share',
        //'icon' => 'application/modules/Video/externals/images/delete.png',
        'route' => 'default',
        'params' => array(
            'controller' => 'index',
            'action' => 'share',
            'id' => $subject->getIdentity(),
            'type' => 'sitepagevideo_video',
            'module' => 'activity',
            'format' => 'smoothbox'
        )
    );
  }
}
?>