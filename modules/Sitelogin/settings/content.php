<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    FeedController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$selectShapeScript = "<script>

    window.addEvent('domready', function () { 
        if($('showLayouts-wrapper')) { 
            showCustomOption();
            hidePosOption();
        }
    });  
    function hidePosOption(){
        if ($('showForm-wrapper')) {
            if ($('showForm-0').checked) {
               document.getElementById('position-wrapper').hide();                
            } else {
                document.getElementById('position-wrapper').show(); 
            }
        }
    }
    function showCustomOption(){ 
        if ($('showLayouts-wrapper')) {
            if ($('showLayouts-1').checked || $('showLayouts-2').checked) {
               document.getElementById('showShape').options[0].show();                
            } else {
                document.getElementById('showShape').options[0].hide(); 
                if(document.getElementById('showShape').value==1)
                document.getElementById('showShape').value=3;
            }
        }
     }
    </script>";
$showButtons = array(
    'MultiCheckbox',
    'show_buttons',
    array(
        'label' => 'Select the social media buttons you want to show on the widget. (By default all are selected)',
        'description' => '',
        'multiOptions' => array(
            'facebook'=>'Facebook',
            'twitter'=>'Twitter',
            'google'=>'Google',
            'instagram'=>'Instagram',
            'linkedin'=>'Linkedin',            
            'pinterest'=>'Pinterest',            
            'yahoo'=>'Yahoo',
            'outlook'=>'Outlook',
            'flickr'=>'Flickr',
            'vk'=>'Vkontakte',
            ),
        'required' => true,
    )
);
$showLayouts = array(
    'Select',
    'showLayouts',
    array(
        'label' => 'Choose a layout for the widget',
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
        'onchange' => 'showCustomOption()',
        'value' => 4,
    )
);
$showForm = array(
    'Radio',
    'showForm',
    array(
        'label' => 'Want to show login form',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No',
        ),
        'onchange' => 'hidePosOption()',
        'value' => 1,
    )
);
$showShadow = array(
    'Select',
    'showShadow',
    array(
        'label' => 'Select whether to show the social media buttons with shadow or without shadow',
        'multiOptions' => array(
             1 => 'Shadow',
             0 => 'Without Shadow',
        ),
        'value' => 1,
    )
);
$Position = array(
    'Radio',
    'position',
    array(
        'label' => 'On which side do you want to show social media buttons with respect to login form?',
        'multiOptions' => array(
            1 => 'On the top',
            0 => 'On the bottom',
        ),
        'value' => 1,
    )
);

$button_width = array(
      'Text',
      'button_width',
      array(
          'label' => 'Enter the width of the Social Login Buttons. For icon view it will be in pixels and for button view in %.[Note: For the % setting, the maximum width that can be entered is 100. Please set the width as 100 if you want to show single button in a row. Likewise 50 for two buttons, 33.3 for three buttons, 25 for four buttons and 20 for five buttons.]',
          'validators'=>array(
                array('Float', true),
                array('Between', false, array('min' => '1', 'max' => '100', 'inclusive' => true)),
                new Engine_Validate_AtLeast(1),
            ),
          'value' => 30,
      ),
  );
return array(
    array(
        'title' => 'Social Media Buttons With Login Form'.$selectShapeScript,
        'description' => 'This widget provides you buttons to signup or login via Social Sites and you can display login form as well. For the users who are not registered, can also Signup via "Join" link
[Note: The login form has its title already so, give a heading to the widget as per the form\'s position.]',
        'category' => 'Social Login and Sign-up Plugin',
        'type' => 'widget',
        'name' => 'sitelogin.social-login-signup',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                 $showForm,$Position,$showLayouts,$showShadow,$showButtons,$button_width
            ),
        ),
    ),
    array(
    'title' => 'Social Login : Popup for Login and Signup',
    'description' => 'Uses a popup for the login and sign up forms when a user clicks to them via the Mini Menu.',
    'category' => 'Social Login and Sign-up Plugin',
    'type' => 'widget',
    'name' => 'sitelogin.login-or-signup-popup',
  ),
);
?>
