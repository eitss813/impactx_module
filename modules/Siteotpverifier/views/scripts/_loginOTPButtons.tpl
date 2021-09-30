<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl
  . 'application/modules/Siteotpverifier/externals/scripts/core.js');

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteotpverifier/externals/styles/siteotpverifier_style.css');
?>
<div id="login_via_siteotp_otp_or-wrapper" class="form-wrapper login_via_siteotp_otp_or-wrapper">
  <div class="form-label dnone">&nbsp;</div>
</div>
<div id="login_via_siteotp_otp-wrapper" class="form-wrapper">
  <div id="login_via_siteotp_otp-label" class="form-label dnone">&nbsp;</div>
  <div id="login_via_siteotp_otp-element" class="otp_signin_options">
   <span><?php echo $this->translate("or") ?></span>
    <a href="javascript:void(0)" name="login_via_siteotp_otp_mobile" id="login_via_siteotp_otp_mobile"  onclick="sendotpCode(this, '<?php echo $this->emailFieldName ?>')" tabindex="6831" data-send-action="mobile">
  <?php echo $this->translate("Login with OTP") ?>
    </a>
   <a href="javascript:void(0)" class="dnone" name="login_via_siteotp_otp_email" id="login_via_siteotp_otp_email"  onclick="sendotpCode(this, '<?php echo $this->emailFieldName ?>')" tabindex="6831" data-send-action="email">
    </a>
    <span class="sent_loading_button dnone">
      <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteotpverifier/externals/images/loading.gif" />
    </span>
  </div>
</div>
<div class="login_via_siteotp_wappper" >
  <a href="javascript:void(0)" onclick="showPasswordForm(this)" ><i class="fa fa-arrow-left"></i> <?php echo $this->translate("Back") ?></a>
  <div class="otp_form_submission">
  </div>
</div>

<script type="text/javascript">
  $$('form .login_via_siteotp_wappper').each(function (el) {
    el.inject(el.getParent('form'), 'after');
	$('<?php echo $this->emailFieldName ?>').getParent('.form-wrapper').addClass('form-email-phone-wrapper'); 
  });
  function showPasswordForm(el) {
    el.getParent('.login_via_siteotp_active').removeClass('login_via_siteotp_active');
  }
  function sendotpCode(element, emailFiledName) {
    var form = element.getParent('form');
    var EmailAddress;
    if ($(form).getElement('.form-errors')) {
      $(form).getElement('.form-errors').destroy();
    }
    if (!$(form).getElement('.siteotp_login_errors')) {
      el = new Element('ul', {
        'class': 'siteotp_login_errors form-errors dnone'
      }).inject($(form).getElement('.form-elements'), 'before');
    }
    $(form).getElements('input').each(function (el) {
      if (el.get('id') == emailFiledName) {
        EmailAddress = el.value;
      }
    });

    var IndNum = /^[1-9][0-9]{4,15}$/;
    var email = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})$/;
    if (!(IndNum.test(EmailAddress)) && !(email.test(EmailAddress))) {
      $(form).getElement('.siteotp_login_errors').removeClass('dnone');
      $(form).getElement('.siteotp_login_errors').innerHTML = "<li>" + "Please enter a valid phone number or email address." + "</li>";
    } else {
      $(form).getElement('.siteotp_login_errors').addClass('dnone');
      var formData = $(form).toQueryString().parseQueryString();
      formData.format = 'json';
      formData.phone_no = EmailAddress;
      formData.sendTo = element.get('data-send-action');
      en4.core.request.send(new Request.JSON({
        url: "<?php echo $this->url(array('module' => 'siteotpverifier', 'controller' => 'auth', 'action' => 'sendotponlogin'), 'default', true); ?>",
        method: 'post',
        data: formData,
        onRequest: function () {
          $(form).getElement('.siteotp_login_errors').empty();
        },
        onSuccess: function (responseJSON) {
          if (responseJSON.otpsent) {
            $(form).getParent().addClass('login_via_siteotp_active');
            $(form).getParent().getElement('.otp_form_submission').set(
                    'html', responseJSON.body);
            if ($(form).hasClass('global_form_box')) {
              $(form).getParent().getElement('.otp_form_submission form').removeClass('global_form').addClass('global_form_box');
            }
            //global_form_box
          } else {
            $(form).getElement('.siteotp_login_errors').removeClass('dnone');
            $(form).getElement('.siteotp_login_errors').removeClass('dnone').set('html', "<li>" + responseJSON.errormessage + "</li>");
          }
        }
      }), {evalsScripts: true});
    }
  }
</script>
