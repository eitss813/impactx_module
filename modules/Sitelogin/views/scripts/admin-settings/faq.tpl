<?php

/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Sitelogin
* @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    faq.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
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
<?php include_once APPLICATION_PATH . '/application/modules/Sitelogin/views/scripts/admin-settings/faq_help.tpl'; ?>
