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
class Siteloginconnect_Widget_ConnectSocialLoginController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    // Logout user will not show this widgets.
    $viewer = Engine_Api::_()->user()->getViewer();
    // $siteloginlinkedinwidget = Zend_Registry::isRegistered('siteloginlinkedinwidget') ? Zend_Registry::get('siteloginlinkedinwidget') : null;    
    if (empty($viewer))
        return $this->setNoRender();

    $this->view->base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
    $enabledSites = $this->_getParam('enabled_socialsites', array());

    $settings = Engine_Api::_()->getDbtable('settings', 'core');

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
    $this->view->connetionStatus = array();
    if(in_array('outlook', $integratedSites)) {
      $outlookTable = Engine_Api::_()->getDbtable('outlook', 'sitelogin');
      $outlook_data = $outlookTable->fetchRow($outlookTable->select()->where("user_id = ?", $viewer->getIdentity()));
      $this->view->connetionStatus["outlook"] = (isset($outlook_data->outlook_id) && !empty($outlook_data->outlook_id)) ? true : false;
    }
    if(in_array('flickr', $integratedSites)) {
      $flickrTable = Engine_Api::_()->getDbtable('flickr', 'sitelogin');
      $flickr_data = $flickrTable->fetchRow($flickrTable->select()->where("user_id = ?", $viewer->getIdentity()));
      $this->view->connetionStatus["flickr"] = (isset($flickr_data->flickr_id) && !empty($flickr_data->flickr_id)) ? true : false;
    }
    if(in_array('vk', $integratedSites)) {
      $vkTable = Engine_Api::_()->getDbtable('vk', 'sitelogin');
      $vk_data = $vkTable->fetchRow($vkTable->select()->where("user_id = ?", $viewer->getIdentity()));
      $this->view->connetionStatus["vk"] = (isset($vk_data->vk_id) && !empty($vk_data->vk_id)) ? true : false;
    }
    if(in_array('google', $integratedSites)) {
      $googleTable = Engine_Api::_()->getDbtable('google', 'sitelogin');
      $google_data = $googleTable->fetchRow($googleTable->select()->where("user_id = ?", $viewer->getIdentity()));
      $this->view->connetionStatus["google"] = (isset($google_data->google_id) && !empty($google_data->google_id)) ? true : false;
    }
    if(in_array('yahoo', $integratedSites)) {
      $yahooTable = Engine_Api::_()->getDbtable('yahoo', 'sitelogin');
      $yahoo_data = $yahooTable->fetchRow($yahooTable->select()->where("user_id = ?", $viewer->getIdentity()));
      $this->view->connetionStatus["yahoo"] = (isset($yahoo_data->yahoo_id) && !empty($yahoo_data->yahoo_id)) ? true : false;
    }
    if(in_array('pinterest', $integratedSites)) {
      $pinterestTable = Engine_Api::_()->getDbtable('pinterest', 'sitelogin');
      $pinterest_data = $pinterestTable->fetchRow($pinterestTable->select()->where("user_id = ?", $viewer->getIdentity()));
      $this->view->connetionStatus["pinterest"] = (isset($pinterest_data->pinterest_id) && !empty($pinterest_data->pinterest_id)) ? true : false;
    }
    if(in_array('instagram', $integratedSites)) {
      $instagramTable = Engine_Api::_()->getDbtable('instagram', 'sitelogin');
      $instagram_data = $instagramTable->fetchRow($instagramTable->select()->where("user_id = ?", $viewer->getIdentity()));
      $this->view->connetionStatus["instagram"] = (isset($instagram_data->instagram_id) && !empty($instagram_data->instagram_id)) ? true : false;
    }
    if(in_array('linkedin', $integratedSites)) {
      $linkedinTable = Engine_Api::_()->getDbtable('linkedin', 'sitelogin');
      $linkedin_data = $linkedinTable->fetchRow($linkedinTable->select()->where("user_id = ?", $viewer->getIdentity()));
      $this->view->connetionStatus["linkedin"] = (isset($linkedin_data->linkedin_id) && !empty($linkedin_data->linkedin_id)) ? true : false;
    }

    if( in_array('facebook', $integratedSites) && $settings->core_facebook_enable ) {
      $facebookTable = Engine_Api::_()->getDbTable('facebook', 'user');
      $facebook_data = $facebookTable->fetchRow($facebookTable->select()->where("user_id = ?", $viewer->getIdentity()));
      $this->view->connetionStatus["facebook"] = (isset($facebook_data->facebook_uid) && !empty($facebook_data->facebook_uid)) ? true : false;
    }

    if(in_array('twitter', $integratedSites)) {
      $twitterTable = Engine_Api::_()->getDbTable('twitter', 'user');
      $twitter_data = $twitterTable->fetchRow($twitterTable->select()->where("user_id = ?", $viewer->getIdentity()));
      $this->view->connetionStatus["twitter"] = (isset($twitter_data->twitter_uid) && !empty($twitter_data->twitter_uid)) ? true : false;
    }
  }
}
?>