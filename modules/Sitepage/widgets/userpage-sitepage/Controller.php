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
class Sitepage_Widget_UserpageSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//DON'T RENDER THIS IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT AND PAGE ID
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    $params = array();
    $params['totalpages'] = $this->_getParam('itemCount', 3);
    $params['category_id'] = $this->_getParam('category_id', 0);
    $params['featured'] = $this->_getParam('featured', 0);
    $params['sponsored'] = $this->_getParam('sponsored', 0);
    $params['popularity'] = $this->_getParam('popularity', 'view_count');
    $params['owner_id'] = $sitepage->owner_id;
    $params['page_id'] = $sitepage->page_id;

    $this->view->userPages = $userPages =Engine_Api::_()->getDbtable('pages', 'sitepage')->userPage($params);

    if (!(count($this->view->userPages) > 0)) {
      return $this->setNoRender();
    }
  }

}
?>