<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:20:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_BrowselocationSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    // Make form
    $this->view->form = $form = new Sitepage_Form_Locationsearch(array('type' => 'sitepage_page'));
 
		if(!empty($_POST)) {
			$this->view->is_ajax = $_POST['is_ajax'];
		}
		
	  if(empty($_POST['sitepage_location'])) {
			$this->view->locationVariable = '1';
		}

    if (empty($_POST['is_ajax'])) {
			$p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
			$form->isValid($p);
			$values = $form->getValues();
			$customFieldValues = array_intersect_key($values, $form->getFieldElements());
			$this->view->is_ajax = $this->_getParam( 'is_ajax', 0 );
    } else {
			$values = $_POST;
			$customFieldValues = array_intersect_key($values, $form->getFieldElements());
    }

    unset($values['or']);
    //$this->view->formValues = array_filter($values);
    $this->view->assign($values);
    $viewer = Engine_Api::_()->user()->getViewer();
    if (@$values['show'] == 2) {

      //GET AN ARRAY OF FRIEND IDS
      $friends = $viewer->membership()->getMembers();

      $ids = array();
      foreach ($friends as $friend) {
        $ids[] = $friend->user_id;
      }

      $values['users'] = $ids;
    }
    $values['type'] = 'browse';
    $values['type_location'] = 'browseLocation';
    
    if (isset($values['show'])) {
			if ($form->show->getValue() == 3) {
				@$values['show'] = 3;
			}
    }
    
    //$viewerId = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->current_page = $page = $this->_getParam( 'page' , 1 ) ;
    $this->view->current_totalpages = $page * 15 ;
    $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitepage()->enableLocation();
    $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.price.field', 1);

    //check for miles or street.
		if (isset($values['locationmiles']) && !empty($values['locationmiles'])) {
			if (isset($values['sitepage_street']) && !empty($values['sitepage_street'])) {
				$values['sitepage_location'] = $values['sitepage_street'] . ',';
				unset($values['sitepage_street']);
			}
			
			if (isset($values['sitepage_city']) && !empty($values['sitepage_city'])) {
				$values['sitepage_location'].= $values['sitepage_city'] . ',';
				unset($values['sitepage_city']);
			}	
			
			if (isset($values['sitepage_state']) && !empty($values['sitepage_state'])) {
				$values['sitepage_location'].= $values['sitepage_state'] . ',';
				unset($values['sitepage_state']);
			} 
			
			if (isset($values['sitepage_country']) && !empty($values['sitepage_country'])) {
				$values['sitepage_location'].= $values['sitepage_country'];
				unset($values['sitepage_country']);
			}
		}   

    $result = Engine_Api::_()->sitepage()->getSitepagesSelect($values, $customFieldValues);
   	$this->view->paginator = $paginator = Zend_Paginator::factory($result); 
    $paginator->setItemCountPerPage(15);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);
    $this->view->totalresults = $paginator->getTotalItemCount();
    $this->view->mobile = Engine_Api::_()->seaocore()->isMobile();
		if(!empty($_POST['is_ajax'])) {
			//For show location marker.
			if (!empty($checkLocation) && $paginator->getTotalItemCount() > 0) {
				$ids = array();
				$sponsored = array();
				foreach ($paginator as $sitepage) {
					$id = $sitepage->getIdentity();
					$ids[] = $id;
					$sitepage_temp[$id] = $sitepage;
				}
				$values['page_ids'] = $ids;
				$this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocation($values);
				$sitepage_lat = array();
				$sitepage_long = array();
				foreach ($locations as $location) {
					$sitepage_lat[] = $location->latitude;
					$sitepage_long[] = $location->longitude;
				}

				foreach ($locations as $location) {
					if ($sitepage_temp[$location->page_id]->sponsored) {
						break;
					}
				}
				$this->view->sitepage = $sitepage_temp;
			}
    }
  }
}