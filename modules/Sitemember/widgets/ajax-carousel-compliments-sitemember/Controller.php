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
class Sitemember_Widget_ajaxCarouselComplimentsSitememberController extends Engine_Content_Widget_Abstract {

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
        $this->view->itemViewType =  $this->_getParam('itemViewType', 0);
        $this->view->showPagination = $values['showPagination'] = $this->_getParam('showPagination', 1);
        $this->view->interval = $values['interval'] = $this->_getParam('interval', 300);
        $this->view->blockHeight = $values['blockHeight'] = $this->_getParam('blockHeight', 240);
        $this->view->blockWidth = $values['blockWidth'] = $this->_getParam('blockWidth', 150);
        $this->view->showOptions = $values['memberInfo'] = $this->_getParam('memberInfo', array("featuredLabel", "sponsoredLabel", "location", "directionLink", "viewCount", "likeCount", "memberCount", "mutualFriend", "memberStatus", "joined", "networks", "profileField"));
        $this->view->title_truncation = $values['truncation'] = $this->_getParam('truncation', 50);
        $this->view->limit = $values['limit'] = $this->_getParam('itemCount', 5);
        $this->view->has_photo = $values['has_photo'] = $this->_getParam('has_photo', 1);
        $this->view->titlePosition = $values['titlePosition'] = $this->_getParam('titlePosition', 1);
        $this->view->customParams = $values['customParams'] = $this->_getParam('customParams', 5);
        $this->view->links = $values['links'] = $this->_getParam('links', array("addfriend", "message"));
        $this->view->settings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->circularImage = $values['circularImage'] = $this->_getParam('circularImage', 0);
        $this->view->circularImageHeight = $values['circularImageHeight'] = $this->_getParam('circularImageHeight', 190);

        
        $this->view->compliment_category = $compliment_category = $this->_getParam('compliment_category');
        $this->view->params = $values;
        $this->view->title =$title =  $this->_getParam('title', null);
        $this->view->complimentTable = Engine_Api::_()->getDbTable('compliments','sitemember');
        $this->view->complimentItem = $complimentItem = Engine_Api::_()->getItem("sitemember_compliment_category",$compliment_category);
        if(empty($title) && !empty($complimentItem)){
            $this->view->title = $complimentItem->getTitle();
        }
        
        if (!$this->view->is_ajax_load) {
            return;
        }
        $user_ids = Engine_Api::_()->getDbTable('compliments','sitemember')->getUserIdsByComplimentCategoryId(array("complimentcategory_id"=>$compliment_category));
        if(empty($user_ids)){
            return $this->setNoRender();
        } 
        $values["compliments"] = array_unique($user_ids);
        $this->view->members = $paginator = Engine_Api::_()->sitemember()->getUsersSelect($values);
        $this->view->totalCount = $paginator->getTotalItemCount();
        if (($this->view->totalCount <= 0)) {
            return $this->setNoRender();
        }
    }

}
