<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MobiController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_MobiController extends Core_Controller_Action_Standard {

    public function indexAction() {
        if (!$this->_helper->requireAuth()->setAuthParams('sitevideo_channel', null, 'view')->isValid())
            return;
        $getLightBox = Zend_Registry::isRegistered('sitevideo_getlightbox') ? Zend_Registry::get('sitevideo_getlightbox') : null;
        if (empty($getLightBox)) {
            return;
        }

        $this->_helper->content
                ->setNoRender()
                ->setEnabled()
        ;
    }

}
