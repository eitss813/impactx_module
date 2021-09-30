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
class Sitepagevideo_Widget_ShowSameTagsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
     $video_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('video_id', $this->_getParam('video_id', null));
    $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $video_id);

    if (empty($sitepagevideo)) {
      return $this->setNoRender();
    }

     //GET SUBJECT
    $subject = Engine_Api::_()->getItem('sitepage_page', $sitepagevideo->page_id);

    //GET TAB ID
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');
    $this->view->page_id = $sitepagevideo->page_id;
    // PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagevideo")) {
        return $this->setNoRender();
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'svcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->setNoRender();
      }
    }
//     $sitepagevideo_getlike = Zend_Registry::isRegistered('sitepagevideo_getlike') ? Zend_Registry::get('sitepagevideo_getlike') : null;
//     if (empty($sitepagevideo_getlike)) {
//       return $this->setNoRender();
//     }
    // PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK
    //FETCH VIDEOS
    $params = array();
    $widgetType = 'showsametag';
    $params['resource_type'] = $sitepagevideo->getType();
    $params['resource_id'] = $sitepagevideo->getIdentity();
    $params['video_id'] = $sitepagevideo->getIdentity();
    $params['limit'] = $this->_getParam('itemCount', 3);
    $params['view_action'] = 1;

		$this->view->paginator = $paginator = Engine_Api::_()->getDbtable('videos', 'sitepagevideo')->widgetVideosData($params,'',$widgetType);
    $this->view->count_video = Count($paginator);
    $this->view->limit_sitepagevideo = $this->_getParam('itemCount', 3);

    if( Count($paginator) <= 0 ) {
      return $this->setNoRender();
    }
  }
}