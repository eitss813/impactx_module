<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: BadgeController.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_BadgeController extends Core_Controller_Action_Standard {

  public function init() {
    $this->view->badgeEnable = $badge_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.badge', 1);

    if (empty($badge_enable)) {
      $action = $this->_getParam('action', null);
      if ($action != 'index') {
        return $this->_forward('notfound', 'error', 'core');
      }
    }
  }

  public function indexAction() {
    // $this->_helper->layout->setLayout('default-simple');
    $this->_helper->layout->disableLayout();
    extract($_GET);

    $parentTable = Engine_Api::_()->getItemTable('album');
    $parentTableName = $parentTable->info('name');
    $table = Engine_Api::_()->getItemTable('album_photo');
    $tableName = $table->info('name');
    $select = $table->select()
            ->from($tableName);
    if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
      $select->joinLeft($parentTableName, $parentTableName . '.album_id=' . $tableName . '.album_id', null);
    } else {
      $select->joinLeft($parentTableName, $parentTableName . '.album_id=' . $tableName . '.collection_id', null);
    }

    switch ($type) {
      case 'recent':
        break;
      case 'liked':
        $select->order($tableName . '.like_count DESC');
        break;
      case 'viewed':
        $select->order($tableName . '.view_count DESC');
        break;
      case 'commented':
        $select->order($tableName . '.comment_count DESC');
        break;
      case 'random':
        $select->order('Rand()');
        break;
      case 'album':
        if (!Engine_Api::_()->sitealbum()->isLessThan417AlbumModule()) {
          $select->where($tableName . '.album_id = ?', $id);
        } else {
          $select->where($tableName . '.collection_id = ?', $id);
        }
        break;
    }
    $select->where($tableName . '.owner_id 	 = ?', $owner);
    if ($type != 'random')
      $select->order($tableName . '.photo_id DESC');

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1))
      $select->where($parentTableName . '.type IS NULL');
    if (empty($no_of_image))
      $no_of_image = 10;

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($no_of_image);
    $paginator->setCurrentPageNumber(1);
    $this->view->border_color = $border_color;
    $this->view->background_color = $background_color;
    $this->view->text_color = $text_color;
    $this->view->link_color = $link_color;
    $this->view->height = $height;
    $width = $width - 8;
    $this->view->inOneRowWidth = @floor($width / 116) * 116;
    $this->view->owner = $owner = Engine_Api::_()->user()->getUser($owner);
  }

  public function createAction() {
    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    $viewerType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum_viewertype');
    if (empty($viewerType)) {
      return;
    }
        // Render
    $this->_helper->content->setEnabled();
    
    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitealbum_main');
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $myAlbums = Engine_Api::_()->getItemTable('album')->getUserAlbums($viewer, array('fetchAll' => 1));
    $albumOptions = array();
    foreach ($myAlbums as $myAlbum) {
      $albumOptions[$myAlbum['album_id']] = $myAlbum['title'];
    }
    $count_my_album = $myAlbums->count();
    if (empty($count_my_album)) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('You have not added any photos yet.');
      $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');
      return;
    }
    $form = $this->view->form = new Sitealbum_Form_Badge_Create();
    $form->owner->setValue($viewer_id);
    $form->album->addMultiOptions($albumOptions);
    $album_id = (int) $this->_getParam('album_id', 0);
    if (!empty($album_id)) {
      $this->view->type = 'album';
      $form->album->setValue($album_id);
      $form->type->setValue('album');
    }
  }

  public function getSourceAction() {

    $params = $this->_getAllParams();
    extract($params);
    $url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'index'), 'sitealbum_badge', true);
    $url.='?type=' . urlencode($type) . '&amp;amp;id=' . urlencode($id) . '&amp;amp;width=' . urlencode($width) . '&amp;amp;height=' . urlencode($height) . '&amp;amp;owner=' . urlencode($owner) . '&amp;amp;no_of_image=' . urlencode($no_of_image) . '&amp;amp;background_color=' . urlencode($background_color) . '&amp;amp;border_color=' . urlencode($border_color) . '&amp;amp;text_color=' . urlencode($text_color) . '&amp;amp;link_color=' . urlencode($link_color);
    $code = '&lt;iframe scrolling="no" frameborder="0" id="badge_photo_iframe" src="' . $url . '" style="overflow: auto; width: ' . $width . 'px; height: ' . $height . 'px;" allowTransparency="true" &gt;';
    $code.="&lt;center&gt;&lt;img src='" . "http://" . $_SERVER['HTTP_HOST'] . $this->view->baseUrl() . "/application/modules/Sitealbum/externals/images/loader.gif' /&gt; &lt;/center&gt;";
    $code .='&lt;/iframe&gt;';
    $this->view->code = $code;
  }

}