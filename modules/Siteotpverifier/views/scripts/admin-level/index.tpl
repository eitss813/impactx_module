<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    siteotpverifier
 * @copyright  Copyright 2016-2017 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2017-03-08 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  var fetchLevelSettings =function(level_id){
    window.location.href= en4.core.baseUrl+'admin/siteotpverifier/level/index/id/'+level_id;
    //alert(level_id);
  }
</script>
<h2 class="fleft">
  <?php echo $this->translate('One Time Password (OTP) Plugin');?>
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
<div class='clear siteotpverifier_settings_form'>
  <div class='settings'>

    <?php echo $this->form->render($this); ?>
  </div>
</div>