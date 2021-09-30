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
class Sitevideo_Widget_OverviewChannelController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitevideo_channel')) {
            return $this->setNoRender();
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.channel.allow', 1)) {
            return $this->setNoRender();
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$viewer->getIdentity()) {
            return $this->setNoRender();
        }

        //GET CHANNEL SUBJECT
        $this->view->channel = $channel = Engine_Api::_()->core()->getSubject('sitevideo_channel');
        $params = array();
        $params['resource_id'] = $channel->channel_id;
        $params['resource_type'] = $channel->getType();
        $params = $this->_getAllParams();
        $this->view->params = $params;
        $this->view->showContent = false;

        if ($this->_getParam('loaded_by_ajax', false)) {
            $this->view->loaded_by_ajax = true;

            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->loaded_by_ajax = false;
                if (!$this->_getParam('onloadAdd', false))
                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');

                $this->view->showContent = true;
            }
        } else {
            $this->view->showContent = true;
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.overview', 1)) {
            return $this->setNoRender();
        }
        //GET VIEWER DETAIL
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitevideo');
        $this->view->overview = $overview = $tableOtherinfo->getColumnValue($channel->getIdentity(), 'overview');

        if (empty($overview) && !$channel->authorization()->isAllowed($viewer, 'edit')) {
            return $this->setNoRender();
        }

        if (empty($overview) && !$channel->authorization()->isAllowed($viewer, 'overview')) {
            return $this->setNoRender();
        }
    }

}
