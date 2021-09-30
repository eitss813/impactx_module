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
<script type="text/javascript">
  var fetchLanguageSettings =function(langauge){
    window.location.href= en4.core.baseUrl+'admin/siteotpverifier/settings/language-editor/lang/'+langauge;
    //alert(level_id);
  }
</script>
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

<div class='siteotpverifier_settings_form'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>
