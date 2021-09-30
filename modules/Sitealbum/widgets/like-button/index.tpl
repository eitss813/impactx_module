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
<?php 
	$baseUrl = $this->layout()->staticBaseUrl;
	$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css');

  $this->headScript()->appendFile($baseUrl . 'application/modules/Sitealbum/externals/scripts/core.js');
?>
<div class="seaocore_like_button" style="padding:2px;" >
  <a  id="<?php echo $this->subject()->getType() ?>unlike_link" href="javascript:void(0);" onclick="en4.sitealbum.likes.unlike('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>');"  <?php if (!$this->subject()->likes()->isLike($this->viewer())): ?> style="display: none;" <?php endif; ?> >	<i class="seaocore_like_thumbdown_icon"></i>
      <span><?php echo $this->translate('Unlike') ?></span></a>
    <a id="<?php echo $this->subject()->getType() ?>like_link" href="javascript:void(0);" onclick="en4.sitealbum.likes.like('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>');"  <?php if ($this->subject()->likes()->isLike($this->viewer())): ?> style="display: none;" <?php endif; ?>  >	<i class="seaocore_like_thumbup_icon"></i>
       <span><?php echo $this->translate('Like') ?></span></a>
</div>
