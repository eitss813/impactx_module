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
class Sitepage_Widget_ItemSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		$this->view->dayitem = Engine_Api::_()->getDbtable('pages', 'sitepage')->getItemOfDay();

    //DONT RENDER IF SITEPAGE COUNT ZERO
    if (!(count($this->view->dayitem) > 0)) {
      return $this->setNoRender();
    } else {
        //GET SETTINGS
        $this->view->sitepage = Engine_Api::_()->getItem('sitepage_page', $this->view->dayitem->page_id);
        $pre_field = array("0" => "1", "1" => "2", "2" => "3", "3" =>"4");
        $contacts = $this->_getParam('contacts', $pre_field);

        if (empty($contacts)) {
            $this->setNoRender();
        } else {
            //INITIALIZATION
            $this->view->show_likes = $this->view->show_comments = $this->view->show_views = $this->view->show_followers = $this->view->show_button = 0;
            if (in_array(1, $contacts)) {
                $this->view->show_likes = 1;
            }
            if (in_array(2, $contacts)) {
                $this->view->show_comments = 1;
            }
            if (in_array(3, $contacts)) {
                $this->view->show_views = 1;
            }
            if (in_array(4, $contacts)) {
                $this->view->show_followers = 1;
            }
        }
    }
  }
}
?>