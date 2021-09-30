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
class Sitemember_Widget_ajaxCarouselSitememberController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

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

        $values = array();
        $this->view->vertical = $values['viewType'] = $this->_getParam('viewType', 0);
        $this->view->showPagination = $values['showPagination'] = $this->_getParam('showPagination', 1);
        $this->view->interval = $values['interval'] = $this->_getParam('interval', 300);
        $this->view->blockHeight = $values['blockHeight'] = $this->_getParam('blockHeight', 240);
        $this->view->blockWidth = $values['blockWidth'] = $this->_getParam('blockWidth', 150);
        $this->view->showOptions = $values['memberInfo'] = $this->_getParam('memberInfo', array("featuredLabel", "sponsoredLabel", "location", "directionLink", "viewCount", "likeCount", "memberCount", "mutualFriend", "memberStatus", "joined", "networks", "profileField"));
        $this->view->title_truncation = $values['truncation'] = $this->_getParam('truncation', 50);
        $this->view->limit = $values['limit'] = $this->_getParam('itemCount', 5);
        $this->view->orderby = $values['orderby'] = $this->_getParam('orderby', 'creation_date');
        $this->view->has_photo = $values['has_photo'] = $this->_getParam('has_photo', 1);
        $this->view->titlePosition = $values['titlePosition'] = $this->_getParam('titlePosition', 1);
        $this->view->customParams = $values['customParams'] = $this->_getParam('customParams', 5);
        $this->view->custom_field_title = $values['custom_field_title'] = $this->_getParam('custom_field_title', 0);
        $this->view->custom_field_heading = $values['custom_field_heading'] = $this->_getParam('custom_field_heading', 0);
        $this->view->links = $values['links'] = $this->_getParam('links', array("addfriend", "message"));
        $this->view->settings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->circularImage = $values['circularImage'] = $this->_getParam('circularImage', 0);
        $this->view->circularImageHeight = $values['circularImageHeight'] = $this->_getParam('circularImageHeight', 190);
        
        $sitemember_ajax_carousel = Zend_Registry::isRegistered('sitemember_ajax_carousel') ? Zend_Registry::get('sitemember_ajax_carousel') : null;

        $this->view->fea_spo = $fea_spo = $this->_getParam('fea_spo', null);
        if ($fea_spo == 'featured') {
            $values['featured'] = 1;
        } elseif ($fea_spo == 'sponsored') {
            $values['sponsored'] = 1;
        } elseif ($fea_spo == 'fea_spo') {
            $values['sponsored'] = 1;
            $values['featured'] = 1;
        }

        $this->view->detactLocation = $values['detactLocation'] = $this->_getParam('detactLocation', 0);
        if ($this->view->detactLocation) {
            $this->view->defaultLocationDistance = $values['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            $this->view->latitude = $values['latitude'] = $this->_getParam('latitude', 0);
            $this->view->longitude = $values['longitude'] = $this->_getParam('longitude', 0);
        }

        $this->view->params = $values;

        if (!$this->view->is_ajax_load) {
            return;
        }
        $this->view->members = $paginator = Engine_Api::_()->sitemember()->getUsersSelect($values);
        $this->view->totalCount = $paginator->getTotalItemCount();
        if (empty($sitemember_ajax_carousel) || ($this->view->totalCount <= 0)) {
            return $this->setNoRender();
        }
    }

}
