<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_Widget_PopupController extends Engine_Content_Widget_Abstract {
  public function indexAction() {
		$this->view->formtype = $formtype = $this->_getParam('formtype',$this->_getParam('formid',null));
		$this->view->closepopup  = $this->_getParam('closepopup',1);
		$this->view->hideform = $this->_getParam('hideform',1);
		$this->view->popuptype  = $this->_getParam('popuptype',1);
		$this->view->redirectOpen  = $this->_getParam('redirectOpen','0');
		if(!$this->view->redirectOpen)
			$this->view->popuptype = 1;
		$this->view->formObj = $formObj = Engine_Api::_()->getItem('sesmultipleform_form',$formtype);
		if(!$formtype || !$formObj || !$formObj->active)
			$this->setNoRender();
		 $settings = Engine_Api::_()->getApi('settings', 'core');
		$this->view->redirect = $this->_getParam('redirect',0);
		$this->view->formsettings = $formsettings =  Engine_Api::_()->getDbtable('settings', 'sesmultipleform')->getSetting(array('id'=>$formtype));
		$sesmultipleform_popup = Zend_Registry::isRegistered('sesmultipleform_popup') ? Zend_Registry::get('sesmultipleform_popup') : null;
		if(empty($sesmultipleform_popup)) {
			return $this->setNoRender();
		}
		//global banned code
		$bannedips = $settings->getSetting('sesmultipleform.ipaddressban', "");
		if (in_array($_SERVER['REMOTE_ADDR'], explode(",", $bannedips)) || in_array($_SERVER['REMOTE_ADDR'], explode(",", $formsettings->ipaddress_ban)))
		 $this->setNoRender();
      
    $this->view->buttontext = $this->_getParam('buttontext',false);   
    if (!($this->view->buttontext))
      return $this->setNoRender();
			
		$this->view->margintype = $this->_getParam('margintype','per');	  
		$this->view->margin = $this->_getParam('margin','3');	  
    $this->view->buttonposition = $this->_getParam('position','3');
    $this->view->buttoncolor = $this->_getParam('buttoncolor','#78c744');
    $this->view->textcolor = $this->_getParam('textcolor', '#ffffff');
    $this->view->texthovercolor = $this->_getParam('texthovercolor', '#000c24');
  }
}