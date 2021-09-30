<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Widget_MembersTestController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        $this->view->defaultLoadingImage = $this->_getParam('defaultLoadingImage', 0);

        if (isset($params['is_ajax_load']))
            unset($params['is_ajax_load']);

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
                $this->view->is_ajax_load = $this->_getParam('loaded_by_ajax', false);
            }
        }
        if (Engine_Api::_()->seaocore()->isSitemobileApp() && $this->_getParam('ajax', false)) {
            if ($this->_getParam('page', 1) > 1)
                $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }

        if (empty($this->view->is_ajax_load)) {
            $cookieLocation = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
            if (isset($cookieLocation['location']) && !empty($cookieLocation['location'])) {
                $this->view->is_ajax_load = 1;
            }
        }

        //GET VIEWER DETAILS
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        //GET PINBOARD SETTING
        $params = array();
        $this->view->show_buttons = $this->_getParam('show_buttons', array('facebook', 'twitter', 'pinit'));
        $this->view->pinboarditemWidth = $this->_getParam('pinboarditemWidth', 237);
        $this->view->withoutStretch = $this->_getParam('withoutStretch', 1);
        //END PINBOARD WORK

        $this->view->memberfoundshow = strpos($_SERVER['REQUEST_URI'], '?');
        $this->view->showContent = $this->_getParam('show_content', 3);
        $this->view->titlePosition = $this->_getParam('titlePosition', 1);
        $this->view->showDetailLink = $this->_getParam('showDetailLink', 1);
        $this->view->allParams = $this->_getAllParams();
        $this->view->identity = $this->view->allParams['identity'] = $this->_getParam('identity', $this->view->identity);
        $this->view->ShowViewArray = $ShowViewArray = $this->_getParam('layouts_views', array("1", "2", "3", "4"));
        $this->view->viewType = $this->_getParam('viewType', '');
        $this->view->statistics = $this->_getParam('memberInfo', array("featuredLabel", "sponsoredLabel", "location", "directionLink", "viewCount", "likeCount", "memberCount", "mutualFriend", "memberStatus", "joined", "networks", "profileField", "distance"));
        $defaultOrder = $this->_getParam('layouts_order', 2);
        $this->view->links = $this->_getParam('links', array("addfriend", "message"));
        $this->view->customParams = $this->_getParam('customParams', 5);
        $this->view->custom_field_title = $this->_getParam('custom_field_title', 5);
        $this->view->custom_field_heading = $this->_getParam('custom_field_heading', 5);
        $this->view->circularImage = $params['circularImage'] = $this->_getParam('circularImage', 0);
        $this->view->circularImageHeight = $params['circularImageHeight'] = $this->_getParam('circularImageHeight', 190);
        $this->view->commonColumnHeight = $params['commonColumnHeight'] = $this->_getParam('commonColumnHeight', 240);
        $this->view->listFullWidthElement = $params['listFullWidthElement'] = $this->_getParam('listFullWidthElement', 0);
        $this->view->circularPinboardImageHeight = $params['circularPinboardImageHeight'] = $this->_getParam('circularPinboardImageHeight', 208);

        
        // FOR MAP VIEW
        $this->view->enableBounce = $this->_getParam('sitemember_map_sponsored', 1);
        $sitemember_browse_member = Zend_Registry::isRegistered('sitemember_browse_member') ? Zend_Registry::get('sitemember_browse_member') : null;

        if (empty($this->view->viewType)) {
            if ($defaultOrder == 1)
                $this->view->viewType = 'listview';
            elseif ($defaultOrder == 2)
                $this->view->viewType = 'gridview';
            elseif ($defaultOrder == 3)
                $this->view->viewType = 'mapview';
            else
                $this->view->viewType = 'pinboardview';
        }

        $this->view->title_truncation = $this->_getParam('truncation', 16);
        $this->view->title_truncationGrid = $this->_getParam('truncationGrid', 16);
        $this->view->list_view = 0;
        $this->view->grid_view = 0;
        $this->view->map_view = 0;
        $this->view->pinboard_view = 0;
        $this->view->defaultView = -1;

        if (empty($sitemember_browse_member)) {
            return $this->setNoRender();
        }

        if (in_array("1", $ShowViewArray)) {
            $this->view->list_view = 1;
            if ($this->view->defaultView == -1 || $defaultOrder == 1)
                $this->view->defaultView = 0;
        }
        if (in_array("2", $ShowViewArray)) {
            $this->view->grid_view = 1;
            if ($this->view->defaultView == -1 || $defaultOrder == 2)
                $this->view->defaultView = 1;
        }
        if (in_array("3", $ShowViewArray)) {
            $this->view->map_view = 1;
            if ($this->view->defaultView == -1 || $defaultOrder == 3)
                $this->view->defaultView = 2;
        }
        if (in_array("4", $ShowViewArray)) {
            $this->view->pinboard_view = 1;
            if ($this->view->defaultView == -1 || $defaultOrder == 4)
                $this->view->defaultView = 3;
        }
        if ($this->view->defaultView == -1) {
            return $this->setNoRender();
        }

        $customFieldValues = array();
        $values = array();

        $request = Zend_Controller_Front::getInstance()->getRequest();

        $this->view->params = $params = $request->getParams();
        $params['has_photo'] = $this->_getParam('has_photo', 1);

        $page = 1;
        if (isset($params['page']) && !empty($params['page'])) {
            $page = $params['page'];
        }

        //GET VALUE BY POST TO GET DESIRED MEMBERS
        if (!empty($params)) {
            $values = array_merge($values, $params);
        }

        //FORM GENERATION
        $form = new Sitemember_Form_Search();

        if (!empty($params)) {
            $form->populate($params);
        }

        $values = array_merge($values, $form->getValues());

        $values['page'] = $page;

        //GET LISITNG FPR PUBLIC PAGE SET VALUE
        if (@$values['show'] == 2) {

            //GET AN ARRAY OF FRIEND IDS
            $friends = $viewer->membership()->getMembers();
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }
            $values['users'] = $ids;
        }

        $this->view->assign($values);

        //CORE API
        $this->view->settings = Engine_Api::_()->getApi('settings', 'core');

        //CUSTOM FIELD WORK
        $customFieldValues = array_intersect_key($values, $form->getFieldElements());
        if ($form->show->getValue() == 3 && !isset($_GET['show'])) {
            @$values['show'] = 3;
        }

        $values['orderby'] = $orderby = $request->getParam('orderby', null);
        if (empty($orderby)) {
            $orderby = $this->_getParam('orderby', 'creation_date');
            if ($orderby == 'creationDate')
                $values['orderby'] = 'creation_date';
            elseif ($orderby == 'viewCount')
                $values['orderby'] = 'view_count';
            else
                $values['orderby'] = $orderby;
        }
        $this->view->orderby = $orderby;

        $values['limit'] = $itemCount = $this->_getParam('itemCount', 10);

        if (count($ShowViewArray) == 1) {
            if (in_array("3", $ShowViewArray) && $defaultOrder == 3) {
                $values['seaolocation_id'] = 1;
            }
        }

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $this->view->text_search = $_GET['search'];
        }

        if ($request->getParam('titleAjax')) {
            $values['search'] = $request->getParam('titleAjax');
        }

        $this->view->detactLocation = $values['detactLocation'] = $this->_getParam('detactLocation', 0);
        if ($this->view->detactLocation) {
            $this->view->defaultLocationDistance = $values['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            $values['latitude'] = $this->_getParam('latitude', 0);
            $values['longitude'] = $this->_getParam('longitude', 0);
        }

        if (!$this->view->detactLocation && empty($_GET['location']) && isset($values['location'])) {
            unset($values['location']);

            if (empty($_GET['latitude']) && isset($values['latitude'])) {
                unset($values['latitude']);
            }

            if (empty($_GET['longitude']) && isset($values['longitude'])) {
                unset($values['longitude']);
            }

            if (empty($_GET['Latitude']) && isset($values['Latitude'])) {
                unset($values['Latitude']);
            }

            if (empty($_GET['Longitude']) && isset($values['Longitude'])) {
                unset($values['Longitude']);
            }
        }

        // GET MEMBERS
        $this->view->paginator = $paginator = Engine_Api::_()->sitemember()->getUsersSelect($values, $customFieldValues);
        $paginator->setItemCountPerPage($itemCount);
        $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);
        $this->view->totalResults = $paginator->getTotalItemCount();

        $this->view->flageSponsored = 0;
        $this->view->totalCount = $paginator->getTotalItemCount();
        if ($paginator->getTotalItemCount() > 0) {
            $ids = array();
            foreach ($paginator as $user) {
                $id = $user->seao_locationid;
                $ids[] = $id;
                $user_temp[$id] = $user;
            }
            $values['user_ids'] = $ids;

            $this->view->locations = $locations = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocations($values);

            foreach ($locations as $location) {
                if ($user_temp[$location->locationitem_id]->sponsored) {
                    $this->view->flageSponsored = 1;
                    break;
                }
            }
            $this->view->sitemember = $user_temp;
        }

        $this->view->search = 0;
        if (!empty($this->_getAllParams) && Count($this->_getAllParams) > 1) {
            $this->view->search = 1;
        }

        $this->view->is_ajax = $this->_getParam('is_ajax', 0);
        $this->view->columnWidth = $this->_getParam('columnWidth', '180');
        $this->view->columnHeight = $this->_getParam('columnHeight', '328');
        $this->view->viewmore = $this->_getParam('viewmore', false);

        $this->view->paramsLocation = array_merge($_GET, $this->_getAllParams());
        $this->view->paramsLocation = array_merge($request->getParams(), $this->view->paramsLocation);

        if (isset($_GET['search']) || isset($_POST['search'])) {
            $this->view->detactLocation = 0;
        } else {
            $this->view->detactLocation = $this->_getParam('detactLocation', 0);
        }

        //Sitemobile code
        if (isset($values['formatType'])) {
            unset($values['formatType']);
        }
        $this->view->formValues = $values;

        //SCROLLING PARAMETERS SEND
        if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
            //SET SCROLLING PARAMETTER FOR AUTO LOADING.
            if (!Zend_Registry::isRegistered('scrollAutoloading')) {
                Zend_Registry::set('scrollAutoloading', array('scrollingType' => 'up'));
            }
        }

        $this->view->page = $this->_getParam('page', 1);
        $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);
        $this->view->totalPages = ceil(($this->view->totalCount) / $itemCount);
        //END - SCROLLING WORK
        //Finish
    }

}
