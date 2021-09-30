<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: changes.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
  <?php echo $this->translate("Advanced Photo Albums Plugin") ?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs clr'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>
<?php if($this->showTip): ?>
<div class="tip">
  <span>
    <?php $link = $this->htmlLink(
            array('route' => 'default', 'module' => 'seaocore', 'controller' => 'admin-settings', 'action' => 'lightbox'), $this->translate('Photos Lightbox Viewer'), array('target' => '_blank'));?>
    <?php echo $this->translate("We have moved these 'Advanced Lightbox Display Settings' in 'SocialEngineAddOns Core Plugin'. You can change the desired settings by visiting '$link' section in 'SocialEngineAddOns Core Plugin'.");?>
  </span>
</div>
<?php endif;?>
