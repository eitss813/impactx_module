<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_PackageController extends Core_Controller_Action_Standard {

  //ACTION FOR SHOW PACKAGES
  public function indexAction() {

    //USER VALIDATON
    if (!$this->_helper->requireUser()->isValid())
      return;

    //PAGE CREATION PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'create')->isValid())
      return;
    
    if (!Engine_Api::_()->sitepage()->hasPackageEnable()) {
      //REDIRECT
      return $this->_helper->redirector->gotoRoute(array('action' => 'create'), 'sitepage_general', true);    
    }

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');
    
    //START INTERGRATION EXTENSION WORK
    $this->view->business_id = $this->_getParam('business_id', null);
    $this->view->group_id = $this->_getParam('group_id', null);
		$this->view->store_id = $this->_getParam('store_id', null);
		//END INTERGRATION EXTENSION WORK
		
    $this->view->parent_id = $this->_getParam('parent_id', null);
    //Start Coupon plugin work.
		$this->view->couponmodules_enabled =  $couponEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitecoupon');
		if (!empty($couponEnabled)) {
			$modules_enabled = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'modules.enabled' ) ;
			if (!empty($modules_enabled)) {
				$this->view->modules_enabled = unserialize($modules_enabled) ;
			}
		}
		//End coupon plugin work.    

    $packages_select = Engine_Api::_()->getItemtable('sitepage_package')->getPackagesSql(null);
    $paginator = Zend_Paginator::factory($packages_select);

//     $this->view->paginator = array();
      $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page'));

    
//    $packagesCountSelect = Engine_Api::_()->getItemtable('sitepage_package')->getPackagesSql(null);
//    $paginatorCountSelect = Zend_Paginator::factory($packagesCountSelect);
//    $totalItemCountSelect = $paginatorCountSelect->getTotalItemCount();
//    $this->view->showAllPackageMsg = 0;
//    if($this->view->paginator->getTotalItemCount() != $totalItemCountSelect) {
//      $this->view->showAllPackageMsg = 1;
//    }
    
  }

	//ACTION FOR PACKAGE DETAIL
  public function detailAction() {

    //USER VALIDATON
    if (!$this->_helper->requireUser()->isValid())
      return;

    //PAGE CREATION PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'create')->isValid())
      return;

    $id = $this->_getParam('id');
    if (empty($id)) {
      return $this->_forward('notfound', 'error', 'core');
    }
    $this->view->package = Engine_Api::_()->getItem('sitepage_package', $id);
  }

	//ACTION FOR PACKAGE UPDATION
  public function updatePackageAction() {

    //USER VALIDATON
    if (!$this->_helper->requireUser()->isValid())
      return;

		//PACKAGE ENABLE VALIDATION
    if (!Engine_Api::_()->sitepage()->hasPackageEnable()) {
      return $this->_forward('notfound', 'error', 'core');
    }

    $this->view->sitepages_view_menu = 15;

		//GET PAGE ID PAGE OBJECT AND THEN CHECK VALIDATIONS
    $this->view->page_id = $page_id = $this->_getParam('page_id');
    if (empty($page_id)) {
      return $this->_forward('notfound', 'error', 'core');
    }
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if (empty($sitepage)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
		
    $this->view->package = Engine_Api::_()->getItem('sitepage_package', $sitepage->package_id);
    $table = Engine_Api::_()->getItemtable('sitepage_package');
    $packages_select = $table->getPackagesSql($sitepage->getOwner())
            ->where("update_list = ?", 1)
            ->where("enabled = ?", 1)
            ->where("package_id <> ?", $sitepage->package_id);
    $paginator = Zend_Paginator::factory($packages_select);
    
    $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page'));

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');
    $this->view->is_ajax = $this->_getParam('is_ajax', '');
    
    //Start Coupon plugin work.
		$couponEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitecoupon');
		if (!empty($couponEnabled)) {
			$modules_enabled = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'modules.enabled' ) ;
			if (!empty($modules_enabled)) {
				$this->view->modules_enabled = unserialize($modules_enabled) ;
			}
		}
		//End coupon plugin work.
  }

	//ACTION FOR PACKAGE UPGRADE CONFIRMATION
  public function updateConfirmationAction() {

		//USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

		//PACKAGE ENABLE VALIDATION
    if (!Engine_Api::_()->sitepage()->hasPackageEnable()) {
      return $this->_forward('notfound', 'error', 'core');
    }

		//GET PAGE ID PAGE OBJECT AND THEN CHECK VALIDATIONS
    $this->view->page_id = $page_id = $this->_getParam('page_id');
    if (empty($page_id)) {
      return $this->_forward('notfound', 'error', 'core');
    }
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if (empty($sitepage)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK


    $this->view->package_id = $this->_getParam('package_id');
    $package_chnage = Engine_Api::_()->getItem('sitepage_package', $this->view->package_id);
    
    if (empty($package_chnage) || !$package_chnage->enabled || (!empty($package_chnage->level_id) && !in_array($sitepage->getOwner()->level_id , explode(",", $package_chnage->level_id)))) {
      return $this->_forward('notfound', 'error', 'core');
    }
	
    $getPackageAuth = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepage');

    if ($this->getRequest()->getPost()) {

      if (!empty($_POST['package_id'])) {
        $table = $sitepage->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
          $is_upgrade_package = true;
          
          //APPLIED CHECKS BECAUSE CANCEL SHOULD NOT BE CALLED IF ALREADY CANCELLED 
          if($sitepage->status == 'active')
            $sitepage->cancel($is_upgrade_package);
          
          $sitepage->package_id = $_POST['package_id'];
          $package = Engine_Api::_()->getItem('sitepage_package', $sitepage->package_id);

          $sitepage->featured = $package->featured;
          $sitepage->sponsored = $package->sponsored;
          $sitepage->pending = 1;
          $sitepage->expiration_date = new Zend_Db_Expr('NULL');
          $sitepage->status = 'initial';
          if (($package->isFree()) && !empty($getPackageAuth)) {
            $sitepage->approved = $package->approved;
          } else {
            $sitepage->approved = 0;
          }

          if (!empty($sitepage->approved)) {            
            $sitepage->pending = 0;
            $expirationDate = $package->getExpirationDate();
            if (!empty($expirationDate))
              $sitepage->expiration_date = date('Y-m-d H:i:s', $expirationDate);
            else
              $sitepage->expiration_date = '2250-01-01 00:00:00';

            if (empty($sitepage->aprrove_date)) {
              $sitepage->aprrove_date = date('Y-m-d H:i:s');
              if (!empty($sitepage) && !empty($sitepage->draft) && empty($sitepage->pending)) {
                Engine_Api::_()->sitepage()->attachPageActivity($sitepage);
              }
            }
          }
          $sitepage->save();
          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
      }
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => true,           
              'format' => 'smoothbox',
              'parentRedirect' => $this->view->url(array('action' => 'update-package', 'page_id' => $sitepage->page_id), 'sitepage_packages', true),
              'parentRedirectTime' => 15,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_('The package for your Page has been successfully changed.'))
      ));
    }
  }

	//ACTION FOR PACKAGE PAYMENT
  public function paymentAction() {

		//USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

		//GET PAGE ID PAGE OBJECT AND THEN CHECK VALIDATIONS
    $page_id = $_POST['page_id_session'];
    if (empty($page_id)) {
      return $this->_forward('notfound', 'error', 'core');
    }
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if (empty($sitepage)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    $getPackageAuth = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepage');
    $package = Engine_Api::_()->getItem('sitepage_package', $sitepage->package_id);

    if ((!$package->isFree()) && !empty($getPackageAuth)) {
      $session = new Zend_Session_Namespace('Payment_Sitepage');
      $session->page_id = $page_id;
  
      return $this->_helper->redirector->gotoRoute(array(), 'sitepage_payment', true);
    } else {
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitepage_general', true);
    }
  }
  
    
  //ACTION FOR PACKAGE CANCEL
  public function cancelAction(){
    if( !($package_id = $this->_getParam('package_id')) ||
        !($package = Engine_Api::_()->getItem('sitepage_package', $package_id)) ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index', 'package_id' => null));
    }

    $this->view->package_id = $package_id;
    $page_id = $this->_getParam('page_id');

    $this->view->form = $form = new Sitepage_Form_Packagecancel();

    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Try to cancel
    $this->view->form = null;
    try {
      Engine_Api::_()->getItem('sitepage_page', $page_id)->cancel();
      $this->view->status = true;
    } catch( Exception $e ) {
      $this->view->status = false;
      $this->view->error = $e->getMessage();
    }
  }
  
}
?>