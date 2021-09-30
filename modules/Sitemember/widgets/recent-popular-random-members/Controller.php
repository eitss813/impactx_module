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
class Sitemember_Widget_RecentPopularRandomMembersController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        $this->view->settings = Engine_Api::_()->getApi('settings', 'core');
        if ($this->_getParam('is_ajax_load', false)) {
            $this->view->is_ajax_load = true;
            if ($this->_getParam('page', 1) > 1)
                $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        } else {

            if (!$this->_getParam('detactLocation', 0)) {
                $this->view->is_ajax_load = true;
            } else {
                $this->getElement()->removeDecorator('Title');
            }
        }

        $params = array();
        $params['orderby'] = $this->_getParam('orderby', 'creation_date');
        $this->view->limit = $params['limit'] = $this->_getParam('itemCount', 5);
        $fea_spo = $this->_getParam('fea_spo', '');
        if ($fea_spo == 'featured') {
            $params['featured'] = 1;
        } elseif ($fea_spo == 'sponsored') {
            $params['sponsored'] = 1;
        } elseif ($fea_spo == 'fea_spo') {
            $params['sponsored'] = 1;
            $params['featured'] = 1;
        }

        $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->view->subject_id = 0;
        if (Engine_Api::_()->core()->hasSubject()) {
            $this->view->subject_id = Engine_Api::_()->core()->getSubject()->getIdentity();
        }

        $this->view->statistics = $params['memberInfo'] = $this->_getParam('memberInfo', array("featuredLabel", "sponsoredLabel", "location", "directionLink", "viewCount", "likeCount", "memberCount", "mutualFriend", "memberStatus", "joined", "networks", "profileField", "title"));
        $this->view->truncation = $params['truncation'] = $this->_getParam('truncation', 16);
        $this->view->columnWidth = $params['columnWidth'] = $this->_getParam('columnWidth', '180');
        $this->view->columnHeight = $params['columnHeight'] = $this->_getParam('columnHeight', '328');
        $this->view->circularImage = $params['circularImage'] = $this->_getParam('circularImage', 0);
        $this->view->circularImageHeight = $params['circularImageHeight'] = $this->_getParam('circularImageHeight', 190);
        $params['interval'] = $params['interval'] = $this->_getParam('interval', 'overall');
        $params['has_photo'] = $params['has_photo'] = $this->_getParam('has_photo', 1);
        $this->view->viewtitletype = $params['viewtitletype'] = $this->_getParam('viewtitletype', 'vertical');
        $this->view->links = $this->_getParam('links', array("addfriend", "message"));
        $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
        if ($this->view->detactLocation) {
            $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            $params['latitude'] = $this->_getParam('latitude', 0);
            $params['longitude'] = $this->_getParam('longitude', 0);
        }

        //When viewer and member profile is visited by other persion.
        if (!empty($this->view->detactLocation)) {
            $params['limit'] = $params['limit'] + 2;
        }

        $this->view->identity = $params['identity'] = $this->_getParam('identity', $this->view->identity);
        $this->view->viewType = $params['viewType'] = $this->_getParam('viewType', 'gridview');
        $this->view->titlePosition = $params['titlePosition'] = $this->_getParam('titlePosition', 1);
        $this->view->params = $params;

        if (!$this->view->is_ajax_load)
            return;
        $this->view->members = $paginator = Engine_Api::_()->sitemember()->getUsersSelect($params);
        if (@count($paginator) <= 0) {
            return $this->setNoRender();
        }
    }

}
