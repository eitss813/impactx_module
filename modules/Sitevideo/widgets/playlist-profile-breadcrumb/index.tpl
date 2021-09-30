<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="sitevideo_channel_breadcrumb">
    <a href="<?php echo $this->url(array('action' => 'browse'), "sitevideo_playlist_general"); ?>"><?php echo $this->translate("Browse Playlists"); ?></a>
    <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
    <?php echo $this->playlist->getTitle(); ?>
</div>
