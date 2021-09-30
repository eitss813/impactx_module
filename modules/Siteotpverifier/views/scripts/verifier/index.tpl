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
  function resendotpCode() {
    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + "siteotpverifier/auth/resend",
      method: 'post',
      data: {
        format: 'json',
        user_id: '<?php echo $this->user_id ?>',
        type: 'login',
      },
      onRequest: function () {
        if($('sent_otp_errors')) {
            $('sent_otp_errors').empty();
        }
        if(!$('sent_loading_button')) {
            var el = new Element('div', {
              'id': 'sent_loading_button'
            });
            var parentDiv = document.getElementById('siteotpverifier_login_form_verify').getElementById('buttons-element');
            el.inject(parentDiv);
        }
        document.getElementById('sent_loading_button').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Siteotpverifier/externals/images/loading.gif" />';
      },
      onSuccess: function (responseJSON) {
          document.getElementById('sent_loading_button').innerHTML = '';
        if (responseJSON.otpSent) {
        } else {
            if(!$('sent_otp_errors')) {
                var el = new Element('ul', {
                  'id': 'sent_otp_errors',
					'class':'form-errors'
                });
                var parentDiv = document.getElementById('siteotpverifier_login_form_verify');
                el.inject(parentDiv.getElement('.form-elements'), 'before');
            }
          document.getElementById('sent_otp_errors').innerHTML = '<li>'+responseJSON.errormessage+'</li>';
        }
      }
    }));
  }

</script>