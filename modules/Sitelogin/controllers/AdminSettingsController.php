<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    AdminSettingsController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_AdminSettingsController extends Core_Controller_Action_Admin {

    public function indexAction() {

        //MAKE NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main', array(), 'sitelogin_admin_main_settings');

        $form = $this->view->form = new Sitelogin_Form_Admin_Global();

        if (!$this->getRequest()->isPost()) {
           return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();

        if (empty($values['sitelogin_customurl']))
          unset($values['sitelogin_customurl']);
        unset($values['ad_header1']);
        unset($values['ad_header2']);
        unset($values['ad_header3']);
        unset($values['ad_header4']);

        foreach( $values as $key => $value ) {
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
          }
        $form->addNotice('Your changes have been saved.');
        $form->populate($values);
        
    }

    public function googleAction() {
        //MAKE NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main', array(), 'sitelogin_admin_main_integration');
        $this->view->navigationSubMenu = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main_integration', array(), 'sitelogin_admin_main_integration_google');

        $form = $this->view->form = new Sitelogin_Form_Admin_Google();
        $form->populate((array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_google);

        $googleSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_google;
        $subscriptionEnabed = Engine_Api::_()->sitelogin()->isSubsciptionStepEnabled();
        $defaultSubscriptionId = Engine_Api::_()->sitelogin()->getDefaultSubscriptionPlan();
        $this->view->showSubscriptionError = 0;

        if (isset($googleSettings['quickEnable']) && empty($defaultSubscriptionId) && $subscriptionEnabed) {
            $this->view->showSubscriptionError = 1;
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();
        if (empty($values['clientId']) || empty($values['clientSecret'])) {
            $values['clientId'] = '';
            $values['clientSecret'] = '';
            $values['googleOptions'] = '';
        }
        if (Engine_Api::_()->getApi('settings', 'core')->sitelogin_google)
            Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitelogin_google');

        Engine_Api::_()->getApi('settings', 'core')->sitelogin_google = $values;
        
        $form->addNotice('Your changes have been saved.');
        $form->populate($values);
    }

    public function linkedinAction() {
        //MAKE NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main', array(), 'sitelogin_admin_main_integration');
        $this->view->navigationSubMenu = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main_integration', array(), 'sitelogin_admin_main_integration_linkedin');

        $form = $this->view->form = new Sitelogin_Form_Admin_Linkedin();
        $form->populate((array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_linkedin);

        $linkedinSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_linkedin;
        $defaultSubscriptionId = Engine_Api::_()->sitelogin()->getDefaultSubscriptionPlan();
        $subscriptionEnabed = Engine_Api::_()->sitelogin()->isSubsciptionStepEnabled();
        $this->view->showSubscriptionError = 0;

        if (isset($linkedinSettings['quickEnable']) && empty($defaultSubscriptionId) && $subscriptionEnabed) {
            $this->view->showSubscriptionError = 1;
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        
        $values = $form->getValues();
        if (empty($values['clientId']) || empty($values['clientSecret'])) {
            $values['clientId'] = '';
            $values['clientSecret'] = '';
            $values['linkedinOptions'] = '';
        }

        if (!isset($values['linkedinOptions']) || empty($values['linkedinOptions']))
            unset($values['linkedinOptions']);
        //Refresh Settings
        if (Engine_Api::_()->getApi('settings', 'core')->sitelogin_linkedin)
            Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitelogin_linkedin');
        Engine_Api::_()->getApi('settings', 'core')->sitelogin_linkedin = $values;
        
        $form->addNotice('Your changes have been saved.');
        $form->populate($values);
    }

    public function twitterAction() {
        //MAKE NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main', array(), 'sitelogin_admin_main_integration');
        $this->view->navigationSubMenu = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main_integration', array(), 'sitelogin_admin_main_integration_twitter');

        $form = $this->view->form = new Sitelogin_Form_Admin_Twitter();

        $coreSettings=(array) Engine_Api::_()->getApi('settings', 'core')->core_twitter;
        $twitterSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_twitter;
        
        if($coreSettings['enable']=='none'){
            $form->addError('Please enable login for twitter from core settings');
        }
        
        $form->populate(array_merge($twitterSettings,array('clientId'=>$coreSettings['key'],'clientSecret'=>$coreSettings['secret'])));

        // Get classes
        include_once 'Services/Twitter.php';
        include_once 'HTTP/OAuth/Consumer.php';

        if( !class_exists('Services_Twitter', false) ||
            !class_exists('HTTP_OAuth_Consumer', false) ) {
            return $form->addError('Unable to load twitter API classes');
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        
        $values = $form->getValues();
        if (empty($values['clientId']) || empty($values['clientSecret'])) {
            $values['clientId'] = '';
            $values['clientSecret'] = '';
            $values['twitterOptions'] = '';
        } else {

            // Try to check credentials
            try {
                $twitter = new Services_Twitter();
                $oauth = new HTTP_OAuth_Consumer($values['clientId'], $values['clientSecret']);
                //$twitter->setOAuth($oauth);
                $oauth->getRequestToken('https://twitter.com/oauth/request_token');
                $oauth->getAuthorizeUrl('http://twitter.com/oauth/authorize');
        
            } catch( Exception $e ) {
                return $form->addError($e->getMessage());
            }
        }

        if (!isset($values['twitterOptions']) || empty($values['twitterOptions']))
            unset($values['twitterOptions']);
        
        if (Engine_Api::_()->getApi('settings', 'core')->sitelogin_twitter)
            Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitelogin_twitter');

        $core_settings=array('key' => $values['clientId'],'secret' => $values['clientSecret']);
        Engine_Api::_()->getApi('settings', 'core')->core_twitter = $core_settings;
        
        $sitelogin_settings=$values;
        unset($sitelogin_settings['clientId']);
        unset($sitelogin_settings['clientSecret']);
        Engine_Api::_()->getApi('settings', 'core')->sitelogin_twitter = $sitelogin_settings;
        
        $form->addNotice('Your changes have been saved.');
        $form->populate($values);
    }

    public function facebookAction() {
        //MAKE NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main', array(), 'sitelogin_admin_main_integration');
        $this->view->navigationSubMenu = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main_integration', array(), 'sitelogin_admin_main_integration_facebook');

        $form = $this->view->form = new Sitelogin_Form_Admin_Facebook();
        
        $facebookSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_facebook;
        $coreSettings=(array) Engine_Api::_()->getApi('settings', 'core')->core_facebook;
        if(!empty($coreSettings['enable']) && $coreSettings['enable'] =='none'){
            $form->addError('Please enable login for facebook from core settings');
        }
        if (isset($coreSettings['appid']) && isset($coreSettings['secret'])) {
          $form->populate(array_merge($facebookSettings,array('clientId'=>$coreSettings['appid'],'clientSecret'=>$coreSettings['secret'])));
        }
      
        $defaultSubscriptionId = Engine_Api::_()->sitelogin()->getDefaultSubscriptionPlan();
        $subscriptionEnabed = Engine_Api::_()->sitelogin()->isSubsciptionStepEnabled();
        $this->view->showSubscriptionError = 0;

        if (isset($facebookSettings['quickEnable']) && empty($defaultSubscriptionId) && $subscriptionEnabed) {
            $this->view->showSubscriptionError = 1;
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        
        $values = $form->getValues();
        if (empty($values['clientId']) || empty($values['clientSecret'])) {
            $values['clientId'] = '';
            $values['clientSecret'] = '';
            $values['facebookOptions'] = '';
        }
        if (!isset($values['facebookOptions']) || empty($values['facebookOptions']))
            unset($values['facebookOptions']);

        if (Engine_Api::_()->getApi('settings', 'core')->sitelogin_facebook)
            Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitelogin_facebook');

        $core_settings=array('appid' => $values['clientId'],'secret' => $values['clientSecret']);
        Engine_Api::_()->getApi('settings', 'core')->core_facebook = $core_settings;
        
        $sitelogin_settings=$values;
        unset($sitelogin_settings['clientId']);
        unset($sitelogin_settings['clientSecret']);
        Engine_Api::_()->getApi('settings', 'core')->sitelogin_facebook = $sitelogin_settings;
        $form->addNotice('Your changes have been saved.');
        $form->populate($values);
    }

    public function instagramAction() {
        //MAKE NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main', array(), 'sitelogin_admin_main_integration');
        $this->view->navigationSubMenu = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main_integration', array(), 'sitelogin_admin_main_integration_instagram');

        $form = $this->view->form = new Sitelogin_Form_Admin_Instagram();
        $form->populate((array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_instagram);

        $instagramSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_instagram;

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();
        if (empty($values['clientId']) || empty($values['clientSecret'])) {
            $values['clientId'] = '';
            $values['clientSecret'] = '';
            $values['instagramOptions'] = '';
        }
        if (Engine_Api::_()->getApi('settings', 'core')->sitelogin_instagram)
            Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitelogin_instagram');

       
        Engine_Api::_()->getApi('settings', 'core')->sitelogin_instagram = $values;
        $form->addNotice('Your changes have been saved.');
        $form->populate($values);
    }

    public function pinterestAction() {
        //MAKE NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main', array(), 'sitelogin_admin_main_integration');
        $this->view->navigationSubMenu = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main_integration', array(), 'sitelogin_admin_main_integration_pinterest');

        $form = $this->view->form = new Sitelogin_Form_Admin_Pinterest();
        $form->populate((array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_pinterest);

        $pinterestSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_pinterest;

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();
        if (empty($values['clientId']) || empty($values['clientSecret'])) {
            $values['clientId'] = '';
            $values['clientSecret'] = '';
            $values['pinterestOptions'] = '';
        }
        if (Engine_Api::_()->getApi('settings', 'core')->sitelogin_pinterest)
            Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitelogin_pinterest');

       
        Engine_Api::_()->getApi('settings', 'core')->sitelogin_pinterest = $values;
        $form->addNotice('Your changes have been saved.');
        $form->populate($values);
    }

    public function flickrAction() {
        //MAKE NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main', array(), 'sitelogin_admin_main_integration');
        $this->view->navigationSubMenu = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main_integration', array(), 'sitelogin_admin_main_integration_flickr');

        $form = $this->view->form = new Sitelogin_Form_Admin_Flickr();
        $form->populate((array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_flickr);

        $flickrSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_flickr;
        $subscriptionEnabed = Engine_Api::_()->sitelogin()->isSubsciptionStepEnabled();
        $defaultSubscriptionId = Engine_Api::_()->sitelogin()->getDefaultSubscriptionPlan();
        $this->view->showSubscriptionError = 0;

        if (isset($flickrSettings['quickEnable']) && empty($defaultSubscriptionId) && $subscriptionEnabed) {
            $this->view->showSubscriptionError = 1;
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();
        if (empty($values['clientId']) || empty($values['clientSecret'])) {
            $values['clientId'] = '';
            $values['clientSecret'] = '';
            $values['flickrOptions'] = '';
        }
        if (Engine_Api::_()->getApi('settings', 'core')->sitelogin_flickr)
            Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitelogin_flickr');

       
        Engine_Api::_()->getApi('settings', 'core')->sitelogin_flickr = $values;
        $form->addNotice('Your changes have been saved.');
        $form->populate($values);
    }

    public function yahooAction() {
        //MAKE NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main', array(), 'sitelogin_admin_main_integration');
        $this->view->navigationSubMenu = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main_integration', array(), 'sitelogin_admin_main_integration_yahoo');

        $form = $this->view->form = new Sitelogin_Form_Admin_Yahoo();
        $form->populate((array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_yahoo);

        $yahooSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_yahoo;

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();
        if (empty($values['clientId']) || empty($values['clientSecret'])) {
            $values['clientId'] = '';
            $values['clientSecret'] = '';
            $values['yahooOptions'] = '';
        }
        if (Engine_Api::_()->getApi('settings', 'core')->sitelogin_yahoo)
            Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitelogin_yahoo');

       
        Engine_Api::_()->getApi('settings', 'core')->sitelogin_yahoo = $values;
        $form->addNotice('Your changes have been saved.');
        $form->populate($values);
    }

    public function outlookAction() {
        //MAKE NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main', array(), 'sitelogin_admin_main_integration');
        $this->view->navigationSubMenu = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main_integration', array(), 'sitelogin_admin_main_integration_outlook');

        $form = $this->view->form = new Sitelogin_Form_Admin_Outlook();
        $form->populate((array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_outlook);

        $outlookSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_outlook;
        $subscriptionEnabed = Engine_Api::_()->sitelogin()->isSubsciptionStepEnabled();
        $defaultSubscriptionId = Engine_Api::_()->sitelogin()->getDefaultSubscriptionPlan();
        $this->view->showSubscriptionError = 0;

        if (isset($outlookSettings['quickEnable']) && empty($defaultSubscriptionId) && $subscriptionEnabed) {
            $this->view->showSubscriptionError = 1;
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();
        if (empty($values['clientId']) || empty($values['clientSecret'])) {
            $values['clientId'] = '';
            $values['clientSecret'] = '';
            $values['outlookOptions'] = '';
        }
        if (Engine_Api::_()->getApi('settings', 'core')->sitelogin_outlook)
            Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitelogin_outlook');

       
        Engine_Api::_()->getApi('settings', 'core')->sitelogin_outlook = $values;
        $form->addNotice('Your changes have been saved.');
        $form->populate($values);
    }   

    public function vkAction() {
        //MAKE NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main', array(), 'sitelogin_admin_main_integration');
        $this->view->navigationSubMenu = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main_integration', array(), 'sitelogin_admin_main_integration_vk');

        $form = $this->view->form = new Sitelogin_Form_Admin_Vk();
        $form->populate((array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_vk);

        $vkSettings = (array) Engine_Api::_()->getApi('settings', 'core')->sitelogin_vk;
        $subscriptionEnabed = Engine_Api::_()->sitelogin()->isSubsciptionStepEnabled();
        $defaultSubscriptionId = Engine_Api::_()->sitelogin()->getDefaultSubscriptionPlan();
        $this->view->showSubscriptionError = 0;

        if (isset($vkSettings['quickEnable']) && empty($defaultSubscriptionId) && $subscriptionEnabed) {
            $this->view->showSubscriptionError = 1;
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();
        if (empty($values['clientId']) || empty($values['clientSecret'])) {
            $values['clientId'] = '';
            $values['clientSecret'] = '';
            $values['vkOptions'] = '';
        }
        if (Engine_Api::_()->getApi('settings', 'core')->sitelogin_vk)
            Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitelogin_vk');

       
        Engine_Api::_()->getApi('settings', 'core')->sitelogin_vk = $values;
        $form->addNotice('Your changes have been saved.');
        $form->populate($values);
    }                 

    public function faqAction() {
        //MAKE NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main', array(), 'sitelogin_admin_main_faq');
        $this->view->navigationSubMenu = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main_faq', array(), 'sitelogin_admin_main_faq_help');

        
    }
    
    public function appfaqAction() {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main', array(), 'sitelogin_admin_main_faq');
        $this->view->navigationSubMenu = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main_faq', array(), 'sitelogin_admin_main_faq_app');

    }
    
    public function statisticsAction() {
        //MAKE NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main', array(), 'sitelogin_admin_main_stats');
        $result=Engine_Api::_()->getApi('core', 'sitelogin')->getStats();
        $ServieNames=array(
            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
            'google' => 'Google',
            'instagram' => 'Instagram',
            'linkedin' => 'Linkedin',            
            'pinterest' => 'Pinterest',            
            'yahoo' => 'Yahoo',
            'outlook' => 'Outlook',
            'flickr' => 'Flickr',
            'vk' => 'Vkontakte',            
        );
        
        $this->view->serviceNames = $ServieNames;
        $this->view->serviceStatistics = $result;
       
    }
    
    public function manageAction() {
        
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitelogin_admin_main', array(), 'sitelogin_admin_main_manage'); 
        
        $result=array();
        //Check if google login is enable
        $coreSettings=Engine_Api::_()->getApi('settings', 'core');
        $this->view->socialSites=$socialSites=Array('google'=>'Google','linkedin'=>'Linkedin','instagram'=>'Instagram','pinterest'=>'Pinterest','flickr'=>'Flickr','yahoo'=>'Yahoo','outlook'=>'Outlook','vk'=>'Vkontakte','facebook'=>'Facebook','twitter'=>'Twitter');
        foreach ($socialSites as $socialsite => $value) {
            $siteintegtration=$socialsite.'Integration';
            if($socialsite == 'facebook' || $socialsite == 'twitter'){                
                    $siteEnabled=Engine_Api::_()->sitelogin()->$siteintegtration();
            } else {                
                    $siteEnabled = Engine_Api::_()->getDbtable($socialsite, 'sitelogin')->$siteintegtration();
            }            
            $result[$socialsite]['enable']=$siteEnabled;

                $siteloginSetting='sitelogin_'.$socialsite;
                $siteSettings = (array) $coreSettings->$siteloginSetting;
                if(!empty($siteSettings['quickEnable'])){
                    if(in_array($socialsite,array('twitter','instagram','flickr','yahoo','pinterest'))){
                        $result[$socialsite]['quickenable']="NA";
                    } else {
                        $result[$socialsite]['quickenable']=$siteSettings['quickEnable'];
                    }  
                }                
                if(!empty($siteSettings[$socialsite.'Options'])){
                    $loginEnable = $siteSettings[$socialsite.'Options']; 
                    if (in_array('signup', $loginEnable)) {
                        $result[$socialsite]['signup']=1;
                    }
                    if (in_array('login', $loginEnable)) {
                        $result[$socialsite]['login']=1;
                    }
                }            
                        
        }
        $this->view->result=$result;
        
        if (!$this->getRequest()->isPost()) {
            return;
        }
        foreach ($socialSites as $socialsite => $value) {
            $siteloginSetting='sitelogin_'.$socialsite;
            $siteSettings = (array) $coreSettings->$siteloginSetting;
            if(!in_array($socialsite,array('twitter','instagram','flickr','yahoo','pinterest'))){
                $enable=isset($_POST[$socialsite.'_quicksignup'])?1:0;
                Engine_Api::_()->getApi('settings', 'core')->setSetting('sitelogin.'.$socialsite.'.quickEnable',$enable);
            }
            
            //if(!empty($coreSettings->getSetting('sitelogin.'.$socialsite.'.'.$socialsite.'Options',0))) {

            if(isset($siteSettings[$socialsite.'Options'])){
                
                if(isset($siteSettings[$socialsite.'Options'][0]) && !empty($siteSettings[$socialsite.'Options'][0])){
                    $coreSettings->removeSetting('sitelogin.'.$socialsite.'.'.$socialsite.'Options.0');                    
                }
                
                if(isset($siteSettings[$socialsite.'Options'][1]) && !empty($siteSettings[$socialsite.'Options'][1])){
                    $coreSettings->removeSetting('sitelogin.'.$socialsite.'.'.$socialsite.'Options.1');                     
                }

            }            
            $i=0;
            if(isset($_POST[$socialsite.'_signup'])){
                $coreSettings->setSetting('sitelogin.'.$socialsite.'.'.$socialsite.'Options.'.$i++,'signup');
            }                
            
            if(isset($_POST[$socialsite.'_login'])){
                $coreSettings->setSetting('sitelogin.'.$socialsite.'.'.$socialsite.'Options.'.$i,'login');
            }
                
            
        }      
        
        return $this->_helper->redirector->gotoRoute(array('module' => 'sitelogin', 'controller' => 'settings', 'action' => 'manage'), 'admin_default', true);
        
    }
}
