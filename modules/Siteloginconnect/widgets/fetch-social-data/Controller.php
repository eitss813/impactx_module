<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Siteloginconnect
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php (var) 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteloginconnect_Widget_FetchSocialDataController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    // Logout user will not show this widgets.
    $viewer = Engine_Api::_()->user()->getViewer();
    // $siteloginlinkedinwidget = Zend_Registry::isRegistered('siteloginlinkedinwidget') ? Zend_Registry::get('siteloginlinkedinwidget') : null;    
    if (empty($viewer))
        return $this->setNoRender();

    $this->view->base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
     $enabledSites = $this->_getParam('enabled_socialsites', array());
    foreach ($enabledSites as $socialsite) {   
        $siteintegtration=$socialsite.'IntegrationEnabled';
        if($socialsite == 'facebook' || $socialsite == 'twitter'){                
                $siteEnabled=Engine_Api::_()->sitelogin()->$siteintegtration();
        } else {                
                $siteEnabled = Engine_Api::_()->getDbtable($socialsite, 'sitelogin')->$siteintegtration();
        }
        if(!empty($siteEnabled))
          $integratedSites[]=$socialsite;
    }
    $this->view->enabledSites = $integratedSites;
    $settings = Engine_Api::_()->getDbtable('settings', 'core');
  }
}
?>