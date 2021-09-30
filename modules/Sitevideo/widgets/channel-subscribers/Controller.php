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
class Sitevideo_Widget_ChannelSubscribersController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitevideo_channel')) {
            return $this->setNoRender();
        }
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1)) {
            return $this->setNoRender();
        }
        //GET CHANNEL SUBJECT
        $this->view->channel = $channel = Engine_Api::_()->core()->getSubject('sitevideo_channel');
        $this->view->width = $this->_getParam('width', 150);
        $this->view->height = $this->_getParam('height', 150);
//GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        //GET PAGINATOR
        $params = array();
        $params['channel_id'] = $channel->channel_id;
        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('channels', 'sitevideo')->getSubscribedChannelPaginator($params);
        $this->view->total_subscribers = $total_subscribers = $paginator->getTotalItemCount();

        //ADD COUNT TO TITLE
        if ($this->_getParam('titleCount', false) && $total_subscribers > 0) {
            $this->_childCount = $total_subscribers;
        }

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

        $this->view->itemCount = $this->_getParam('itemCount', 20);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($this->view->itemCount);
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}
