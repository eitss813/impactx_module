<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate('Directory / Page - Videos Extension'); ?></h2>
<div class='tabs'>
  <?php
  echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->render();
  ?>
</div>
<?php if( count($this->subNavigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
    ?>
  </div>
<?php endif; ?>
<br />

<div class="tip">
	<span>
		<?php echo $this->translate('We have moved these "Widget Settings" to "Layout Editor". You can change the desired settings of the respective widgets from "Layout Editor" by clicking on the "edit" link.');?>
	</span>
</div>