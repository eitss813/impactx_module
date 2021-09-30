<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: header.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="sitevideo_dashboard_header">
    <span class="fright">
        <?php echo $this->htmlLink($this->channel->getHref(), $this->translate('View this Channel'), array("class" => 'sitevideo_buttonlink')) ?> 
    </span>
    <span class="fright mright5">
    	<a href="<?php echo $this->url(array('action' => 'manage'), 'sitevideo_channel_general', true); ?>"  class="sitevideo_buttonlink"><?php echo $this->translate('My Channels'); ?></a>
    </span>
    <span class="fright mright5">
      <a href="<?php echo $this->url(array('action' => 'manage'), 'sitevideo_video_general', true); ?>"  class="sitevideo_buttonlink">
          <?php echo $this->translate('My Videos'); ?>
      </a>
    </span>
    <span class="siteevent_dashboard_header_title o_hidden">
        <?php echo $this->translate('Dashboard'); ?>: 
        <?php echo $this->htmlLink($this->channel->getHref(), $this->channel->getTitle()) ?>
    </span>
</div>