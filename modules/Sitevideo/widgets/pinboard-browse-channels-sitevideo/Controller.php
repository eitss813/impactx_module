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
class Sitevideo_Widget_pinboardBrowseChannelsSitevideoController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1)) {
            return $this->setNoRender();
        }
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->params = array_merge($this->_getAllParams(), $request->getParams());
        if (!isset($this->view->params['noOfTimes']) || empty($this->view->params['noOfTimes']))
            $this->view->params['noOfTimes'] = 1000;

        if ($this->_getParam('autoload', true)) {
            $this->view->autoload = true;

            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->autoload = false;
                if ($this->_getParam('contentpage', 1) > 1)
                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            } else {
                //  $this->view->layoutColumn = $this->_getParam('layoutColumn', 'middle');
                $this->getElement()->removeDecorator('Title');
                //return;
            }
        } else {
            $this->view->is_ajax_load = $this->_getParam('is_ajax_load', false);
            if ($this->_getParam('contentpage', 1) > 1) {
                $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            }
        }
        $params = $this->view->params;
        $sitevideoChannelList = Zend_Registry::isRegistered('sitevideoChannelList') ? Zend_Registry::get('sitevideoChannelList') : null;
        $this->view->channelOption = $params['channelOption'] = $this->_getParam('channelOption');
        $this->view->itemCountPerPage = $params['itemCountPerPage'] = $this->_getParam('itemCountPerPage', 12);
        
        $params['itemCount'] = $params['itemCountPerPage'];
        
        $this->view->titleTruncation = $params['titleTruncation'] = $this->_getParam('titleTruncation', 100);
        $this->view->descriptionTruncation = $params['descriptionTruncation'] = $this->_getParam('descriptionTruncation', 200);
        $this->view->userComment = $this->_getParam('userComment', 1);
        $this->view->defaultLoadingImage = $params['defaultLoadingImage'] = $this->_getParam('defaultLoadingImage', 1);
        $this->view->itemWidth = $params['itemWidth'] = $this->_getParam('itemWidth', 237);
        $this->view->withoutStretch = $params['withoutStretch'] = $this->_getParam('withoutStretch', 1);
        if(empty($sitevideoChannelList))
            return $this->setNoRender();
        if (!isset($params['category_id']))
            $params['category_id'] = 0;

        if (!isset($params['subcategory_id']))
            $params['subcategory_id'] = 0;

        if (empty($params['category_id'])) {
            $this->view->category_id = $params['category_id'] = $this->_getParam('category_id');
            $params['subcategory_id'] = $this->_getParam('subcategory_id');
        }

        if (empty($this->view->channelOption) || !is_array($this->view->channelOption)) {
            $this->view->channelOption = $params['channelOption'] = array();
        }

        //GET CATEGORYID AND SUBCATEGORYID
        $this->view->categoryName = '';
        if ($this->view->category_id) {
            $this->view->categoryName = $params['categoryname'] = Engine_Api::_()->getItem('sitevideo_channel_category', $this->view->category_id)->category_name;

            if ($this->view->subcategory_id) {
                $this->view->subCategoryName = $params['subcategoryname'] = Engine_Api::_()->getItem('sitevideo_channel_category', $this->view->subcategory_id)->category_name;
            }
        }

        //FORM GENERATION
        $form = new Sitevideo_Form_Search_Search();
        $this->view->params = $params;

        if (!empty($params)) {
            $form->populate($params);
        }

        $this->view->formValues = $form->getValues();
        $params = array_merge($params, $form->getValues());
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
        $customFieldValues = array();
        //CUSTOM FIELD WORK
        $customFieldValues = array_intersect_key($params, $form->getFieldElements());
        $params['type'] = 'browse';
        $params['orderby'] = $orderBy = $request->getParam('orderby', null);

        if (empty($orderBy)) {
            $orderby = $this->_getParam('orderby', 'creation_date');
            if ($orderby == 'creationDate')
                $params['orderby'] = 'creation_date';
            else
                $params['orderby'] = $orderby;
        }

        $requestedAllParams = $this->_getAllParams();

        if (isset($requestedAllParams['hidden_category_id']) && !empty($requestedAllParams['hidden_category_id'])) {
            $this->view->category_id = $params['category_id'] = $this->_getParam('hidden_category_id');
            $this->view->subcategory_id = $params['subcategory_id'] = $this->_getParam('hidden_subcategory_id');
            $this->view->subsubcategory_id = $params['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id');
        }

        $videoSize = array();
        $videoSize['thumb.normal'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('normal.video.width', 375);
        $videoSize['thumb.large'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('normallarge.video.width', 720);
        $videoSize['thumb.main'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('main.video.height', 1600);
        $videoSize['width'] = $this->view->itemWidth;
        $thumbnailType = Engine_Api::_()->getApi('core', 'sitevideo')->findThumbnailType($videoSize, $this->view->itemWidth);
        $this->view->thumbnailType = $params['thumbnailType'] = $thumbnailType;
        $this->view->params = $params;
        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('channels', 'sitevideo')->getChannelPaginator($params, $customFieldValues);
        $this->view->totalCount = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage($params['itemCountPerPage']);
        $paginator->setCurrentPageNumber($this->_getParam('contentpage', 1));
        $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('sitevideo_channel', null, 'create');
        $this->view->countPage = $paginator->count();

        if ($this->view->params['noOfTimes'] > $this->view->countPage)
            $this->view->params['noOfTimes'] = $this->view->countPage;

        $this->view->show_buttons = $this->_getParam('show_buttons', array("favourite", "linkedin", "googleplus", "comment", "like", 'facebook', 'twitter', 'pinit'));
    }

}
