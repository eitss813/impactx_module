<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Widget_ReviewPosterNameSitememberController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        // Don't render this if not authorized
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        $this->view->ownerMessage = 0;
        if ($module == 'sitemember' && $controller == 'review' && $action == 'owner-reviews') {
            $this->view->ownerMessage = 1;
        }

        // Get subject and check auth
        $this->view->user = $user = Engine_Api::_()->core()->getSubject('user');
        if (!$user->authorization()->isAllowed($viewer, 'view')) {
            return $this->setNoRender();
        }
    }

}