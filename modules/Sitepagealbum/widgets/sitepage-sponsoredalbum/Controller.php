<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagealbum_Widget_SitepageSponsoredalbumController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //NUMBER OF ALBUMS IN LISTING
    $totalAlbums = $this->_getParam('itemCount', 3);

    //GET ALBUM DATAS
    $params = array();
    $params['category_id'] = $this->_getParam('category_id',0);
    $params['limit'] = $totalAlbums;
    $albumType = 'sponsored';
    $this->view->recentlyview = $row = Engine_Api::_()->getDbtable('albums', 'sitepage')->getAlbums($params,$albumType);
    $sitepagePackageEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.enable', 1);
    if ( ( Count($row) <= 0 ) || empty($sitepagePackageEnable)) {
      return $this->setNoRender();
    }
  }

}
?>