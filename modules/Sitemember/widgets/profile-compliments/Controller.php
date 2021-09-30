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
class Sitemember_Widget_ProfileComplimentsController extends Seaocore_Content_Widget_Abstract {
    protected $_childCount;
    public function indexAction() {
        
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
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
         
        //GET VIEWER DETAILS
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        //GET PINBOARD SETTING
        
        $this->view->memberfoundshow = strpos($_SERVER['REQUEST_URI'], '?');
        $this->view->showContent = $this->_getParam('show_content', 3);
        $this->view->allParams = $this->_getAllParams();
        $this->view->identity = $this->view->allParams['identity'] = $this->_getParam('identity', $this->view->identity);
        
        $this->view->statistics = array("memberCount", "mutualFriend","verifyLabel");
        
        $this->view->links = $this->_getParam('links', array("addfriend", "message"));
        $this->view->circularImage = $params['circularImage'] = $this->_getParam('circularImage', 0);
        $this->view->circularImageHeight = $params['circularImageHeight'] = $this->_getParam('circularImageHeight', 190);
        $this->view->commonColumnHeight = $params['commonColumnHeight'] = $this->_getParam('commonColumnHeight', 240);
        $values = array();
        $request = Zend_Controller_Front::getInstance()->getRequest();

        $this->view->params = $params = $request->getParams();
        $params = array_merge($params, array("resource_type" => $subject->getType(),"resource_id"=> $subject->getIdentity()));
       
        $page = 1;
        if (isset($params['page']) && !empty($params['page'])) {
            $page = $params['page'];
        }
        //GET VALUE BY POST TO GET DESIRED MEMBERS
        if (!empty($params)) {
            $values = array_merge($values, $params);
        }

        $values['page'] = $page;
        //CORE API
        $this->view->settings = Engine_Api::_()->getApi('settings', 'core');

        $values['limit'] = $itemCount = $this->_getParam('itemCount', 10);
        
        // GET MEMBERS 
        $complimentSelect = Engine_Api::_()->getDbTable('compliments','sitemember')->getCompliments($params);
        $this->view->paginator = $paginator = Zend_Paginator::factory($complimentSelect);
        $paginator->setItemCountPerPage($itemCount); 
        $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);
        $this->view->totalResults = $paginator->getTotalItemCount();
        $this->view->totalCount = $paginator->getTotalItemCount();
        $this->_childCount = $paginator->getTotalItemCount();;
        $this->view->search = 0;
        if (!empty($this->_getAllParams) && Count($this->_getAllParams) > 1) {
            $this->view->search = 1;
        }
        $this->view->is_ajax = $this->_getParam('is_ajax', 0);
        $this->view->columnWidth = $this->_getParam('columnWidth', '180');
        $this->view->columnHeight = $this->_getParam('columnHeight', '328');
        $this->view->viewmore = $this->_getParam('viewmore', false);
        $this->view->formValues = $values;
        $this->view->page = $this->_getParam('page', 1);
        $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);
        $this->view->totalPages = ceil(($this->view->totalCount) / $itemCount);
        
    }
    public function getChildCount() {
        return $this->_childCount;
    }

}
