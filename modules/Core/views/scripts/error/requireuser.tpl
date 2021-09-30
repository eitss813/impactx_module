<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: requireuser.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<!-- todo: 5.2.1 Upgrade => Make the requireUser and Login Form Same -->
<style>
    #user_form_login #facebook-wrapper,
    #user_form_login #twitter-wrapper {
        display: none;
    }
    /* todo: 5.2.1 Upgrade => Login CSS */
    .layout_page_user_auth_login .layout_core_content {
        padding: 20px !important;
    }
    /* todo: 5.2.1 Upgrade => Added missing remember-me field in form */
    .layout_core_content #user_form_login #remember-wrapper {
        position: unset !important;
    }
</style>
<?php
    $this->form->setDescription('');
?>
<?php
    $coreSettings=Engine_Api::_()->getApi('settings', 'core');
$socialSites=Array(0=>'google',1=>'linkedin',2=>'instagram',3=>'pinterest',4=>'flickr',5=>'yahoo',6=>'outlook',7=>'vk',8=>'facebook',9=>'twitter');
foreach ($socialSites as $socialsite) {
$siteintegtration=$socialsite.'IntegrationEnabled';
if($socialsite == 'facebook' || $socialsite == 'twitter'){
$siteEnabled=Engine_Api::_()->sitelogin()->$siteintegtration();
} else {
$siteEnabled = Engine_Api::_()->getDbtable($socialsite, 'sitelogin')->$siteintegtration();
}
if (!empty($siteEnabled)) {
$siteloginSetting='sitelogin_'.$socialsite;
$siteSettings = (array) $coreSettings->$siteloginSetting;

$loginEnable = $siteSettings[$socialsite.'Options'];
if (in_array('login', $loginEnable)) {
$socialsite = ucfirst($socialsite);
$data['render'.$socialsite] = 1;
}
}
}
$enable=$coreSettings->getSetting('sitlogin.loginenable', 1);
$isEnableSocialAccount=isset($data['renderFlickr'])||isset($data['renderTwitter'])||isset($data['renderFacebook'])||isset($data['renderLinkedin'])||isset($data['renderGoogle'])||isset($data['renderInstagram'])||isset($data['renderPinterest'])||isset($data['renderYahoo'])||isset($data['renderOutlook'])||isset($data['renderVk']);

if(empty($this->inwidget)){
$data['showShadow']=$coreSettings->getSetting('sitlogin.loginlayoutshadow', 1);
$data['layout']=$coreSettings->getSetting('sitlogin.loginlayout', 13);

if(in_array($data['layout'],array("1","2","3")))
$layout=1;
elseif(in_array($data['layout'],array("4","5","6")))
$layout=2;
elseif(in_array($data['layout'],array("7","8","9")))
$layout=3;
elseif(in_array($data['layout'],array("10","11","12")))
$layout=4;
elseif(in_array($data['layout'],array("13","14","15")))
$layout=5;

$data['button_width']=$coreSettings->getSetting('sitlogin.loginlayoutwidth',50);

$position=$coreSettings->getSetting('sitelogin.position', 2);

}else{
$data['showShadow']=$coreSettings->getSetting('sitlogin.loginlayoutshadowpopup', 1);
$data['layout']=$coreSettings->getSetting('sitlogin.loginlayoutpopup', 4);

if(in_array($data['layout'],array("1","2","3")))
$layout=1;
elseif(in_array($data['layout'],array("4","5","6")))
$layout=2;
elseif(in_array($data['layout'],array("7","8","9")))
$layout=3;
elseif(in_array($data['layout'],array("10","11","12")))
$layout=4;
elseif(in_array($data['layout'],array("13","14","15")))
$layout=5;

$data['button_width']=$coreSettings->getSetting('sitlogin.loginlayoutwidthpopup',33);

$position=$coreSettings->getSetting('sitelogin.positionpopup', 1);

}
?>
<?php $layoutPos = array(1=> 'left', 2 => 'right', 3 => 'top', 4 => 'bottom')?>
<div id="sociallogin_signin_popup" class="<?php echo empty($this->inwidget)  ? '' : 'sociallogin_signin_popup' ?>">
    <div class="social-signin-layout-<?php echo $layoutPos[$position] ?>">
        <?php if(!empty($isEnableSocialAccount) && $enable): ?>
        <div class="signin_page_heading"><h2><?php echo $this->form->getTitle(); ?></h2></div>
        <?php if(empty($this->inwidget)): ?>
        <?php if($position==1) : ?>
        <!-- left -->
        <div class="social-login-row social-login-row-left" id="sociallogin_popup_div">
            <div class="social-login-column-3">
                <h3>Sign In With a Social Network</h3>
                <?php echo $this->partial('_layout'.$layout.'.tpl', 'sitelogin',$data); ?>
            </div>
            <div class="social-login-column-2"></div>
            <div class="social-login-column-1">
                <?php  echo $this->form->render($this);?>
            </div>
        </div>
        <?php elseif($position==2) : ?>
        <!-- Right -->
        <div class="social-login-row social-login-row-right" id="sociallogin_popup_div">
            <div class="social-login-column-1">
                <?php echo $this->form->render($this) ?>
            </div>
            <div class="social-login-column-2"></div>
            <div class="social-login-column-3">
                <h3>Sign In With a Social Network</h3>
                <?php echo $this->partial('_layout'.$layout.'.tpl', 'sitelogin',$data); ?>
            </div>
        </div>
        <?php elseif($position==4) : ?>
        <!-- Bottom-->
        <div class="social-login-row social-login-row-bottom" id="sociallogin_popup_div">
            <div class="social-login-column-1">
                <?php echo $this->form->render($this) ?>
            </div>
            <div class="social-login-column-2"></div>
            <div class="social-login-column-3">
                <h3>Sign in with a social network</h3>
                <?php echo $this->partial('_layout'.$layout.'.tpl', 'sitelogin',$data); ?>
            </div>
        </div>
        <?php elseif($position==3) : ?>
        <!-- Top -->
        <div class="social-login-row social-login-row-top" id="sociallogin_popup_div">
            <div class="social-login-column-3">
                <h3>Sign In With a Social Network</h3>
                <?php echo $this->partial('_layout'.$layout.'.tpl', 'sitelogin',$data); ?>
            </div>
            <div class="social-login-column-2"></div>
            <div class="social-login-column-1">
                <?php echo $this->form->render($this) ?>
            </div>
        </div>
        <?php endif; ?>

        <?php else : ?>
        <?php if($position==2) :?>
        <div class="social-login-row social-login-row-bottom" id="sociallogin_popup_div">
            <?php echo $this->form->render($this) ?>

            <div id="Sitelogin-loginpopup-div" class="sitelogin-loginpopup-box" style="display:none;">
                <div class="social-loginpopup-column-2">
                    <span>or</span>
                </div>
                <?php echo $this->partial('_layout'.$layout.'.tpl', 'sitelogin',$data); ?>
            </div>
        </div>
        <script type="text/javascript">
            if(document.getElementById("sitemenu_signupform_sociallinks")) {
                document.getElementById("sitemenu_signupform_sociallinks").style.display="none";
            }
            if(document.getElementById("sitehomepagevideo_fb_twi_share_links")) {
                document.getElementById("sitehomepagevideo_fb_twi_share_links").style.display="none";
            }

            if (document.getElementById("user_form_login")){
                var parentDiv = document.querySelectorAll("[id='user_form_login']");
                var i, el;
                if (parentDiv.length > 0) {
                    for (i = 0; i < parentDiv.length; i++) {
                        var el=document.getElementById("Sitelogin-loginpopup-div").cloneNode(true);
                        el.id="Sitelogin-loginpopup-div-"+i;
                        el.style.display="block";

                        if(parentDiv[i].getElement('#facebook-wrapper'))
                            parentDiv[i].getElement('#facebook-wrapper').style.display="none";

                        if(parentDiv[i].getElement('#twitter-wrapper'))
                            parentDiv[i].getElement('#twitter-wrapper').style.display="none";


                        if(parentDiv[i].getElement('div div h3'))
                        {   if(!document.getElementById("Sitelogin-loginpopup-div-"+i))
                            el.inject(parentDiv[i].getElement('div div'),'after');

                        }
                    }
                }
            }
        </script>
        <style type="text/css">
            .sitehomepagevideo_signup_form .sitehomepagevideo_left .sitehomepagevideo_signup_instead_btn {
                top: -135px !important;
                right: 18px !important;
            }
            .sitemenu_signup_instead_btn {
                display: none;
            }
        </style>
        <?php elseif($position==1): ?>
        <div class="social-login-row social-login-row-top" id="sociallogin_popup_div">
            <div id="Sitelogin-loginpopup-div" class="sitelogin-loginpopup-box" style="display:none;">
                <?php echo $this->partial('_layout'.$layout.'.tpl', 'sitelogin',$data); ?>
                <div class="social-loginpopup-column-2">
                    <span>or</span>
                </div>
            </div>
            <?php echo $this->form->render($this) ?>
        </div>
        <script type="text/javascript">
            if(document.getElementById("sitemenu_signupform_sociallinks")) {
                document.getElementById("sitemenu_signupform_sociallinks").style.display="none";
            }
            if(document.getElementById("sitehomepagevideo_fb_twi_share_links")) {
                document.getElementById("sitehomepagevideo_fb_twi_share_links").style.display="none";
            }
            if (document.getElementById("user_form_login")){
                var parentDiv = document.querySelectorAll("[id='user_form_login']");
                var i, el;
                if (parentDiv.length > 0) {
                    for (i = 0; i < parentDiv.length; i++) {
                        if (parentDiv[i].getParent('.layout_page_user_auth_login')) {
                            continue;
                        }
                        var el=document.getElementById("Sitelogin-loginpopup-div").cloneNode(true);
                        el.id="Sitelogin-loginpopup-div-"+i;
                        el.style.display="block";

                        if(parentDiv[i].getElement('#facebook-wrapper'))
                            parentDiv[i].getElement('#facebook-wrapper').style.display="none";

                        if(parentDiv[i].getElement('#twitter-wrapper'))
                            parentDiv[i].getElement('#twitter-wrapper').style.display="none";
                        if(parentDiv[i].getElement('div div h3'))
                        {   if(!document.getElementById("Sitelogin-loginpopup-div-"+i))
                            el.inject(parentDiv[i].getElement('div div h3'),'after');

                        }
                    }
                }
            }
        </script>
        <style type="text/css">
            .sitemenu_signup_instead_btn {
                display: none;
            }
        </style>
        <?php endif; ?>
        <?php endif; ?>
        <?php else : ?>
        <?php echo $this->form->render($this) ?>
        <?php endif; ?>
    </div>
</div>


