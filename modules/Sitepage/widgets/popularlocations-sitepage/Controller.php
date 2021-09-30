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
class Sitepage_Widget_PopularlocationsSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF LOCATION IS DIS-ABLED BY ADMIN
    $locationFieldEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.locationfield', 1);
    if ( empty($locationFieldEnable) ) {
      return $this->setNoRender();
    }

    $category_id = $this->_getParam('category_id',0);
    $items_count = $this->_getParam('itemCount', 5);

    // GET SITEPAGE SITEPAGE FOR MOST RATED
    $this->view->sitepageLocation = Engine_Api::_()->getDbTable('pages', 'sitepage')->getPopularLocation($items_count,$category_id);

    $this->view->searchLocation = null;
    if ( isset($_GET['sitepage_location']) && !empty($_GET['sitepage_location']) )
      $this->view->searchLocation = $_GET['sitepage_location'];

    //DONT RENDER IF PAGE COUNT ZERO
    if ( !(count($this->view->sitepageLocation) > 0) ) {
      return $this->setNoRender();
    }
  }

}
?>