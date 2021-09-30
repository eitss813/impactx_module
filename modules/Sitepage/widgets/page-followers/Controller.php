<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_PageFollowersController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        // DON'T RENDER IF NOT AUTHORIZED.
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        // get params
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        // GET THE SUBJECT OF PAGE.
        $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

        $this->view->params = $params;

        $paginator = Engine_Api::_()->getDbTable('follows', 'seaocore')->getAllFollowsUsers($sitepage);
        $this->view->paginator = $paginator;

    }
}
?>