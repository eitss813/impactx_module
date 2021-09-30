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

class Sitepage_Widget_ParentpageSitepageController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

		//GET THE SUBJECT OF PAGE.
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    $sitepage_id = $sitepage->parent_id;
		$LIMIT = $this->_getParam('itemCount', 3);
    $params = array();
    
    //FUNCTION CALL FORM THE DBTABLE AND PASS PAGE ID OR LIMIT OF PAGES TO SHOW ON THE WIDGET.
		$this->view->userListings = $userListings = Engine_Api::_()->getDbtable('favourites', 'sitepage')->linkedPages($sitepage_id, $LIMIT,$params, 'parentpage');

		//NOT RENDER IF SITEPAGE COUNT ZERO
		if (!(count($this->view->userListings) > 0)) {
      return $this->setNoRender();
    }
  }
}