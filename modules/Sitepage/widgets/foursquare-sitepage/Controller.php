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
class Sitepage_Widget_FoursquareSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
      
    //DO NOT RENDER THIS WIDGET IF ALREADY PLACED FOR SITES 
    return $this->setNoRender();  

		//DONT RENDER IS SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
      return $this->setNoRender();
    }

		//SET NO RENDER IF NOT AUTHORIZED
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'foursquare');
    if (empty($sitepage->foursquare_text) || empty($sitepage->location) || empty($isManageAdmin)) {
      return $this->setNoRender();
    }
  }

}
?>