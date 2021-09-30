<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: add.tpl 6590 2016-07-07 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<!--ADD NAVIGATION-->
<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?>
<?php if( count($this->subNavigation) ): ?>
  <div class='seaocore_admin_tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
    ?>
  </div>
<?php endif; ?><div class="seaocore_settings_form">
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>


