<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteloginconnect
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    AdminSettingsController.php 2018-02-21 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteloginconnect_AdminSettingsController extends Core_Controller_Action_Admin {

	public function indexAction() {
		//GET NAVIGATION
    	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('siteloginconnect_admin_main', array(), 'siteloginconnect_admin_main_settings');

		$this->view->form = $form = new Siteloginconnect_Form_Admin_Global();
		$form->populate((array) Engine_Api::_()->getApi('settings', 'core')->siteloginconnect_global);
		if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
        {	
        	$values = $form->getValues();
        	if (Engine_Api::_()->getApi('settings', 'core')->siteloginconnect_global)
            Engine_Api::_()->getApi('settings', 'core')->removeSetting('siteloginconnect_global');
       
        	Engine_Api::_()->getApi('settings', 'core')->siteloginconnect_global = $values;
            $form->addNotice('Your changes have been saved.');
        }
	}

	public function mapAction() {
			//GET NAVIGATION
    	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('siteloginconnect_admin_main', array(), 'siteloginconnect_admin_main_mapping');

		$isPost = $this->getRequest()->isPost();
		$post = $this->getRequest()->getPost();
		$mapTable = Engine_Api::_()->getDbTable('maps', 'siteloginconnect');
		$maps = Engine_Api::_()->siteloginconnect()->getProfileFieldMaps();

		$params = array("social_site" => "", "maps"=>$maps);
		if(!empty($post["social_site"])) {
			$params["socialSite"] = $post["social_site"];

			$result=$mapTable->fetchAll($mapTable->select()
                        	->where("social_site = ?", $post["social_site"]))
            				->toarray();
            foreach ($result as $key => $value) {
            	$formvalue['field_'.$value['profile_type'].'_'.$value['field_id']]=$value['social_site_field'];
            }
		}

		$this->view->maps = Zend_Json::encode($maps);
		$this->view->form = $form = new Siteloginconnect_Form_Admin_Mapfields($params);
		if(!empty($formvalue))
			$form->populate($formvalue);
		
		if(!$isPost) {
			return;
		}

		if(empty($post['profile_type'])) {
			return;
		}

		$elements = $form->getValues();

		foreach ($maps["profile_types"] as $profileType) {
			unset($values['profile_type_'.$profileType['option_id']]);
		}
		
		$options = Engine_Api::_()->siteloginconnect()->getSocialSiteFields();
		$currentOptions = $options[$post["social_site"]];

		foreach($post as $k => $value) {
			if(strpos($k, "field_") !== false) {
				if(!in_array($value, $currentOptions)) {
					unset($post[$k]);
				}
			}
		}
		
		if(!$form->isValid($post)) {
			return;
		}

		$mapTable->delete(	
					array(
						"profile_type IN(?) " => explode(",", $form->profile_type->getValue()),
						"social_site = ?" => $form->social_site->getValue(),
					));

		foreach( $form->getValues() as $key=>$value) {
			$key = explode("_", $key);
			if($key[0] == "field" && !empty($value)) {
				try{
					$newRow = $mapTable->createRow();
					$newRow->profile_type = $key[1];
					$newRow->field_id = $key[2];
					$newRow->social_site = $form->social_site->getValue();
					$newRow->social_site_field = $value;

					$newRow->save();
				} catch (Exception $e) {
					// $form->addError($e->getMessage());
					dc($e->getMessage());
				}
			} 
		}
		$form->addNotice('Your changes have been saved.');
	}

	public function faqAction() {
        //MAKE NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteloginconnect_admin_main', array(), 'siteloginconnect_admin_main_faq');
                
    }

}