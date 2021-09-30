<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Widget_MenuMainController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $coreMenuApi = Engine_Api::_()->getApi('menus', 'core');
    $this->view->menuPannelType = $this->_getParam('menuType', 'overlay');
    $this->view->menuIcons = $this->_getParam('menuIcons', 1);
    $this->view->alwaysOpen = $this->view->menuPannelType === 'slide' && $this->_getParam('alwaysOpen', '0');
    $pages = $coreMenuApi
      ->getMenuParams('core_main');
    if ($this->_getParam('mobuleNavigations', 1)) {
      foreach ($pages as $key => $page) {
        $menuName = end(explode(' ', $page['class']));
        $moduleName = str_replace('core_main_', '', $menuName);
        if (strpos($moduleName, 'custom_') !== false) {
          continue;
        }
        if (strpos($moduleName, 'sitereview_listtype_') !== false) {
          $mainMenu = str_replace('sitereview_', 'sitereview_main_', $moduleName);
        } else {
          $mainMenu = $moduleName . '_main';
        }
        $subPages = $coreMenuApi->getMenuParams($mainMenu);
        if (empty($subPages)) {
          continue;
        }
        $page['pages'] = $subPages;
        $pages[$key] = $page;
      }
    }
    $navigation = new Zend_Navigation();
    $navigation->addPages($pages);
    $this->view->navigation = $navigation;
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $requireCheck = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
    if (!$requireCheck && !$viewer->getIdentity()) {
      $navigation->removePage($navigation->findOneBy('route', 'user_general'));
    }
    $this->view->coverUserPhoto = $this->getUserCoverPhoto();

    if ($viewer && $viewer->getIdentity() && $this->_getParam('settingNavigations', 1)) {
      // Set up navigation
      $this->view->userSettingsNavigation = Engine_Api::_()
        ->getApi('menus', 'core')
        ->getNavigation('user_settings');
      // Check last super admin
      if (1 === count(Engine_Api::_()->user()->getSuperAdmins()) && 1 === $viewer->level_id) {
        foreach ($this->view->userSettingsNavigation as $page) {
          if ($page instanceof Zend_Navigation_Page_Mvc &&
            $page->getAction() == 'delete') {
            $this->view->userSettingsNavigation->removePage($page);
          }
        }
      }
    }
    $this->view->footerSection = $this->_getParam('footerSection', 1);
  }

  public function getCacheKey() {
    //return Engine_Api::_()->user()->getViewer()->getIdentity();
  }

  public function getUserCoverPhoto() {
    $user = Engine_Api::_()->user()->getViewer();
    $coverPhoto = null;
    if (!$user->getIdentity()) {
      return $coverPhoto;
    }
    $albumType = Engine_Api::_()->hasModuleBootstrap('advalbum') ? 'advalbum_photo' : 'album_photo';
    if (Engine_Api::_()->hasModuleBootstrap('siteusercoverphoto')) {
      $has_advalbum = Engine_Api::_()->hasModuleBootstrap('advalbum');
      if (isset($user->user_cover) && $user->user_cover) {
        $coverPhoto = Engine_Api::_()->getItem($albumType, $user->user_cover);
      } elseif (!empty($coverId = Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$user->level_id.id"))) {
        $coverPhoto = Engine_Api::_()->getItem('storage_file', $coverId);
      }
    } elseif (isset($user->coverphoto)) {
      if (Engine_Api::_()->authorization()->isAllowed('user', $user, 'coverphotoupload') && $user->coverphoto) {

        $coverPhoto = Engine_Api::_()->getItem('storage_file', $user->coverphoto);
      }
      $coverId = Engine_Api::_()->getApi("settings", "core")
        ->getSetting("usercoverphoto.preview.level.id." . $user->level_id);
      if (empty($coverPhoto) && $coverId) {
        $coverPhoto = Engine_Api::_()->getItem('storage_file', $coverId);
      }
    }
    return $coverPhoto;
  }

}