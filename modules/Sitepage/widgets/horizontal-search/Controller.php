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
class Sitepage_Widget_HorizontalSearchController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $valueArray = array('street' => $this->_getParam('street', 1), 'city' => $this->_getParam('city', 1), 'country' => $this->_getParam('country', 1), 'state' => $this->_getParam('state', 1), 'advancedsearchLink' => $this->_getParam('advancedsearchLink', 1));
    $sitepage_street = serialize($valueArray);

    // Make form
    $this->view->form = $form = new Sitepage_Form_Locationsearch(array('value' => $sitepage_street, 'type' => 'sitepage_page'));

    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    if (!empty($p)) {
      if (isset($p['category']))
        $this->view->category_id = $p['category'];
      if (isset($p['category_id']))
        $this->view->category_id = $p['category_id'];
      if (isset($p['subcategory_id']))
        $this->view->subcategory_id = $p['subcategory_id'];
      if (isset($p['subcategoryname']))
        $this->view->subcategory_name = $p['subcategoryname'];
      if (isset($p['subsubcategory_id']))
        $this->view->subsubcategory_id = $p['subsubcategory_id'];
    }

    if (!$this->view->category_id) {
      $content_id = $this->view->identity;
      $widgetname = 'sitepage.pinboard-browse';
      $filtercategory_id = Engine_Api::_()->sitepage()->getSitepageCategoryid($content_id, $widgetname);
      if (!empty($filtercategory_id)) {
        $this->view->category_id = $p['category_id'] = $filtercategory_id;
      } else {
        $content_id = $this->view->identity;
        $widgetname = 'sitepage.pages-sitepage';
        $filtercategory_id = Engine_Api::_()->sitepage()->getSitepageCategoryid($content_id, $widgetname);
        if (!empty($filtercategory_id)) {
          $this->view->category_id = $p['category_id'] = $filtercategory_id;
        }
      }
    }

    if ($this->view->category_id) {
      if (!isset($_GET['category_id']))
        $_GET['category_id'] = $p['category_id'];
      if (!isset($_GET['categoryname'])) {
        $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($this->view->category_id);
        if (!empty($row->category_name)) {
          $this->view->category_name = $_GET['categoryname'] = $categoryname = $row->category_name;
        }
      }
      $this->view->advanced_search = $p['advanced_search'] = 1;
    } elseif (!empty($p) && isset($p['advanced_search'])) {
      $this->view->advanced_search = $p['advanced_search'];
    }

// 		if (!empty($p)) {
// 			$form->populate($p);
//     }
    // Process form

    $form->isValid($p);
    $values = $form->getValues();

    unset($values['or']);
    $this->view->formValues = array_filter($values);
    $this->view->assign($values);
    $form->setMethod('GET');
    $browseredirect = $this->_getParam('browseredirect', 'default');
    if ($browseredirect == 'pinboard')
      $form->setAction($this->view->url(array('action' => 'pinboard-browse'), 'sitepage_general', true));
    elseif ($browseredirect == 'default') {
      $form->setAction($this->view->url(array('action' => 'index'), 'sitepage_general', true));
    }
  }

}