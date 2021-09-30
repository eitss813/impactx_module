<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Widget_UploadPhotoSitealbumController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        // Must be logged in
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer || !$viewer->getIdentity()) {
            return $this->setNoRender();
        }

        // Must be able to create albums
        if (!Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create')) {
            return $this->setNoRender();
        }

        $this->view->upload_button = $this->_getParam('upload_button', 0);
        $this->view->upload_button_title = $this->_getParam('upload_button_title', 'Add New Photos');
    }

}
