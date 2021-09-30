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
class Sitemember_Widget_OptionsSitememberController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GET NAVIGATION
    $this->view->gutterNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitemember_gutter');
  }

}