<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Controller.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Widget_SocialLoginSignupController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        
        // Do not show if logged in
        if( Engine_Api::_()->user()->getViewer()->getIdentity() ) {
            $this->setNoRender();
            return;
        }
        
         $this->view->showForm=$showForm=$this->_getParam('showForm',1);
        // Display form
        if($showForm) {
            $form = $this->view->form = new User_Form_Login(array(
                'mode' => 'column',
            ));
            $form->setTitle(null)->setDescription(null);
            $form->removeElement('forgot');

            // Facebook login
            if( 'none' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable ) {
                $form->removeElement('facebook');
            }
    
            // Check for recaptcha - it's too fat
            $this->view->noForm = false;
            if( ($captcha = $form->getElement('captcha')) instanceof Zend_Form_Element_Captcha && 
                $captcha->getCaptcha() instanceof Zend_Captcha_ReCaptcha ) {
                $this->view->noForm = true;
                $emailFieldName = $form->getEmailElementFieldName();
                $form->removeElement($emailFieldName);
                $form->removeElement('password');
                $form->removeElement('captcha');
                $form->removeElement('submit');
                $form->removeElement('remember');
                $form->removeDisplayGroup('buttons');
            }
        }
                
        $showlayout=$this->_getParam('showLayouts',4); 
        if(in_array($showlayout,array("1","2","3")))
            $layout=1;
        elseif(in_array($showlayout,array("4","5","6")))
            $layout=2;
        elseif(in_array($showlayout,array("7","8","9")))
            $layout=3;
        elseif(in_array($showlayout,array("11","10","12")))
            $layout=4;
        elseif(in_array($showlayout,array("13","14","15")))
            $layout=5;
        
        $this->view->layout=$layout;  
        
        $socialSites=$this->_getParam('show_buttons');
        //$socialSites=Array(0=>'google',1=>'linkedin',2=>'instagram',3=>'pinterest',4=>'flickr',5=>'yahoo',6=>'outlook',7=>'vk',8=>'facebook',9=>'twitter');
        if (isset($socialSites)) {
            foreach ($socialSites as $socialsite) {
                $siteintegtration=$socialsite.'IntegrationEnabled';
                if($socialsite == 'facebook' || $socialsite == 'twitter'){                
                    $siteEnabled=Engine_Api::_()->sitelogin()->$siteintegtration();
                } else {                
                    $siteEnabled = Engine_Api::_()->getDbtable($socialsite, 'sitelogin')->$siteintegtration();
                }                
                if (!empty($siteEnabled)) {
                    $socialsite = ucfirst($socialsite);
                    $data['render'.$socialsite] = 1;                
                }            
            }
        }
        $this->view->position=$this->_getParam('position',1);
        $data['layout']=$showlayout;
        $data['showShadow']=$this->_getParam('showShadow',1);
        $data['button_width']=$this->_getParam('button_width',30);
        $isEnableSocialAccount=isset($data['renderFlickr'])||isset($data['renderTwitter'])||isset($data['renderFacebook'])||isset($data['renderLinkedin'])||isset($data['renderGoogle'])||isset($data['renderInstagram'])||isset($data['renderPinterest'])||isset($data['renderYahoo'])||isset($data['renderOutlook'])||isset($data['renderVk']);
        if(empty($isEnableSocialAccount)&& empty($showForm))
            $this->setNoRender();
        
        $this->view->data=$data;
        
    }
    
    public function getCacheKey()
    {
        return false;
    }

}
