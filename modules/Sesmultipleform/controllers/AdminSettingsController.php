<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminSettingsController.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_AdminSettingsController extends Core_Controller_Action_Admin {
  public function indexAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesmultipleform_admin_main', array(), 'sesmultipleform_admin_main_settings');
    $this->view->form = $form = new Sesmultipleform_Form_Admin_Settings_Global();    
    $settings = Engine_Api::_()->getApi('settings', 'core');
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
			$values = $form->getValues();
			include_once APPLICATION_PATH . "/application/modules/Sesmultipleform/controllers/License.php";
			if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmultipleform.pluginactivated')) {
        foreach ($values as $key => $value) {
          $settings->setSetting($key, $value);
        }
        $form->addNotice('Your changes have been saved.');
        if($error)
        $this->_helper->redirector->gotoRoute(array());
      }
    }
  }
	  public function advanceSettingAction() {
			$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
							->getNavigation('sesmultipleform_admin_main', array(), 'sesmultipleform_admin_main_forms');
							// Setup
			$viewer = Engine_Api::_()->user()->getViewer();
			$this->view->form_id = $id = $this->_getParam('id');
			$this->view->formObj = $formObj =  Engine_Api::_()->getItem('sesmultipleform_form',$id);
			$this->view->formset = $formset = Engine_Api::_()->getDbtable('settings', 'sesmultipleform')->getSetting(array('id'=> $id));
			$this->view->form = $form = new Sesmultipleform_Form_Admin_Settings_Advance();     
			if(($formset)){
				 $itemArray = $formset->toArray();
				 $form->populate($itemArray);  
			 }
			 $form->populate($formObj->toArray());  
			 // Check method/valid
			if( !$this->getRequest()->isPost() ) {
				return;
			}
			if( !$form->isValid($this->getRequest()->getPost()) ) {
			  return;
			}
			// Process
			$db = Engine_Api::_()->getDbtable('settings', 'sesmultipleform')->getAdapter();
			$db->beginTransaction();
			try {
				if(!($formset)){	
					$table = Engine_Api::_()->getDbtable('settings', 'sesmultipleform');
					$formset = $table->createRow();
				}
				$values = $form->getValues();
				$values['form_id'] = $id;
				$formset->setFromArray($values);
				$formset->save();
				//save form
				$formObj->setFromArray($values);
				$formObj->save();
				$db->commit();
			} catch( Exception $e ) {
				$db->rollBack();
				throw $e;
			}
			if (isset($_POST['submitsave'])){
					$formUrl = rtrim($this->view->baseUrl(), '/') . '/admin/sesmultipleform/forms';
				// Redirect
					return $this->_helper->redirector->gotoUrl($formUrl, array('prependBase' => false));
			}
  }
	public function aboutusAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesmultipleform_admin_main', array(), 'sesmultipleform_admin_main_aboutus');
    $this->view->form = $form = new Sesmultipleform_Form_Admin_Settings_Aboutus();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      foreach ($values as $key => $value) {
        if ($key == 'sesmultipleform_aboutusposition') {
          $menu_table = Engine_Api::_()->getDbtable('menuitems', 'core');
          if ($value == 1){
            $menu_table->update(array('menu' => 'core_footer', 'enabled' => 1), array('name =?' => 'sesmultipleform_footer_aboutus'));
						$menu_table->update(array('menu' => 'core_main',  'enabled' => 0), array('name =?' => 'sesmultipleform_main_aboutus'));
						$menu_table->update(array('menu' => 'core_mini', 'enabled' => 0), array('name =?' => 'sesmultipleform_mini_aboutus'));
					}else if ($value == 3){
            $menu_table->update(array('menu' => 'core_main','enabled' => 1), array('name =?' => 'sesmultipleform_main_aboutus'));
						$menu_table->update(array('menu' => 'core_mini','enabled' => 0), array('name =?' => 'sesmultipleform_mini_aboutus'));
						$menu_table->update(array('menu' => 'core_footer','enabled' =>0), array('name =?' => 'sesmultipleform_footer_aboutus'));
					}else if ($value == 2){
            $menu_table->update(array('menu' => 'core_mini', 'enabled' => 1), array('name =?' => 'sesmultipleform_mini_aboutus'));
						$menu_table->update(array('menu' => 'core_footer', 'enabled' => 0), array('name =?' => 'sesmultipleform_footer_aboutus'));
						$menu_table->update(array('menu' => 'core_main','enabled' => 0), array('name =?' => 'sesmultipleform_main_aboutus'));
					}else if ($value == 0){
            $menu_table->update(array('enabled' => 0), array('name =?' => 'sesmultipleform_mini_aboutus'));
						$menu_table->update(array('enabled' => 0), array('name =?' => 'sesmultipleform_main_aboutus'));
						$menu_table->update(array('enabled' => 0), array('name =?' => 'sesmultipleform_footer_aboutus'));
					}
        }
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
      $form->addNotice('Your changes have been saved.');
    }
  }
}
