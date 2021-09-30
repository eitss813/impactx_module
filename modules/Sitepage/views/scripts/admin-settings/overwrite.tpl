<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="global_form_popup">
	<?php
	if (!empty($this->error)):
	  echo $this->translate($this->error); ?>
	  <br /><br />
     <div>
		<button onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate('Close'); ?></button>
	</div>
	<?php elseif ($this->status):
	  echo $this->translate('File has been overwritten successfully.');
	?>
	<br /><br />
	 <div class='tip'><span><b>
			<?php echo $this->translate('NOTE: Whenever you will upgrade Socialengine Core at your site, these changes will be overwritten and you will have to again choose one of the 3 options and configure these settings.'); ?></b></span>
		</div>
  <div>
		<button onclick="parent.window.location.reload(true); javascript:parent.Smoothbox.close();"><?php echo $this->translate('Close'); ?></button>
	</div>
	<?php 
	endif;
	?>
	
</div>
