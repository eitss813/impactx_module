<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Widget_ProjectBackersController extends Engine_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        //DONT RENDER IF VEWER IS EMPTY
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if (empty($viewer_id)) {
            //return $this->setNoRender();
        }
        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        //GET LIST SUBJECT
        $subject = Engine_Api::_()->core()->getSubject();

        if(!$subject->isFundingApproved()){
            return $this->setNoRender();
        }

        $this->view->resource_type = $resource_type = $subject->getType();
        $this->view->resource_id = $resource_id = $subject->getIdentity();
        $this->view->height = $height = $this->_getParam('height', 100);
        $this->view->width = $width = $this->_getParam('width', 80);

        $this->view->options = $options = $this->_getParam('options');
        if (empty($this->view->options) || !is_array($this->view->options))
            $this->view->options = $options = array();
        $backersTable = Engine_Api::_()->getDbtable('backers', 'sitecrowdfunding');
        $params = array();
        $params['project_id'] = $resource_id;
        $this->view->paginator = $paginator = $backersTable->getBackersPaginator($params);
        $this->view->backersCount = $backersCount = $paginator->getTotalItemCount();
        if ($backersCount > 0)
            $this->_childCount = $backersCount;

        $sitecrowdfundingProjectBackers = Zend_Registry::isRegistered('sitecrowdfundingProjectBackers') ? Zend_Registry::get('sitecrowdfundingProjectBackers') : null;
        $params = $this->_getAllParams();
        $this->view->params = $params;
        $this->view->showContent = true;

        if ($this->_getParam('loaded_by_ajax', false)) {
            $this->view->loaded_by_ajax = true;
            $this->view->showContent = false;

            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->loaded_by_ajax = false;

                if (!$this->_getParam('onloadAdd', false))
                    $this->getElement()->removeDecorator('Title');

                $this->getElement()->removeDecorator('Container');
                $this->view->showContent = true;
            } else {
                return;
            }
        }
        if (empty($sitecrowdfundingProjectBackers))
            return $this->setNoRender();
        $this->view->itemCount = $this->_getParam('itemCount', 8);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($this->view->itemCount);
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}
