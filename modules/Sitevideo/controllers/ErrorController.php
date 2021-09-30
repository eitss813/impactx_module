<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: ErrorController.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitevideo_ErrorController extends Core_Controller_Action_Standard {

    public function requireauthAction() {
        // 403 error -- authorization failed
        if (!$this->_helper->requireUser()->isValid())
            return;
        $this->getResponse()->setRawHeader($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('You are not authorized to access this resource.');
        $this->view->video_id = $video_id = (int) $this->_getParam('video_id');
        $this->view->video = $video = Engine_Api::_()->getItem('sitevideo_video', $video_id);
        $this->view->form = new Sitevideo_Form_Password();
    }

}
