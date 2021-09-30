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
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Siteotpverifier/externals/styles/siteotpverifier_style.css');
?>
<?php echo $this->form->render($this) ?>
<?php
$loginoption = $this->settings('siteotpverifier.allowoption', 'default');
?>
<?php if($loginoption == 'otp' && 0): ?>
<div class="otp_email_link">
  <?php
  echo $this->htmlLink(array('route' => 'default', 'module' => 'siteotpverifier', 'controller' => 'verifier', 'action' => 'sendmail'), 'Send OTP via email', array('class' => 'smoothbox link_button otpverifier_email_icon'));
  ?>
</div>
<?php endif; ?>
<script type="text/javascript">
//<![CDATA[
</script>