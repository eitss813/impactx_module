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
class Sitepage_Widget_TagcloudSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');   

    //CONSTRUCTING TAG CLOUD
    $tag_array = array();
    $this->view->category_id = $category_id = $this->_getParam('category_id',0);
		$pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
		$this->view->count_only = $pageTable->getTagCloud(20,$category_id, 1);
		if($this->view->count_only <= 0) {
			return $this->setNoRender();
		}

    $this->view->loaded_by_ajax = $is_ajax_load = $this->_getParam('loaded_by_ajax', true);
    $this->view->isajax = $isajax = $this->_getParam('isajax', false);
    $this->view->allParams  = array('isajax' => 1, 'loaded_by_ajax' => 1, 'category_id' => $category_id);    

    if (!$is_ajax_load || ($is_ajax_load && $isajax)) {
        $tag_cloud_array = $pageTable->getTagCloud(20, $category_id, 0);

        foreach ($tag_cloud_array as $vales) {
          $tag_array[$vales['text']] = $vales['Frequency'];
          $tag_id_array[$vales['text']] = $vales['tag_id'];
        }

        if (!empty($tag_array)) {
          $max_font_size = 18;
          $min_font_size = 12;
          $max_frequency = max(array_values($tag_array));
          $min_frequency = min(array_values($tag_array));
          $spread = $max_frequency - $min_frequency;
          if ($spread == 0) {
            $spread = 1;
          }
          $step = ($max_font_size - $min_font_size) / ($spread);

          $tag_data = array('min_font_size' => $min_font_size, 'max_font_size' => $max_font_size, 'max_frequency' => $max_frequency, 'min_frequency' => $min_frequency, 'step' => $step);

          $this->view->tag_data = $tag_data;
          $this->view->tag_id_array = $tag_id_array;
        }
        $this->view->tag_array = $tag_array;

        if(empty($this->view->tag_array)) {
          return $this->setNoRender();
        }
        
        $this->view->showcontent = true;
        if($isajax) {        
          $this->getElement()->removeDecorator('Container');
        }        
      }
  }

}
?>