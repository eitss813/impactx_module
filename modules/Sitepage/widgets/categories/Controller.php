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
class Sitepage_Widget_CategoriesController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //START NETWORK WORK

    $showAllCategories = $this->_getParam('showAllCategories', 0);
    $this->view->displayNotAll = 1;
    $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.network', 0);
    
    if (!empty($showAllCategories)) {
      $displayOnlyUsefulPages = 0;
      $this->view->displayNotAll=!$enableNetwork;
    } else {
      $displayOnlyUsefulPages = 1;
    }   
  
    $this->view->show3rdlevelCategory = $show3rdlevelCategory = $this->_getParam('show3rdlevelCategory', 1);
		$this->view->show2ndlevelCategory = $show2ndlevelCategory = $this->_getParam('show2ndlevelCategory', 1);

    $categories = array();
    if ($this->view->displayNotAll) {
      $category_info = Engine_Api::_()->getDbtable('categories', 'sitepage')->getAllCategories(0, 'category_id', $displayOnlyUsefulPages, 'page_id', 1, 0);
      foreach ($category_info as $value) {
        $sub_cat_array = array();
				if (!empty($show2ndlevelCategory)) {
					$category_info2 = Engine_Api::_()->getDbtable('categories', 'sitepage')->getAllCategories($value['category_id'], 'subcategory_id', $displayOnlyUsefulPages, 'subcategory_id', null, 0);
					foreach ($category_info2 as $subresults) {
						if (!empty($show3rdlevelCategory)) {
							$subcategory_info2 = Engine_Api::_()->getDbtable('categories', 'sitepage')->getAllCategories($subresults['category_id'], 'subsubcategory_id', $displayOnlyUsefulPages, 'subsubcategory_id', null, 0);
							$treesubarrays[$subresults->category_id] = array();
							foreach ($subcategory_info2 as $subvalues) {
								$treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
										'tree_sub_cat_name' => $subvalues->category_name,
										'count' => $subvalues->count,
										'order' => $subvalues->cat_order,
								);
								$treesubarrays[$subresults['category_id']][] = $treesubarray;
							}

							$tmp_array = array('sub_cat_id' => $subresults->category_id,
									'sub_cat_name' => $subresults->category_name,
									'tree_sub_cat' => $treesubarrays[$subresults->category_id],
									'count' => $subresults->count,
									'order' => $subresults->cat_order);
							$sub_cat_array[] = $tmp_array;
						} else {
							$tmp_array = array('sub_cat_id' => $subresults->category_id,
									'sub_cat_name' => $subresults->category_name,
									'count' => $subresults->count,
									'order' => $subresults->cat_order);
							$sub_cat_array[] = $tmp_array;
						}
					}
				}
        $category_array = array('category_id' => $value->category_id,
            'category_name' => $value->category_name,
            'order' => $value->cat_order,
            'count' => $value->count,
            'sub_categories' => $sub_cat_array
        );
        $categories[] = $category_array;
      }
    } else {
      $categoriesTable = Engine_Api::_()->getDbtable('categories', 'sitepage');
      $category_info = $categoriesTable->getCategories(0);
      foreach ($category_info as $value) {
        $sub_cat_array = array();

				if (!empty($show2ndlevelCategory)) {
					$category_info2 = $categoriesTable->getSubCategories($value['category_id']);
					foreach ($category_info2 as $subresults) {
						if (!empty($show3rdlevelCategory)) {
							$subcategory_info2 = $categoriesTable->getSubCategories($subresults['category_id'], 'subsubcategory_id');
							$treesubarrays[$subresults->category_id] = array();
							foreach ($subcategory_info2 as $subvalues) {
								$treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
										'tree_sub_cat_name' => $subvalues->category_name,
										'order' => $subvalues->cat_order,
								);
								$treesubarrays[$subresults['category_id']][] = $treesubarray;
							}

							$tmp_array = array('sub_cat_id' => $subresults->category_id,
									'sub_cat_name' => $subresults->category_name,
									'tree_sub_cat' => $treesubarrays[$subresults->category_id],
									'order' => $subresults->cat_order);
							$sub_cat_array[] = $tmp_array;
						} else {
							$tmp_array = array('sub_cat_id' => $subresults->category_id,
									'sub_cat_name' => $subresults->category_name,
									'order' => $subresults->cat_order);
							$sub_cat_array[] = $tmp_array;
						}
					}
				}
        $category_array = array('category_id' => $value->category_id,
            'category_name' => $value->category_name,
            'order' => $value->cat_order,
            'sub_categories' => $sub_cat_array
        );
        $categories[] = $category_array;
      }
    }
    $this->view->categories = $categories;

    //SET NO RENDER
    if (!(count($this->view->categories) > 0)) {
      return $this->setNoRender();
    }
  }

}
?>