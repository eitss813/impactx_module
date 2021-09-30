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
<?php if ($this->pageIdentity !== 'user-signup-index') : ?>
  <div id='user_signup_popup'>
    <div class="close_icon_container" onclick="parent.Smoothbox.close();">
      <i class="fa fa-times" aria-hidden="true" ></i>
    </div>
    <?php echo $this->action('index','signup', 'user', array(
      'disableContent' => true,
      'return_url' => '64-' . base64_encode($this->url())
    )); ?>
  </div>
<?php endif; ?>
<?php if ($this->pageIdentity !== 'user-auth-login') : ?>
  <div id='user_auth_popup'>
    <div class="close_icon_container" onclick="parent.Smoothbox.close();">
      <i class="fa fa-times" aria-hidden="true" ></i>
    </div>
    <?php echo $this->action('login','auth', 'user', array(
      'disableContent' => true,
      'return_url' => '64-' . base64_encode($this->url())
    )); ?>
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
      <?php if ($this->pageIdentity !== 'user-signup-index') : ?>
        $$('.user_signup_link').addEvent('click', function(event) {
          setPopupContent(event, 'user_signup_popup');
        });
      <?php endif; ?>
      <?php if ($this->pageIdentity !== 'user-auth-login') : ?>
        $$('.user_auth_link').addEvent('click', function(event) {
          setPopupContent(event, 'user_auth_popup');
        });
      <?php endif; ?>
    });
  }
</script>
