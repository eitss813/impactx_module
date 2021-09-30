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
class Sitepagevideo_Widget_LikeSitepagevideosController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GET SUBJECT
    $subject = Engine_Api::_()->core()->getSubject('sitepage_page');

    // PACKAGE BASE PRIYACY START
    if ( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
      if ( !Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagevideo") ) {
        return $this->setNoRender();
      }
    }
    else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'svcreate');
      if ( empty($isPageOwnerAllow) ) {
        return $this->setNoRender();
      }
    }
    $sitepagevideo_getlike = Zend_Registry::isRegistered('sitepagevideo_getlike') ? Zend_Registry::get('sitepagevideo_getlike') : null;
    if ( empty($sitepagevideo_getlike) ) {
      return $this->setNoRender();
    }
    // PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'view');
    if ( empty($isManageAdmin) ) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK
    //FETCH VIDEOS
    $params = array();
    $params['page_id'] = $subject->page_id;
    $params['orderby'] = 'like_count DESC';
    $params['zero_count'] = 'like_count';
    $params['profile_page_widget'] = 1;
    $params['limit'] = $this->_getParam('itemCount', 3);
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('videos', 'sitepagevideo')->widgetVideosData($params);

    if ( Count($paginator) <= 0 ) {
      return $this->setNoRender();
    }
  }

}

?>