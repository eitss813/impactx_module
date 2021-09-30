<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Sitelogin
* @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    index.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<h2>
    <?php echo $this->translate("Social Login and Sign-up Plugin") ?>
</h2>

<?php if( count($this->navigation) ): ?>
      <div class='tabs seaocore_admin_tabs clr'>
        <?php
    // Render the menu
    //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
      </div>
    <?php endif; ?>
    <?php if( count($this->navigationSubMenu) ): ?>
      <div class='tabs seaocore_admin_tabs clr'>
        <?php
    // Render the menu
    //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigationSubMenu)->render()
        ?>
      </div>

<?php endif; ?>

<div class='sitelogin_settings_form'>
    <div class='settings' style="margin-top:15px;">
        <?php echo $this->form->render($this); ?>
    </div>
</div>
<script type="text/javascript">
    window.addEvent('domready', function () {
        showCustomOption();
    });

    function showCustomOption() {
        if ($("sitelogin_redirectlink-wrapper")) {
            if ($("sitelogin_redirectlink-4").checked) {
                if ($("sitelogin_customurl-wrapper")) {
                    $("sitelogin_customurl-wrapper").show();
                }
            } else {
                if ($("sitelogin_customurl-wrapper")) {
                    $("sitelogin_customurl-wrapper").hide();
                } 
            }
        }
    }

</script>