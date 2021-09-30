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
class Sitevideo_Widget_TopContentOfChannelController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        if (!Engine_Api::_()->core()->hasSubject('sitevideo_channel')) {
            return $this->setNoRender();
        }

        $this->view->channel = $channel = Engine_Api::_()->core()->getSubject('sitevideo_channel');
        $this->view->showInformationOptions = $this->_getParam('showInformationOptions', array('likeButton', 'title', 'description', 'owner', 'updateddate', 'facebooklikebutton', 'commentViewEnabled', 'editmenus'));
        $this->view->showLayout = $this->_getParam('showLayout', 'center');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->canEdit = $canComment = $channel->authorization()->isAllowed($viewer, 'edit');
        $this->view->canComment = $canComment = $channel->authorization()->isAllowed($viewer, 'comment');
        $sitevideoChannelList = Zend_Registry::isRegistered('sitevideoChannelList') ? Zend_Registry::get('sitevideoChannelList') : null;
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('channel_profile');
        // Do other stuff
        $this->view->mine = true;
        if (!$channel->getOwner()->isSelf($viewer)) {
            $this->view->mine = false;
        }

        $tableCategory = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo');
        $this->view->category_name = '';

        if(empty($sitevideoChannelList))
            return $this->setNoRender();
        
        if (!empty($channel->category_id)) {
            $this->view->category_name = $tableCategory->getCategory($channel->category_id)->category_name;
        }

        $this->view->sitevideoTags = $channel->tags()->getTagMaps();
        $auth = Engine_Api::_()->authorization()->context;
        $this->view->allowView = $auth->isAllowed($channel, 'registered', 'view');

        if (!empty($viewer_id) && ($viewer->level_id == 1 || $viewer->level_id == 2)) {
            $this->view->allowView = $auth->isAllowed($channel, 'everyone', 'view') === 1 ? true : false || $auth->isAllowed($channel, 'registered', 'view') === 1 ? true : false;
        }
    }

}
