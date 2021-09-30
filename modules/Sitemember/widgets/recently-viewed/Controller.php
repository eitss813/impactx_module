<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Widget_RecentlyViewedController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() { 
        $this->view->settings = Engine_Api::_()->getApi('settings', 'core');
        
        $this->view->viewer_id = $viewer_id =  Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->view->subject_id = 0;
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->fullWidth = $coreSettings->getSetting('siteusercoverphoto.content.full.width', 0);
        $this->view->insideTab = $coreSettings->getSetting('siteusercoverphoto.change.tab.position', 1);;
        if (Engine_Api::_()->core()->hasSubject()) {
            $subject = Engine_Api::_()->core()->getSubject();
            $this->view->subject_id =$subject_id = $subject->getIdentity();
        }
        $params = array();
        $this->view->limit = $params['limit'] = $this->_getParam('itemCount', 5);
        $viewedBy = $this->_getParam('viewed_by', 'viewed_by_me');
        $viewsTable = Engine_Api::_()->getDbTable("views","sitemember");
        if ($viewedBy == 'viewed_by_me' && !empty($viewer_id)) {
            $ids = $viewsTable->getAllIdsViewedByMe($viewer_id);
        } elseif(!empty ($subject) && $subject->getType()=='user' && $viewedBy == 'viewed_by_user') {
            $ids = $viewsTable->getAllIdsViewedByUsers($subject_id);
        } 
        

        if(empty($ids)){
            return $this->setNoRender();
        }
        $params["viewed_by"] = $ids;
        $this->view->statistics = $params['memberInfo'] = $this->_getParam('memberInfo', array("memberCount", "mutualFriend", "title"));
        $this->view->siteusercoverphoto = $this->_getParam('siteusercoverphoto', '0');
        $this->view->itemCount = $this->_getParam('itemCount', 10); 
        $this->view->columnWidth = $params['columnWidth'] = $this->_getParam('columnWidth', '180');
        $this->view->columnHeight = $params['columnHeight'] = $this->_getParam('columnHeight', '328');
        $this->view->circularImage = $params['circularImage'] = $this->_getParam('circularImage', 0);
        $this->view->circularImageHeight = $params['circularImageHeight'] = $this->_getParam('circularImageHeight', 190);
        $params['has_photo'] = $params['has_photo'] = $this->_getParam('has_photo', 1);
        $this->view->viewtitletype = $params['viewtitletype'] = $this->_getParam('viewtitletype', 'vertical');
        $this->view->identity = $params['identity'] = $this->_getParam('identity', $this->view->identity);
        $this->view->viewType = $params['viewType'] = $this->_getParam('viewType', 'gridview');
        $this->view->titlePosition = $params['titlePosition'] = $this->_getParam('titlePosition', 1);
        $this->view->params = $params;

        $this->view->members = $paginator = Engine_Api::_()->user()->getUserMulti($ids);
        
        if (@count($paginator) <= 0) {
            return $this->setNoRender();
        }
         
    }

}
