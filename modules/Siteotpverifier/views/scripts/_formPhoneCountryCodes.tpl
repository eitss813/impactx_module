<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl
  . 'application/modules/Siteotpverifier/externals/scripts/core.js');
$class = 'siteotp_' . rand(1000, 9999);
$defaultCountry = Engine_Api::_()->siteotpverifier()->getDefaultCountry();
if( count($this->countryCodes) > 1 ):
  echo $this->formSelect('country_code', $defaultCountry, array('style' => 'display:none;'), $this->countryCodes);
else:
  echo $this->formHidden('country_code', $this->settings('siteotpverifier.defaultCountry', '+1'), array());
endif;
?>
<div id="siteotp_choice_wapper" class="form-wrapper <?php echo $class ?> dnone" >
  <div class="form-label"></div>
  <div class="form-element">
    <?php 
      if (Engine_Api::_()->hasModuleBootstrap('sitemobile') && Engine_API::_()->siteotpverifier()->isSiteMobileModeEnabled()) {
        $event = 'sm4.siteotpverifier.signup.toggoleElementsClickHandler(this)';
      } else {
        $event = 'en4.siteotpverifier.signup.toggoleElementsClickHandler(this)';
      }
     ?>
    <span>
      <a href="javascript:void(0);" class="siteotp_choice siteotp_phone_choice" onclick="<?php echo $event ?>"><?php echo $this->translate('Create via Phone Number.'); ?></a>
      <a href="javascript:void(0);" class="siteotp_choice siteotp_email_choice" onclick="<?php echo $event ?>"><?php echo $this->translate('Create via Email Address.'); ?></a>
    </span>
  </div>
</div>
<script type="text/javascript">
  var coreObject = (typeof sm4 != 'undefined') ? sm4 : en4;
  coreObject.core.runonce.add(function () {
    coreObject.siteotpverifier.signup.init({
      emailName: '<?php echo $this->emailFieldName; ?>',
      showBothPhoneAndEmail: '<?php echo $this->settings('siteotpverifier.singupShowBothPhoneAndEmail', 1) ?>',
      elementClass: '<?php echo $class ?>',
      autoEmailTemplate: '<?php echo $this->settings('siteotpverifier.signupAutoEmailTemplate', 'se[PHONE_NO]@semail.com')?>',
      formKey: 'siteotp_singup_form_<?php echo rand(1000, 9999); ?>'
    });
  });
</script>
<style>
#phoneno-element #phoneno {
width: -webkit- calc(100% - 80px) !important;
width: -moz- calc(100% - 80px) !important;
width:  calc(100% - 80px) !important;
}
</style>