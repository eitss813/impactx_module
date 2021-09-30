<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_PageAdsController extends Engine_Content_Widget_Abstract {

  //ACTION FOR SHOWING THE AD WITH PAGES
  public function indexAction() {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $module = $request->getModuleName();
		$controller = $request->getControllerName();
    $action = $request->getActionName();
    $load_content = 0;
    $this->view->communityad_id = $communityad_id = $this->_getParam('communityadid', null);
    $this->view->isajax = $isajax = $this->_getParam('isajax', null);
    $this->view->limit = $limit = $this->_getParam('limit', null);

    $enable_ads = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
    if (!$enable_ads) {
      return $this->setNoRender();
    }

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1)) {
      return $this->setNoRender();
    }

    if ($this->view->identity) {
      $limit = 0;
      $this->view->identity_temp =$this->view->identity;
      $this->view->communityad_id = $communityad_id = $this->_getParam('communityadid', "communityadid_widget_showads");

      switch ($module) {

        case "sitepageevent":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adeventview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adeventbrowse', 3);
          }
          break;
        case "sitepagevideo":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.advideoview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.advideobrowse', 3);
          }
          break;
        case "sitepagenote":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnoteview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnotebrowse', 3);
          }
          break;
        case "sitepagemember":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.admemberwidget', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.admemberbrowse', 3);
          }
          break;
        case "sitepagepoll":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adpollview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adpollbrowse', 3);
          }
          break;
        case "sitepageoffer":
          $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adofferlist', 3);
          break;
        case "sitepagemusic":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.admusicview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.admusicbrowse', 3);
          }
          break;
        case "sitepagereview":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adreviewview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adreviewbrowse', 3);
          }
          break;
        case "sitepagedocument":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addocumentview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addocumentbrowse', 3);
          }
          break;
        case "sitepagebadge":
          $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adbadgeview', 3);
          break;
        case "sitepage":
          if ($controller == 'album' && $action == 'view' ) {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adalbumview', 3);
          } elseif($controller == 'album' && $action == 'browse' ) {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adalbumbrowse', 3);
          }
          break;
      }
      if (empty($limit)) {
        return $this->setNoRender();
      }
    }

    if (!empty($_GET['load_content']) || empty($communityad_id) || !empty($isajax)) {
      $load_content = 1;
      $this->view->tab = $this->_getParam('tab', null);
      if ($limit == 0 && empty($this->view->identity)) {
        return $this->setNoRender();
      }
//      elseif (!empty($this->view->identity)) {
//        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')) {
//          $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adeventview', 3);
//          if (empty($limit)) {
//            return $this->setNoRender();
//          }
//        }
//      }

      $this->view->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $params = array();
      $params['lim'] = $limit;

      if (Engine_Api::_()->core()->hasSubject()) {
        $subject = Engine_Api::_()->core()->getSubject();
        Engine_Api::_()->core()->clearSubject();
      }

      $fetch_community_ads = Engine_Api::_()->communityad()->getAdvertisement($params);
      if (!empty($subject)) {
        Engine_Api::_()->core()->clearSubject();
        Engine_Api::_()->core()->setSubject($subject);
      }

      if (!empty($fetch_community_ads)) {
        $this->view->communityads_array = $fetch_community_ads;
      } else {
        return $this->setNoRender();
      }
    }

    $this->view->load_content = $load_content;
  }

}

?>