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
class Sitepage_Widget_PhotolightboxAdsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//COMMUNITY-ADS IS ENABLED OR NOT
    $enable_ads = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
    if (!$enable_ads) {
      return $this->setNoRender();
    }

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1)) {
      return $this->setNoRender();
    }

		//GET TAB ID AND VIEWER DETAIL
    $this->view->tab = $tab = $this->_getParam('tab', null);
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->user_id = $viewer->getIdentity();

    $params = array();
    $params['lim'] = $this->_getParam('limit', null);
    $ad_type = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adtype', 3);

    switch ($ad_type) {
      case 0:
        $params['sponsored'] = 1;
        $params['featured'] = 1;
        break;

      case 1:
        $params['featured'] = 1;
        break;

      case 2:
        $params['sponsored'] = 1;
        break;

      case 3:
        break;
    }

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

}
?>