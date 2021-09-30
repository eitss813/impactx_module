<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Global.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelogin_Form_Admin_Global extends Engine_Form {

    public function init() {
        
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        //GENERAL HEADING
        $this
                ->setTitle('Global Settings')
                ->setDescription('These settings affect all members in your community.');

        $coreSettings=Engine_Api::_()->getApi('settings', 'core');
        
        $this->addElement('Radio', 'sitelogin_redirectlink', array(
            'label' => 'Redirect URL',
            'description' => 'On which Url you want to redirect user after signup process?',
            'multiOptions' => array(
                2 => 'Member Home Page (Default)',
                1 => 'User Own Profile Page',                
                3 => 'Editing his/ her own profile',
                4 => 'Other',
                
            ),
            'value' => $coreSettings->getSetting('sitelogin.redirectlink', 2),
            'onchange'=>'showCustomOption(this)',
        ));
        
        $this->addElement('Text','sitelogin_customurl',array(
        'description' => 'Note: The format for this URL is: blogs/manage',    
        'value'=>$coreSettings->getSetting('sitelogin.customurl', ""),
            
        ));
        
        $this->addElement('Dummy', 'ad_header1', array(
            'label' => 'For Signup Page',
            'description' =>""
        ));
        $this->ad_header1->getDecorator('Label')->setOption('style', 'font-weight:bolder;color: #000;width:100%');
        
        $this->addElement('Radio', 'sitlogin_signupenable', array(
            'label' => 'Enable',
            'description' => 'Enable display of Social Sites button?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No',                               
            ),
            'value' => $coreSettings->getSetting('sitlogin.signupenable', 1),
        ));
        
        $this->addElement('Select', 'sitlogin_signuplayout', array(
            'label' => 'Layout',
            'description' => 'Select the layout for social media buttons. <a title="Preview - Layouts" href="application/modules/Sitelogin/externals/images/layout_preview.png" target="_blank" class="sitelogin_icon_view" > </a>',
            'multiOptions' => array(
                4 => 'Icon View (Circle)',
                5 => 'Icon View (Round)',
                6 => 'Icon View (Square)',
                1 => 'Icon Labelled View (Circle)',
                2 => 'Icon Labelled View (Round)',
                3 => 'Icon Labelled View (Square)',
                7 => 'Button View (Round)',
                8 => 'Button View (Rounded Corner)',
                9 => 'Button View (Square)',
                10 => 'Button Shaded View (Round)',
                11 => 'Button Shaded View (Rounded Corner)',
                12 => 'Button Shaded View (Square)',
                13 => 'Arrow Button View (Round)',
                14 => 'Arrow Button View (Rounded Corner)',
                15 => 'Arrow Button View (Square)',  
            ),
            'value' => $coreSettings->getSetting('sitlogin.signuplayout', 13),
        ));
        $this->sitlogin_signuplayout->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
       
        $this->addElement('Text','sitlogin_signuplayoutwidth',array(
            'label'=>'Width of Social Login Buttons',
            'validators'=>array(
                array('Float', true),
                array('Between', false, array('min' => '1', 'max' => '100', 'inclusive' => true)),
                new Engine_Validate_AtLeast(1),
            ),  
            'description'=> 'Enter the width of the Social Login Buttons. For icon view it will be in pixels and for button view in %.[Note: For the % setting, the maximum width that can be entered is 100. Please set the width as 100 if you want to show single button in a row. Likewise 50 for two buttons, 33.3 for three buttons, 25 for four buttons and 20 for five buttons.]',
            'value'=>$coreSettings->getSetting('sitlogin.signuplayoutwidth',50),
        ));
  
        $this->addElement('Select', 'sitlogin_signuplayoutshadow', array(
            'label' => 'Look of Buttons',
            'description' => 'Select whether to show the social media buttons with shadow or without shadow.',
            'multiOptions' => array(
                1 => 'Shadow',
                0 => 'Without Shadow',
            ),
            'value' => $coreSettings->getSetting('sitlogin.signuplayoutshadow', 1)
        ));
        $this->addElement('Select', 'sitelogin_signup_position', array(
            'label' => 'Position of Social Media Buttons',
            'description' => 'On which side do you want to show social media buttons?',
            'multiOptions' => array(
                1 => 'On the Left',
                2 => 'On the right',
                3 => 'On the top',
                4 => 'On the bottom',
                
            ),
            'value' => $coreSettings->getSetting('sitelogin.signup.position', 2),
        ));
        
        $this->addElement('Dummy', 'ad_header3', array(
            'label' => 'For Signup Pop Up',
            'description' =>""
        ));
        $this->ad_header3->getDecorator('Label')->setOption('style', 'font-weight:bolder;color: #000;width:100%');
        
        $this->addElement('Select', 'sitlogin_signuplayoutpopup', array(
            'label' => 'Layout',
            'description' => 'Select the layout for social media buttons. <a title="Preview - Layouts" href="application/modules/Sitelogin/externals/images/layout_preview.png" target="_blank" class="sitelogin_icon_view" > </a>',
            'multiOptions' => array(
                4 => 'Icon View (Circle)',
                5 => 'Icon View (Round)',
                6 => 'Icon View (Square)',
                1 => 'Icon Labelled View (Circle)',
                2 => 'Icon Labelled View (Round)',
                3 => 'Icon Labelled View (Square)',
                7 => 'Button View (Round)',
                8 => 'Button View (Rounded Corner)',
                9 => 'Button View (Square)',
                10 => 'Button Shaded View (Round)',
                11 => 'Button Shaded View (Rounded Corner)',
                12 => 'Button Shaded View (Square)',
                13 => 'Arrow Button View (Round)',
                14 => 'Arrow Button View (Rounded Corner)',
                15 => 'Arrow Button View (Square)',  
            ),
            'value' => $coreSettings->getSetting('sitlogin.signuplayoutpopup', 4),
        ));
        $this->sitlogin_signuplayoutpopup->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
        $this->addElement('Text','sitlogin_signuplayoutwidthpopup',array(
            'label'=>'Width of Social Login Buttons',
            'validators'=>array(
                array('Float', true),
                array('Between', false, array('min' => '1', 'max' => '100', 'inclusive' => true)),
                new Engine_Validate_AtLeast(1),
            ),  
            'description'=> 'Enter the width of the Social Login Buttons. For icon view it will be in pixels and for button view in %.[Note: For the % setting, the maximum width that can be entered is 100. Please set the width as 100 if you want to show single button in a row. Likewise 50 for two buttons, 33.3 for three buttons, 25 for four buttons and 20 for five buttons.]',
            'value'=>$coreSettings->getSetting('sitlogin.signuplayoutwidthpopup',33),
        ));
             
        $this->addElement('Select', 'sitlogin_signuplayoutshadowpopup', array(
            'label' => 'Look of Buttons',
            'description' => 'Select whether to show the social media buttons with shadow or without shadow.',
            'multiOptions' => array(
                1 => 'Shadow',
                0 => 'Without Shadow',
            ),
            'value' => $coreSettings->getSetting('sitlogin.signuplayoutshadowpopup', 1)
        ));
        
        $this->addElement('Select', 'sitelogin_signup_positionpopup', array(
            'label' => 'Position of Social Media Buttons',
            'description' => 'On which side do you want to show social media buttons?',
            'multiOptions' => array(
                1 => 'On the top',
                2 => 'On the bottom',
                
            ),
            'value' => $coreSettings->getSetting('sitelogin.signup.positionpopup', 1),
        ));
        
                
        $this->addElement('Dummy', 'ad_header2', array(
            'label' => 'For Login Page',
            'description' =>""
        ));
        $this->ad_header2->getDecorator('Label')->setOption('style', 'font-weight:bolder;color: #000;width:100%');
     
        $this->addElement('Radio', 'sitlogin_loginenable', array(
            'label' => 'Enable',
            'description' => 'Enable display of Social Sites button?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No',                               
            ),
            'value' => $coreSettings->getSetting('sitlogin.loginenable', 1),
        ));
        
        $this->addElement('Select', 'sitlogin_loginlayout', array(
            'label' => 'Layout',
            'description' => 'Select the layout for social media buttons. <a title="Preview - Layouts" href="application/modules/Sitelogin/externals/images/layout_preview.png" target="_blank" class="sitelogin_icon_view" > </a>',
            'multiOptions' => array(
                4 => 'Icon View (Circle)',
                5 => 'Icon View (Round)',
                6 => 'Icon View (Square)',
                1 => 'Icon Labelled View (Circle)',
                2 => 'Icon Labelled View (Round)',
                3 => 'Icon Labelled View (Square)',
                7 => 'Button View (Round)',
                8 => 'Button View (Rounded Corner)',
                9 => 'Button View (Square)',
                10 => 'Button Shaded View (Round)',
                11 => 'Button Shaded View (Rounded Corner)',
                12 => 'Button Shaded View (Square)',
                13 => 'Arrow Button View (Round)',
                14 => 'Arrow Button View (Rounded Corner)',
                15 => 'Arrow Button View (Square)',         
            ),
            'value' => $coreSettings->getSetting('sitlogin.loginlayout', 13)
        ));
        $this->sitlogin_loginlayout->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
        $this->addElement('Text','sitlogin_loginlayoutwidth',array(
        'label'=>'Width of Social Login Buttons',
            'validators' => array(
              array('Float', true),
              array('Between', false, array('min' => '1', 'max' => '100', 'inclusive' => true)),
              new Engine_Validate_AtLeast(1),
            ),
        'description'=> 'Enter the width of the Social Login Buttons. For icon view it will be in pixels and for button view in %.[Note: For the % setting, the maximum width that can be entered is 100. Please set the width as 100 if you want to show single button in a row. Likewise 50 for two buttons, 33.3 for three buttons, 25 for four buttons and 20 for five buttons.]',
        'value'=>$coreSettings->getSetting('sitlogin.loginlayoutwidth',50),
        ));        

        $this->addElement('Select', 'sitlogin_loginlayoutshadow', array(
            'label' => 'Look of Buttons',
            'description' => 'Select whether to show the social media buttons with shadow or without shadow.',
            'multiOptions' => array(
                1 => 'Shadow',
                0 => 'Without Shadow',
            ),
            'value' => $coreSettings->getSetting('sitlogin.loginlayoutshadow', 1)
        ));
       
        $this->addElement('Select', 'sitelogin_position', array(
            'label' => 'Position of Social Media Buttons',
            'description' => 'On which side do you want to show social media buttons?',
            'multiOptions' => array(
                1 => 'On the Left',
                2 => 'On the right',
                3 => 'On the top',
                4 => 'On the bottom',
                
            ),
            'value' => $coreSettings->getSetting('sitelogin.position', 2),
        ));
        $this->addElement('Dummy', 'ad_header4', array(
            'label' => 'For Login Pop Up',
            'description' =>""
        ));
        $this->ad_header4->getDecorator('Label')->setOption('style', 'font-weight:bolder;color: #000;width:100%');
        
        $this->addElement('Select', 'sitlogin_loginlayoutpopup', array(
            'label' => 'Layout',
            'description' => 'Select the layout for social media buttons. <a title="Preview - Layouts" href="application/modules/Sitelogin/externals/images/layout_preview.png" target="_blank" class="sitelogin_icon_view" > </a>',
            'multiOptions' => array(
                4 => 'Icon View (Circle)',
                5 => 'Icon View (Round)',
                6 => 'Icon View (Square)',
                1 => 'Icon Labelled View (Circle)',
                2 => 'Icon Labelled View (Round)',
                3 => 'Icon Labelled View (Square)',
                7 => 'Button View (Round)',
                8 => 'Button View (Rounded Corner)',
                9 => 'Button View (Square)',
                10 => 'Button Shaded View (Round)',
                11 => 'Button Shaded View (Rounded Corner)',
                12 => 'Button Shaded View (Square)',
                13 => 'Arrow Button View (Round)',
                14 => 'Arrow Button View (Rounded Corner)',
                15 => 'Arrow Button View (Square)',              
            ),
            'value' => $coreSettings->getSetting('sitlogin.loginlayoutpopup', 4)
        ));
        $this->sitlogin_loginlayoutpopup->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
        $this->addElement('Text','sitlogin_loginlayoutwidthpopup',array(
        'label'=>'Width of Social Login Buttons',
            'validators' => array(
              array('Float', true),
              array('Between', false, array('min' => '1', 'max' => '100', 'inclusive' => true)),
              new Engine_Validate_AtLeast(1),
            ),
        'description'=> 'Enter the width of the Social Login Buttons. For icon view it will be in pixels and for button view in %.[Note: For the % setting, the maximum width that can be entered is 100. Please set the width as 100 if you want to show single button in a row. Likewise 50 for two buttons, 33.3 for three buttons, 25 for four buttons and 20 for five buttons.]',
        'value'=>$coreSettings->getSetting('sitlogin.loginlayoutwidthpopup',33),
        ));        
        
        $this->addElement('Select', 'sitlogin_loginlayoutshadowpopup', array(
            'label' => 'Look of Buttons',
            'description' => 'Select whether to show the social media buttons with shadow or without shadow.',
            'multiOptions' => array(
                1 => 'Shadow',
                0 => 'Without Shadow',
            ),
            'value' => $coreSettings->getSetting('sitlogin.loginlayoutshadowpopup', 1)
        ));
       
        $this->addElement('Select', 'sitelogin_positionpopup', array(
            'label' => 'Position of Social Media Buttons',
            'description' => 'On which side do you want to show social media buttons?',
            'multiOptions' => array(
                1 => 'On the top',
                2 => 'On the bottom',
            ),
            'value' => $coreSettings->getSetting('sitelogin.positionpopup', 1),
        ));
                
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));

    }

}
