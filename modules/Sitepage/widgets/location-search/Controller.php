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

class Sitepage_Widget_LocationSearchController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    $valueArray = array('street' => $this->_getParam('street', 1), 'city' => $this->_getParam('city', 1), 'country' => $this->_getParam('country', 1), 'state' => $this->_getParam('state', 1), 'advancedsearchLink' => $this->_getParam('advancedsearchLink', 1));
    $sitepage_street = serialize($valueArray);

    // Make form
    $this->view->form = $form = new Sitepage_Form_Locationsearch(array('value' => $sitepage_street, 'type' => 'sitepage_page'));

    if (!empty($_POST)) {
      $this->view->category_id = $_POST['category'];
      $this->view->subcategory_id = $_POST['subcategory_id'];
      $this->view->subcategory_name = $_POST['subcategoryname'];
      $this->view->subsubcategory_id = $_POST['subsubcategory_id'];
    }
    
    if (!empty($_POST)) { 
			$this->view->advanced_search =  $_POST['advanced_search'];
    }
// 		if (!empty($_POST)) {
// 			$form->populate($_POST);
//     }

    // Process form
    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    $form->isValid($p);
    $values = $form->getValues();

    unset($values['or']);
    $this->view->formValues = array_filter($values);
    $this->view->assign($values);
  }
}