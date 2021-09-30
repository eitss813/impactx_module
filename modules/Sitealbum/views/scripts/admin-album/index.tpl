<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
<?php echo $this->translate("Advanced Photo Albums Plugin") ?>
</h2>
  <?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
  <?php
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
  </div>
  <?php endif; ?>
<h3>
<?php echo $this->translate("Ajax based Tabbed widget for Albums") ?>
</h3>
  <?php if (count($this->subNavigation)): ?>
  <div class='tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
  ?>
  </div>
  <?php endif; ?>
<div class="tip">
  <span>
<?php echo $this->translate('We have moved these "Tabbed Album Widget" to "Layout Editor". You can change the desired settings of the respective widgets from "Layout Editor" by clicking on the "edit" link.'); ?></span>
</div>