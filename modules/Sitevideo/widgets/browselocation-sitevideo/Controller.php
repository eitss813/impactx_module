<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Widget_BrowselocationSitevideoController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->viewType = $this->_getParam('viewType', 'vertical');
        $this->view->advancedSearch = $this->_getParam('advancedSearch', 0);
        $this->view->showAllCategories = $this->_getParam('showAllCategories', 1);
        $this->view->locationDetection = $this->_getParam('locationDetection', 0);
        $sitevideoBrowseLocation = Zend_Registry::isRegistered('sitevideoBrowseLocation') ? Zend_Registry::get('sitevideoBrowseLocation') : null;
        $this->view->videoOption = $this->_getParam('videoOption', array('title', 'owner', 'creationDate', 'view', 'like', 'comment', 'location'));
        $widgetSettings = array(
            'advancedSearch' => $this->view->advancedSearch,
            'showAllCategories' => $this->view->showAllCategories,
            'locationDetection' => $this->view->locationDetection,
        );
        // Make form
        $this->view->form = $form = new Sitevideo_Form_Locationsearch(array('widgetSettings' => $widgetSettings));

        if (!empty($_POST)) {
            $this->view->is_ajax = $_POST['is_ajax'];
        }

        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $action = $front->getRequest()->getActionName();
        $controller = $front->getRequest()->getControllerName();
        if (!($module == 'sitevideo' && $controller == 'video' && $action == 'map') && empty($this->view->is_ajax)) {
            return $this->setNoRender();
        }
        if (empty($sitevideoBrowseLocation)) {
            return $this->setNoRender();
        }

        if (empty($_POST['location'])) {
            $this->view->locationVariable = '1';
        }

        if (empty($_POST['is_ajax'])) {

            $values = $form->getValues();
            $customFieldValues = array_intersect_key($values, $form->getFieldElements());
            $this->view->is_ajax = $this->_getParam('is_ajax', 0);
        } else {
            $values = $_POST;
            $form->isValid($values);
            $parms = $form->getValues();
            $values = array_merge($values, $parms);
            $customFieldValues = array_intersect_key($values, $form->getFieldElements());
        }

        unset($values['or']);
        $this->view->assign($values);
        // FIND USERS' FRIENDS
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!empty($params['view_view']) && $params['view_view'] == 1) {
            //GET AN ARRAY OF FRIEND IDS
            $friends = $viewer->membership()->getMembers();
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }
            $params['users'] = $ids;
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();

        $values['type'] = 'browse';
        $values['type_location'] = 'browseLocation';
        $this->view->truncationLocation = $this->_getParam('truncationLocation', 35);

        if (isset($values['show'])) {
            if ($form->show->getValue() == 3) {
                @$values['show'] = 3;
            }
        }

        if ($request->getParam('page'))
            $this->view->current_page = $page = $request->getParam('page');
        else
            $this->view->current_page = $page = $this->_getParam('page', 1);

        $this->view->enableLocation = $checkLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.location', 0);

        //check for miles or street.
        if (isset($values['locationmiles']) && !empty($values['locationmiles'])) {

            if (isset($values['video_street']) && !empty($values['video_street'])) {
                $values['location'] = $values['video_street'] . ',';
                unset($values['video_street']);
            }

            if (isset($values['video_city']) && !empty($values['video_city'])) {
                $values['location'].= $values['video_city'] . ',';
                unset($values['video_city']);
            }

            if (isset($values['video_state']) && !empty($values['video_state'])) {
                $values['location'].= $values['video_state'] . ',';
                unset($values['video_state']);
            }

            if (isset($values['video_country']) && !empty($values['video_country'])) {
                $values['location'].= $values['video_country'];
                unset($values['video_country']);
            }
        }

        $values['orderby'] = $orderBy = $request->getParam('orderby', null);

        if (empty($orderBy)) {
            $orderby = $this->_getParam('orderby', 'creation_date');
            if ($orderby == 'creationDate')
                $values['orderby'] = 'creation_date';
            else
                $values['orderby'] = $orderby;
        }
        $result = Engine_Api::_()->getDbtable('videos', 'sitevideo')->getVideoSelect($values, $customFieldValues);
        $this->view->paginator = $paginator = Zend_Paginator::factory($result);
        $paginator->setItemCountPerPage(15);
        $this->view->paginator = $paginator->setCurrentPageNumber($page);
        $this->view->totalresults = $paginator->getTotalItemCount();
        $this->view->mobile = Engine_Api::_()->seaocore()->isMobile();
        $this->view->flageSponsored = 0;

        if (!empty($checkLocation) && $paginator->getTotalItemCount() > 0) {

            $ids = array();
            foreach ($paginator as $video) {
                $id = $video->getIdentity();
                $ids[] = $id;
                $video_temp[$id] = $video;
            }
            $values['video_ids'] = $ids;
            $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'sitevideo')->getLocation($values);
            foreach ($locations as $location) {
                if ($video_temp[$location->video_id]->sponsored) {
                    $this->view->flageSponsored = 1;
                    break;
                }
            }

            $this->view->list = $video_temp;
        } else {
            $this->view->enableLocation = 0;
        }

        $this->view->isViewMoreButton = false;
        $this->view->titleTruncation = 100;
        $this->view->descriptionTruncation = 100;
        $this->view->message = "No videos have been posted yet.";
        $this->view->widgetPath = 'widget/index/mod/sitevideo/name/browselocation-sitevideo';
    }

}
