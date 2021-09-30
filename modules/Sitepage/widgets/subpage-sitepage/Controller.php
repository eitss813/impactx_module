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

class Sitepage_Widget_SubpageSitepageController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

		//GET THE SUBJECT OF PAGE.
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    $sitepage_id = $sitepage->page_id;
		$limit = $this->_getParam('itemCount', 3);
    $params = array();
    
    //FUNCTION CALL FORM THE DBTABLE AND PASS PAGE ID OR LIMIT OF PAGES TO SHOW ON THE WIDGET.
		if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      //FUNCTION CALL FORM THE DBTABLE AND PASS PAGE ID OR LIMIT OF PAGES TO SHOW ON THE WIDGET.
			$this->view->userListings = $userListings = Engine_Api::_()->getDbtable('favourites', 'sitepage')->linkedPages($sitepage_id, $limit,$params, 'subpage');
			// Set item count per page and current page number
			$this->view->userListings = $userListings->setItemCountPerPage(5);
			$this->view->userListings = $userListings->setCurrentPageNumber($this->_getParam('page', 1));
		  $this->_childCount = $userListings->getTotalItemCount();
      if ($userListings->getTotalItemCount() <= 0) {
				return $this->setNoRender();
			}
    } else {
			$this->view->userListings = $userListings = Engine_Api::_()->getDbtable('favourites', 'sitepage')->linkedPages($sitepage_id, $limit,$params, 'subpage');
			//NOT RENDER IF SITEPAGE COUNT ZERO
			if (!(count($this->view->userListings) > 0)) {
				return $this->setNoRender();
			}
    }
  }
}