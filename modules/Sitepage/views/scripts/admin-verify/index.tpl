<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2 class="fleft"><?php echo $this->translate('Directory / Pages Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/manageExtensions.tpl'; ?>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->subnavigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class="settings">
	<div class="admin_form">
		<?php echo $this->form->render(); ?>
	</div>
</div>


