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
class Sitepagevideo_Widget_SitepageSponsoredvideoController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $getPackageVideo = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepagevideo');

    //NUMBER OF VIDEOS IN LISTING
    $totalVideos = $this->_getParam('itemCount', 3);

//     $sitepagevideo_sponsoredvideo = Zend_Registry::isRegistered('sitepagevideo_sponsoredvideo') ? Zend_Registry::get('sitepagevideo_sponsoredvideo') : null;

    //GET VIDEO DATAS
    $params = array();
    $params['category_id'] = $this->_getParam('category_id',0);
    $params['limit'] = $totalVideos;
    $videoType = 'sponsored';
    $this->view->recentlyview = $row = Engine_Api::_()->getDbtable('videos', 'sitepagevideo')->widgetVideosData($params,$videoType);
     $sitepagePackageEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.enable', 1);
    if ( ( Count($row) <= 0 ) || empty($getPackageVideo) || empty($sitepagePackageEnable) ) {
      return $this->setNoRender();
    }
  }

}
?>