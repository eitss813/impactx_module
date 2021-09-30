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
class Sitevideo_Widget_mySubscriptionsSitevideoController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();

        //FIND THE PAGE NUMBER
        if (isset($params['page']) && !empty($params['page']))
            $this->view->page = $page = $params['page'];
        else
            $this->view->page = $page = 1;

        $this->view->form = $form = new Sitevideo_Form_SearchChannel();
        $values = $form->getValues();
        $params['search'] = $values['search'];
        // ASSIGNING THE PARAMETER
        $sitevideoVideosList = Zend_Registry::isRegistered('sitevideoVideosList') ? Zend_Registry::get('sitevideoVideosList') : null;
        if(empty($sitevideoVideosList))
            return $this->setNoRender();
        $this->view->is_ajax_load = $this->_getParam('loaded_by_ajax', false);
        $this->view->is_ajax = $this->_getParam('isajax', '');
        $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);
        $params['itemCountPerPage'] = $this->_getParam('itemCountPerPage', 12);
        $this->view->orderBy = $params['orderBy'] = $this->_getParam('orderBy');
        $params['owner'] = Engine_Api::_()->user()->getViewer();
        $params['owner_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->view->params = $params;
        $this->view->showEditDeleteOption = false;
        $params['paginator'] = 1;
        //FIND THE PLAYLIST MODELS
        $paginator = $this->view->paginator = Engine_Api::_()->getDbTable('subscriptions', 'sitevideo')->getSubscriptionPaginator($params);
        //FIND TOTAL NO. OF RECORDS
        $this->view->totalCount = $paginator->getTotalItemCount();
        //SET THE NO. OF RECORDS PER PAGE
        $paginator->setItemCountPerPage($params['itemCountPerPage']);
        //SET THE CURRENT PAGE NO.
        $paginator->setCurrentPageNumber($page);
        $this->view->totalPlaylists = $paginator->getTotalItemCount();
        $this->view->totalPages = ceil(($this->view->totalPlaylists) / $params['itemCountPerPage']);
    }

}
