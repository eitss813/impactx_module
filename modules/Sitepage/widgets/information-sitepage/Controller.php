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
class Sitepage_Widget_InformationSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SETTING
    $this->view->showContent = array("ownerPhoto", "ownerName", "modifiedDate", "viewCount","likeCount", "commentCount", "tags", "location", "price", "memberCount", "followerCount", "categoryName");

    $this->view->showContent = $this->_getParam('showContent', array("ownerPhoto", "ownerName", "modifiedDate", "viewCount","likeCount", "commentCount", "tags", "location", "price", "memberCount", "followerCount", "categoryName"));       
    
    if(empty($this->view->showContent))
     return $this->setNoRender();
    
    //GET SUBJECT
    if(Engine_Api::_()->core()->getSubject()->getType() == 'sitepage_page') {
    	$this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    }
    else {
      $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject()->getParent();
    }
    
    $tableCategories = Engine_Api::_()->getDbTable('categories', 'sitepage');
    $this->view->category_name = $this->view->subcategory_name == $this->view->subsubcategory_name = '';
    if($sitepage->category_id) {
        $categoriesNmae = $tableCategories->getCategory($sitepage->category_id);
        if (!empty($categoriesNmae->category_name)) {
          $this->view->category_name = $categoriesNmae->category_name;
        }
        
        if($sitepage->subcategory_id) {
            $subcategory_name = $tableCategories->getCategory($sitepage->subcategory_id);
            if (!empty($subcategory_name->category_name)) {
              $this->view->subcategory_name = $subcategory_name->category_name;
            }
            
            //GET SUB-SUB-CATEGORY
            if($sitepage->subsubcategory_id) {
                $subsubcategory_name = $tableCategories->getCategory($sitepage->subsubcategory_id);
                if (!empty($subsubcategory_name->category_name)) {
                  $this->view->subsubcategory_name = $subsubcategory_name->category_name;
                }
            }
        }
    }
    
    $this->view->sitepageTags = $sitepage->tags()->getTagMaps();
  }
}
?>