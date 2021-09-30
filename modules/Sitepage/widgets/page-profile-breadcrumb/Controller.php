<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_PageProfileBreadcrumbController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitepage_page')) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');    

    //GET CATEGORY TABLE
    $this->view->tableCategory = Engine_Api::_()->getDbTable('categories', 'sitepage');
    if (!empty($sitepage->category_id)) {
      $this->view->category_name = $this->view->tableCategory->getCategory($sitepage->category_id)->category_name;

      if (!empty($sitepage->subcategory_id)) {
        $this->view->subcategory_name = $this->view->tableCategory->getCategory($sitepage->subcategory_id)->category_name;

        if (!empty($sitepage->subsubcategory_id)) {
          $this->view->subsubcategory_name = $this->view->tableCategory->getCategory($sitepage->subsubcategory_id)->category_name;
        }
      }
    }
  }

}