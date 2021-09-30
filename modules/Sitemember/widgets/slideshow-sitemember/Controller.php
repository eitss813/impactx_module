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
class Sitemember_Widget_SlideshowSitememberController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $this->view->settings = Engine_Api::_()->getApi('settings', 'core');
        if ($this->_getParam('is_ajax_load', false)) {
            $this->view->is_ajax_load = true;
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
        $params['limit'] = $this->_getParam('itemCount', 5);
        $fea_spo = $this->_getParam('fea_spo', '');
        if ($fea_spo == 'featured') {
            $params['featured'] = 1;
        } elseif ($fea_spo == 'sponsored') {
            $params['sponsored'] = 1;
        } elseif ($fea_spo == 'fea_spo') {
            $params['sponsored'] = 1;
            $params['featured'] = 1;
        }

        $sitemember_members_slideshow = Zend_Registry::isRegistered('sitemember_members_slideshow') ? Zend_Registry::get('sitemember_members_slideshow') : null;
        $this->view->statistics = $params['memberInfo'] = $this->_getParam('memberInfo', array("featuredLabel", "sponsoredLabel", "location", "directionLink", "viewCount", "likeCount", "memberCount", "mutualFriend", "memberStatus", "joined", "networks", "profileField"));
        $this->view->truncation = $params['truncation'] = $this->_getParam('truncation', 16);
        $params['interval'] = $params['interval'] = $this->_getParam('interval', 'overall');
        $params['has_photo'] = $params['has_photo'] = $this->_getParam('has_photo', 1);
        $this->view->circularImage = $params['circularImage'] = $this->_getParam('circularImage', 0);
        $this->view->customParams = $params['customParams'] = $this->_getParam('customParams', 5);
        $this->view->showTitle = $params['showTitle'] = $this->_getParam('showTitle', 1);
        $this->view->custom_field_title = $params['custom_field_title'] = $this->_getParam('custom_field_title', 0);
        $this->view->custom_field_heading = $params['custom_field_heading'] = $this->_getParam('custom_field_heading', 0);

        $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
        if ($this->view->detactLocation) {
            $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            $params['latitude'] = $this->_getParam('latitude', 0);
            $params['longitude'] = $this->_getParam('longitude', 0);
        }
        $this->view->identity = $params['identity'] = $this->_getParam('identity', $this->view->identity);

        $this->view->params = $params;

        if (!$this->view->is_ajax_load)
            return;

        //GET MEMBERS
        $this->view->show_slideshow_object = $paginator = Engine_Api::_()->sitemember()->getUsersSelect($params);

        $totalCount = $paginator->getTotalItemCount();

        $this->view->num_of_slideshow = $totalCount > $params['limit'] ? $params['limit'] : $totalCount;

        if (empty($sitemember_members_slideshow))
            $this->view->setNoRender();

        if (($this->view->num_of_slideshow <= 0)) {
            return $this->setNoRender();
        }
    }

}
