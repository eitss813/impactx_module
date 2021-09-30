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
class Sitemember_Widget_ProfileReviewBreadcrumbSitememberController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF NOT AUTHORIZED
        if (!Engine_Api::_()->core()->hasSubject('sitemember_review')) {
            return $this->setNoRender();
        }

        //GET REVIEWS
        $this->view->reviews = Engine_Api::_()->core()->getSubject();

        //GET USER 
        $this->view->user = $user = Engine_Api::_()->getItem('user', Zend_Controller_Front::getInstance()->getRequest()->getParam('user_id'));
    }

}