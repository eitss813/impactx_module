<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    index.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
  <?php echo 'One Time Password (OTP) Plugin'; ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='siteotpverifier_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<?php $messages = array(); ?>
<?php if( !Engine_Api::_()->getApi('core', 'siteotpverifier')->enabledOTPClient() ): ?>
  <?php
  $messages[] = sprintf('You have not configured and enabled any SMS service to send OTP verification code to your site users. Functionality of this plugin will only work when a service is enabled. So, to enable the service, please click <a href="%s">here</a>.
', $this->url(array(
      'module' => 'siteotpverifier',
      'controller' => 'services',
      ), 'admin_default', true));
  ?>
<?php else: ?>
  <?php $service = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.integration'); ?>
  <?php if( $service == 'testmode' ): ?>
    <?php
    $messages[] = sprintf('You have enabled \'Virtual SMS Client\' service to send OTP verification code. This service is for testing purpose only. If you want your site users to receive OTP verification code then please enable other SMS services from <a href="%s">here</a>.
    ', $this->url(array(
        'module' => 'siteotpverifier',
        'controller' => 'services',
        ), 'admin_default', true));
    ?>
  <?php endif; ?>
<?php endif; ?>
<?php $step_row = Engine_Api::_()->getDbtable('signup', 'user')->fetchRow(Engine_Api::_()->getDbtable('signup', 'user')->select()->where('class = ?', 'Siteotpverifier_Plugin_Signup_Otpverify'));
 if( empty($step_row->enable) ):
  $messages[] = sprintf('You have not enabled \'OTP Verification\' from the \'Signup Process\'. Functionality of this plugin won\'t work until it is enabled. To enable \'OTP Verification\', please click <a href="%s">here</a>.', $this->url(array(
      'module' => 'user',
      'controller' => 'signup',
    'action' => 'index',
    'signup_id' => $step_row->signup_id
      ), 'admin_default', true));
endif;
?>
<?php foreach ( $messages as $message ): ?>
  <div class='tip'>
    <span>
  <?php echo $message; ?>
    </span>
  </div>
<?php endforeach; ?>
<div class='siteotpverifier_settings_form'>
  <div class='settings'>
<?php echo $this->form->render($this); ?>
  </div>
</div>

<script type="text/javascript">
  function showPhoneNumberFieldSignupCase() {
    var value = $('singupUserPhone-wrapper').getElement('input[name=singupUserPhone]:checked').get('value');
    if (value == 0) {
      $('singupShowBothPhoneAndEmail-wrapper').setStyle('display', 'none');
      $('singupRequirePhone-wrapper').setStyle('display', 'none');
      $('signupAutoEmailTemplate-wrapper').setStyle('display', 'none');
    } else {
      $('singupShowBothPhoneAndEmail-wrapper').setStyle('display', 'block');
      showSignupFieldsBaseSetting();
    }
  }
  function showSignupFieldsBaseSetting() {
    var value = $('singupShowBothPhoneAndEmail-wrapper').getElement('input[name=singupShowBothPhoneAndEmail]:checked').get('value');
    if (value == 0) {
      $('singupRequirePhone-wrapper').setStyle('display', 'none');
      $('signupAutoEmailTemplate-wrapper').setStyle('display', 'block');
    } else {
      $('signupAutoEmailTemplate-wrapper').setStyle('display', 'none');
      $('singupRequirePhone-wrapper').setStyle('display', 'block');
    }
  }
  en4.core.runonce.add(function () {
    showPhoneNumberFieldSignupCase();
  });
</script>