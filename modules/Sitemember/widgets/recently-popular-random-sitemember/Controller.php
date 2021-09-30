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
class Sitemember_Widget_RecentlyPopularRandomSitememberController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        if ($this->_getParam('is_ajax_load', false)) {
            $this->view->is_ajax_load = true;
            if (!$this->_getParam('detactLocation', 0))
                $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        } else {
            if ($this->_getParam('detactLocation', 0))
                $this->getElement()->removeDecorator('Title');
            $this->view->is_ajax_load = $this->_getParam('loaded_by_ajax', false);
        }

        $this->view->showContent = $this->_getParam('show_content', 2);
        $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->view->titlePosition = $this->_getParam('titlePosition', 1);
        $this->view->showDetailLink = $this->_getParam('showDetailLink', 1);
        $this->view->params = $params = $this->_getAllParams();
        $params['limit'] = $this->_getParam('limit', 12);
        $this->view->statistics = $this->_getParam('memberInfo', array("featuredLabel", "sponsoredLabel", "location", "directionLink", "viewCount", "likeCount", "memberCount", "mutualFriend", "memberStatus", "joined", "networks", "profileField"));
        $this->view->links = $this->_getParam('links', array("addfriend", "message"));
        $params['has_photo'] = $this->_getParam('has_photo', 1);
        $this->view->circularImage = $params['circularImage'] = $this->_getParam('circularImage', 0);
        $this->view->circularImageHeight = $params['circularImageHeight'] = $this->_getParam('circularImageHeight', 190);
        $this->view->commonColumnHeight = $params['commonColumnHeight'] = $this->_getParam('commonColumnHeight', 240);
        $this->view->listFullWidthElement = $params['listFullWidthElement'] = $this->_getParam('listFullWidthElement', 0);

        //GET CORE API
        $this->view->settings = Engine_Api::_()->getApi('settings', 'core');

        $this->view->is_ajax = $isAjax = $this->_getParam('is_ajax', 0);
        if (empty($isAjax)) {
            $showTabArray = $params['ajaxTabs'] = $this->_getParam('ajaxTabs', array("most_recent", "most_viewed", "most_popular", "most_liked", "featured", "sponsored", "this_month", "this_week", "today"));

            if ($showTabArray) {
                foreach ($showTabArray as $key => $value)
                    $showTabArray[$key] = str_replace("ZZZ", "_", $value);
            } else {
                $showTabArray = array();
            }

            $this->view->tabs = $showTabArray;
            $this->view->tabCount = count($showTabArray);
            if (empty($this->view->tabCount)) {
                return $this->setNoRender();
            }
            $this->view->tabs = $showTabArray = $this->setTabsOrder($showTabArray);
        } else {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }

        $layouts_views = $this->_getParam('layouts_views', array("list_view", "grid_view", "map_view"));

        foreach ($layouts_views as $key => $value)
            $layouts_views[$key] = str_replace("ZZZ", "_", $value);

        $this->view->layouts_views = $layouts_views;
        $this->view->defaultLayout = str_replace("ZZZ", "_", $this->_getParam('defaultOrder', 'list_view'));

        $paramsContentType = $this->_getParam('content_type', null);
        $this->view->content_type = $paramsContentType = $paramsContentType ? $paramsContentType : $showTabArray[0];

        $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
        if ($this->view->detactLocation) {
            $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            $params['latitude'] = $this->_getParam('latitude', 0);
            $params['longitude'] = $this->_getParam('longitude', 0);
        }

        if (!$this->view->is_ajax_load)
            return;

        $params['contentType'] = $contentType = Zend_Controller_Front::getInstance()->getRequest()->getParam('contentType', null);
        if (empty($contentType)) {
            $params['contentType'] = $this->_getParam('contentType', 'All');
        }
        $this->view->contentType = $params['contentType'];

        switch ($paramsContentType) {
            case 'most_recent':
                $params['orderby'] = 'creation_date';
                break;
            case 'most_popular':
                $params['orderby'] = "member_count";
                break;
            case 'most_viewed':
                $params['orderby'] = "view_count";
                break;
            case 'most_liked':
                $params['orderby'] = "like_count";
                break;
            case 'featured':
                $params['featured'] = 1;
                break;
            case 'sponsored':
                $params['sponsored'] = 1;
                break;
            default:
                $params['orderby'] = $paramsContentType;
                break;
        }

        $this->view->paginator = $paginator = Engine_Api::_()->sitemember()->getUsersSelect($params, '');
        $this->view->totalCount = $paginator->getTotalItemCount();

        $this->view->locations = array();
        if (in_array('map_view', $layouts_views)) {
            $this->view->enableLocation = $checkLocation = 1;
            if ($checkLocation) {
                $user_ids = array();
                $locationMember = array();
                $this->view->flagSponsored = $this->_getParam('sitemember_map_sponsored', 1);
                foreach ($paginator as $item) {
                    if ($item->location) {
                        $user_ids[] = $item->seao_locationid;
                        $locationMember[$item->seao_locationid] = $item;
                    }
                }
                if (count($user_ids) > 0) {
                    $values['user_ids'] = $user_ids;
                    $this->view->locations = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocations($values);
                    $this->view->locationsMember = $locationMember;
                }
            } else {
                unset($layouts_views[array_search('map_view', $layouts_views)]);
                $this->view->layouts_views = $layouts_views;
            }
        }
        if (!$this->view->enableLocation && Count($layouts_views) == 1 && in_array('map_view', $layouts_views)) {
            return $this->setNoRender();
        }
        $this->view->columnWidth = $this->_getParam('columnWidth', '180');
        $this->view->columnHeight = $this->_getParam('columnHeight', '328');
        $this->view->title_truncationList = $this->_getParam('truncationList', 600);
        $this->view->title_truncationGrid = $this->_getParam('truncationGrid', 90);
        $this->view->listViewType = $this->_getParam('listViewType', 'list');
        $this->view->customParams = $this->_getParam('customParams', 5);
        $this->view->custom_field_title = $this->_getParam('custom_field_title', 0);
        $this->view->custom_field_heading = $this->_getParam('custom_field_heading', 0);
        if (isset($values) && $params && $values)
            $this->view->paramsLocation = array_merge($params, $values);
    }

    public function setTabsOrder($tabs) {

        $tabsOrder['most_recent'] = $this->_getParam('upcoming_order', 1);
        $tabsOrder['most_viewed'] = $this->_getParam('views_order', 2);
        $tabsOrder['most_popular'] = $this->_getParam('popular_order', 3);
        $tabsOrder['most_like'] = $this->_getParam('like_order', 4);
        $tabsOrder['featured'] = $this->_getParam('featured_order', 4);
        $tabsOrder['sponsored'] = $this->_getParam('sponosred_order', 5);
        $tabsOrder['this_month'] = $this->_getParam('month_order', 7);
        $tabsOrder['this_week'] = $this->_getParam('week_order', 8);
        $tabsOrder['today'] = $this->_getParam('today_order', 9);

        $tempTabs = array();
        foreach ($tabs as $tab) {
            $order = $tabsOrder[$tab];
            if (isset($tempTabs[$order]))
                $order++;
            $tempTabs[$order] = $tab;
        }
        ksort($tempTabs);
        $orderTabs = array();
        $i = 0;
        foreach ($tempTabs as $tab)
            $orderTabs[$i++] = $tab;
        return $orderTabs;
    }

}
