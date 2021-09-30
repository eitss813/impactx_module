<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js');
?>
<?php if ($this->pageIdentity !== 'user-auth-login') : ?>
  <div id='user_auth_popup' style="display:none;">
    <div class="close_icon_container" onclick="parent.Smoothbox.close();">
      <i class="fa fa-times" aria-hidden="true" ></i>
    </div>
    <?php echo $this->action('login','auth', 'sitelogin', array(
      'disableContent' => true,
      'return_url' => '64-' . base64_encode($this->url())
    )); ?>
  </div>
<?php endif; ?>
<?php if (!in_array($this->pageIdentity, array('user-signup-index','sitequicksignup-signup-index'))) : ?>
  <div id='user_signup_popup' style="display:none;">
    <div class="close_icon_container" onclick="parent.Smoothbox.close();">
      <i class="fa fa-times" aria-hidden="true" ></i>
    </div>
    <?php $ifSiteLogin = Engine_Api::_()->hasModuleBootstrap('sitequicksignup'); 
          $signupModule = $ifSiteLogin ? 'sitequicksignup' : 'user';
    ?>  
    <?php echo $this->action('index','signup', $signupModule, array('disableContent' => true)); ?>
  </div>
<?php endif; ?>

<script type='text/javascript'>
  if( !DetectMobileQuick() && !DetectIpad() ) {
    en4.core.runonce.add(function() {
      var setPopupContent = function (event, contentId) {
        event.stop();
        Smoothbox.open($(contentId).get('html'));
        en4.core.reCaptcha.render();
        $('TB_window').addClass('signup_login_popup_wrapper');
        Smoothbox.instance.doAutoResize();
      };
      <?php if (!in_array($this->pageIdentity, array('user-signup-index','sitequicksignup-signup-index'))) : ?>
        $$('.user_signup_link').addEvent('click', function(event) {
          if($('socialsignup_popup_div')) $('socialsignup_popup_div').addClass('socialsignup_popup_div');
          if($('sociallogin_signup_popup')) $('sociallogin_signup_popup').addClass('sociallogin_signup_popup');
          setPopupContent(event, 'user_signup_popup');
        });
      <?php endif; ?>
      <?php if ($this->pageIdentity !== 'user-auth-login') : ?>
        $$('.user_auth_link').addEvent('click', function(event) {
          if($('sociallogin_popup_div')) $('sociallogin_popup_div').addClass('sociallogin_popup_div');
          setPopupContent(event, 'user_auth_popup');
        });
      <?php endif; ?>
    });
  }
</script>
