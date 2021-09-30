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
<?php if( !$this->userloggedin ): ?>
  <?php echo $this->form->render($this) ?>
  <div class="otp_email_link">
    <?php
    echo $this->htmlLink(array('route' => 'default', 'module' => 'siteotpverifier', 'controller' => 'verifier', 'action' => 'sendmail'), 'Send OTP via email', array('class' => 'smoothbox link_button otpverifier_email_icon'));
    ?>
  </div>
<?php else : ?>
  <div style="width: 100px;height: 100px;text-align: center;">
    <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Siteotpverifier/externals/images/loading.gif" />
  </div>
<?php endif; ?>
<script type="text/javascript">
//<![CDATA[
  function resendotpCode() {
    en4.core.request.send(new Request.JSON({
      url: "<?php echo $this->url(array('module' => 'siteotpverifier', 'controller' => 'auth', 'action' => 'resend'), 'default', true); ?>",
      method: 'post',
      data: {
        format: 'json',
        user_id: '<?php echo $this->user_id ?>',
        type: 'login',
      },
      onRequest: function () {
        var el = new Element('div', {
          'id': 'sent_loading_button'
        });
        var parentDiv = document.getElementById('siteotpverifier_form_verify');
        el.inject(parentDiv.getElement('.form-elements'));
        document.getElementById('sent_loading_button').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Siteotpverifier/externals/images/loading.gif" />';
      },
      onSuccess: function (responseJSON) {
        if (responseJSON.otpSent) {
          document.getElementById('sent_loading_button').innerHTML = '';
        } else {
          document.getElementById('sent_loading_button').innerHTML = responseJSON.errormessage;

        }
      }
    }));

  }

</script>

<?php if( $this->userloggedin ): ?>
  <script type="text/javascript">
    window.onload = function () {
        parent.window.location.href = en4.core.baseUrl;
    }
  </script>
<?php endif; ?>