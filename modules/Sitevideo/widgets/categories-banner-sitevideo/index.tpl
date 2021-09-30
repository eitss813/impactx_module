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
<?php if ($this->category['banner_id']): ?>
    <div class="sitevideo_browse_banner">
        <a <?php if ($this->category['banner_url']) : ?> href="<?php echo $this->category['banner_url'] ?>" <?php endif; ?> title="<?php echo $this->category['banner_title'] ?>" <?php if ($this->category['banner_url_window'] == 1): ?> target ="_blank" <?php endif; ?>>
        	<?php echo $this->itemPhoto($this->storage->get($this->category['banner_id'], ''), null, null, array()); ?>
        </a>
    </div>	
<?php endif; ?>