<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: account.tpl 10143 2014-03-26 16:18:25Z andres $
 * @author     John
 */
?>
<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()
->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey")
?>
<style>
#signup_account_form #name-wrapper {
  display: none;
}
#pswd_info > ul > li{
    flex: 1;
}
</style>
<script type="text/javascript">
//<![CDATA[
  window.addEvent('load', function () {
    if ($('username') && $('profile_address')) {
      $('profile_address').innerHTML = $('profile_address')
              .innerHTML
              .replace('<?php echo /* $this->translate( */'yourname'/* ) */ ?>',
                      '<span id="profile_address_text"><?php echo $this->translate('yourname') ?></span>');

      $('username').addEvent('keyup', function () {
        var text = '<?php echo $this->translate('yourname') ?>';
        if (this.value != '') {
          text = this.value;
        }

        $('profile_address_text').innerHTML = text.replace(/[^a-z0-9]/gi, '');
      });
      // trigger on page-load
      if ($('username').value.length) {
        $('username').fireEvent('keyup');
      }
    }
  });
//]]>
</script>
<?php 
  $contentHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Content');
  $siteloginSignupPopUp = !$contentHelper->getEnabled();
?>
<?php
//Check if google login is enable
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
            if (in_array('signup', $loginEnable)) {
                $socialsite = ucfirst($socialsite);
                $data['render'.$socialsite] = 1;
            }
        }            
    }
$enable=$coreSettings->getSetting('sitlogin.signupenable', 1);
$isEnableSocialAccount=isset($data['renderFlickr'])||isset($data['renderTwitter'])||isset($data['renderFacebook'])||isset($data['renderLinkedin'])||isset($data['renderGoogle'])||isset($data['renderInstagram'])||isset($data['renderPinterest'])||isset($data['renderYahoo'])||isset($data['renderOutlook'])||isset($data['renderVk']);

if ($siteloginSignupPopUp) {
    $data['showShadow']=$coreSettings->getSetting('sitlogin.signuplayoutshadowpopup', 1);
    $data['layout']=$coreSettings->getSetting('sitlogin.signuplayoutpopup', 4);

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

    $data['button_width']=$coreSettings->getSetting('sitlogin.signuplayoutwidthpopup',33);
    $position=$coreSettings->getSetting('sitelogin.signup.positionpopup', 1);
}else{
    $data['showShadow']=$coreSettings->getSetting('sitlogin.signuplayoutshadow', 1);
    $data['layout']=$coreSettings->getSetting('sitlogin.signuplayout', 13);

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

    $data['button_width']=$coreSettings->getSetting('sitlogin.signuplayoutwidth',50);
    $position=$coreSettings->getSetting('sitelogin.signup.position', 2);
}
    
?>
<?php $layoutPos = array(1=> 'left', 2 => 'right', 3 => 'top', 4 => 'bottom')?>
<div id="<?php echo $siteloginSignupPopUp ? 'sociallogin_signup_popup' : 'sociallogin_signup_page' ?>">
<div class="social-signup-layout-<?php echo $layoutPos[$position] ?>">

<?php if( $isEnableSocialAccount && $enable ): ?>
<?php if ($siteloginSignupPopUp): ?>
<?php if($position==2) : ?>
<!-- Bottom-->
    <div class="social-signup-row social-signup-row-bottom" id="socialsignup_popup_div">
        <?php echo $this->form->render($this) ?>
      <div id="Sitelogin-Signuppopup-div" class="sitelogin-signuppopup-box" style="display:none;"> 
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
            if (document.getElementById("signup_account_form")){
                var parentDiv = document.querySelectorAll("[id='signup_account_form']");
                var i, el;
                if (parentDiv.length > 0) {
                    for (i = 0; i < parentDiv.length; i++) {
          
                        var el=document.getElementById("Sitelogin-Signuppopup-div").cloneNode(true);
                        el.id="Sitelogin-Signuppopup-div-"+i;
                        el.style.display="block";

                        if(parentDiv[i].getElement('#facebook-wrapper'))
                            parentDiv[i].getElement('#facebook-wrapper').style.display="none";

                        if(parentDiv[i].getElement('#twitter-wrapper'))
                            parentDiv[i].getElement('#twitter-wrapper').style.display="none"; 
                        
                        if(parentDiv[i].getElement('div div h3'))
                        {   if(!document.getElementById("Sitelogin-Signuppopup-div-"+i))
                            el.inject(parentDiv[i].getElement('div div'),'after');
                        }
                        
                    }
                }
            }           
    </script>
    <style>
      .sitehomepagevideo_signup_form .sitehomepagevideo_left .sitehomepagevideo_login_instead_btn {
           top: -119px !important;
      }
      .sitemenu_login_instead_btn {
          display: none;
      }
    </style>
<?php elseif($position==1) : ?>
<!-- Top -->
<div class="social-signup-row social-signup-row-top" id="socialsignup_popup_div">
  <div id="Sitelogin-Signuppopup-div" class="sitelogin-signuppopup-box" style="display:none;"> 
    <?php echo $this->partial('_layout'.$layout.'.tpl', 'sitelogin',$data); ?>
    <div class="social-loginpopup-column-2">
                <span>or</span>
                </div>
  </div>
    <?php echo $this->form->render($this) ?>
    <script type="text/javascript">
        if(document.getElementById("sitemenu_signupform_sociallinks")) {
          document.getElementById("sitemenu_signupform_sociallinks").style.display="none";
        }
        if(document.getElementById("sitehomepagevideo_fb_twi_share_links")) {
          document.getElementById("sitehomepagevideo_fb_twi_share_links").style.display="none";
        }
            if (document.getElementById("signup_account_form")){
                var parentDiv = document.querySelectorAll("[id='signup_account_form']");
                var i, el;
                if (parentDiv.length > 0) {
                    for (i = 0; i < parentDiv.length; i++) {
          
                        var el=document.getElementById("Sitelogin-Signuppopup-div").cloneNode(true);
                        el.id="Sitelogin-Signuppopup-div-"+i;
                        el.style.display="block";

                        if(parentDiv[i].getElement('#facebook-wrapper'))
                            parentDiv[i].getElement('#facebook-wrapper').style.display="none";

                        if(parentDiv[i].getElement('#twitter-wrapper'))
                            parentDiv[i].getElement('#twitter-wrapper').style.display="none"; 
                        
                        if(parentDiv[i].getElement('div div h3'))
                        {   if(!document.getElementById("Sitelogin-Signuppopup-div-"+i))
                            el.inject(parentDiv[i].getElement('div div h3'),'after');
                        }
                        
                    }
                }
            }           
    </script>
    <style type="text/css">
        .sitemenu_login_instead_btn {
          display: none;
      }
    </style>
</div>
<?php endif; ?>
<?php else: ?>
  <div class="signup_page_heading"><h2><?php echo $this->form->getTitle(); ?></h2></div>
<?php if($position==1) : ?>
<!-- left -->
<div class="social-signup-row social-signup-row-left" id="socialsignup_popup_div">
  <div class="social-signup-column-3">
    <h3>Sign Up With a Social Network</h3>
    <?php echo $this->partial('_layout'.$layout.'.tpl', 'sitelogin',$data); ?>
  </div>

  <div class="social-signup-column-2"></div>

  <div class="social-signup-column-1">
    <?php  echo $this->form->render($this);?>
  </div>
</div>
<?php elseif($position==2) : ?>
<!-- Right -->
<div class="social-signup-row social-signup-row-right" id="socialsignup_popup_div">
  <div class="social-signup-column-1">
    <?php echo $this->form->render($this) ?>
  </div>

  <div class="social-signup-column-2"></div>

  <div class="social-signup-column-3">
    <h3>Sign Up With a Social Network</h3>
    <?php echo $this->partial('_layout'.$layout.'.tpl', 'sitelogin',$data); ?>
  </div>
</div>
<?php elseif($position==4) : ?>
<!-- Bottom-->
<div class="social-signup-row social-signup-row-bottom" id="socialsignup_popup_div">
  <div class="social-signup-column-1">
    <?php echo $this->form->render($this) ?>
  </div>

  <div class="social-signup-column-2"></div>

  <div class="social-signup-column-3">
    <h3>Sign Up With a Social Network</h3>
    <?php echo $this->partial('_layout'.$layout.'.tpl', 'sitelogin',$data); ?>
  </div>
</div>
<?php elseif($position==3) : ?>
<!-- Top -->
<div class="social-signup-row social-signup-row-top" id="socialsignup_popup_div">
  <div class="social-signup-column-3">
    <h3>Sign Up With a Social Network</h3>
    <?php echo $this->partial('_layout'.$layout.'.tpl', 'sitelogin',$data); ?>
  </div>

  <div class="social-signup-column-2"></div>

  <div class="social-signup-column-1">
    <?php echo $this->form->render($this) ?>
  </div>
</div>
<?php endif; ?>
<?php endif; ?>
<?php else: ?>
<?php echo $this->form->render($this) ?>
<?php endif; ?>
</div>
</div>

<script type="text/javascript">
    en4.core.runonce.add(function() {
        if(document.getElementById('location') && (('<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>' && '<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
            var autocompleteSECreateLocation = new google.maps.places.Autocomplete(document.getElementById('location'));
        <?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/locationWithAddress.tpl'; ?>
        }
    });
</script>