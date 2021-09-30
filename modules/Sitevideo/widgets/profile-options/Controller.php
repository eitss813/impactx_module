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
class Sitevideo_Widget_ProfileOptionsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject('sitevideo_channel') || !$viewer->getIdentity()) {
            return $this->setNoRender();
        }

        // Get subject and check auth
        $sitevideoProfile = Zend_Registry::isRegistered('sitevideoProfile') ? Zend_Registry::get('sitevideoProfile') : null;
        if(empty($sitevideoProfile))
            return $this->setNoRender();
        $this->view->channel = $subject = Engine_Api::_()->core()->getSubject('sitevideo_channel');
        $this->view->navigation = Engine_Api::_()
                ->getApi('menus', 'core')
                ->getNavigation('channel_profile');
    }

}
