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
class Sitecoretheme_Widget_BrowseMenuMainController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $coreApi = Engine_Api::_()
      ->getApi('menus', 'core');
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $viewer = Engine_Api::_()->user()->getViewer();
    if (Engine_Api::_()->hasModuleBootstrap('sitemenu')) {
      $isCacheEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.cache.enable', true);
      if (!empty($isCacheEnabled)) {
        $cache = Zend_Registry::get('Zend_Cache');

        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
          $viewer_level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
          $viewer_level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $cacheName = 'browse_main_menu_cache_level_' . $viewer_level_id;
        $data = $cache->load($cacheName);
        if (!empty($data)) {
          $this->view->browsenavigation = $navigation = $data;
        } else {
          $this->view->browsenavigation = $navigation = $data = $this->getNavigation();
          $cache->setLifetime(Engine_Api::_()->sitemenu()->cacheLifeInSec());
          $cache->save($data, $cacheName);
        }
      } else {
        $this->view->browsenavigation = $navigation = $this->getNavigation();
      }
    } else {
      $this->view->browsenavigation = $navigation = $this->getNavigation();
    }
    $this->view->max = $coreSettings->getSetting('sitecoretheme.header.desktop.totalmenu', 3);
    $this->view->max = $this->_getParam('max', false) > 0 ? $this->_getParam('max') : $this->view->max;
    $this->view->menuIcons = $this->_getParam('menuIcons', false);
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
    if (!$require_check && !$viewer->getIdentity()) {
      $navigation->removePage($navigation->findOneBy('route', 'user_general'));
    }

  $this->view->mobileNavigation = $mobileNavigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('core_mini');
    //print_r($mobileNavigation);

      $front = Zend_Controller_Front::getInstance();
      $this->view->moduleName = $module = $front->getRequest()->getModuleName();
      $controller = $front->getRequest()->getControllerName();
      $action = $front->getRequest()->getActionName();
  }

  protected function getNavigation() {
    $coreMenuApi = Engine_Api::_()->getApi('menus', 'core');
    $pages = $coreMenuApi
      ->getMenuParams('core_main');
    if ($this->_getParam('mobuleNavigations', 0)) {
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

    $viewer = Engine_Api::_()->user()->getViewer();
    $requireCheck = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
    if (!$requireCheck && !$viewer->getIdentity()) {
      $navigation->removePage($navigation->findOneBy('route', 'user_general'));
    }
    return $navigation;
  }

}