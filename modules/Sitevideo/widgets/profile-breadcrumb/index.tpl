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
    <a href="<?php echo $this->url(array('action' => 'index'), "sitevideo_general"); ?>"><?php echo $this->translate("Channels Home"); ?></a>
    <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
    <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1)) : ?>
        <?php if ($this->category_name): ?>
            <a href="<?php echo $this->url(array('category_id' => $this->channel->category_id, 'categoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $this->channel->category_id)->getCategorySlug()), 'sitevideo_general_category'); ?>"><?php echo $this->translate($this->category_name); ?></a>
            <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
            <?php if (!empty($this->subcategory_name)): ?>
                <a href="<?php echo $this->url(array('category_id' => $this->channel->category_id, 'categoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $this->channel->category_id)->getCategorySlug(), 'subcategory_id' => $this->channel->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $this->channel->subcategory_id)->getCategorySlug()), "sitevideo_general_subcategory") ?>"><?php echo $this->translate($this->subcategory_name); ?></a>
                <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
                <?php if (!empty($this->subsubcategory_name)): ?>
                    <a href="<?php echo $this->url(array('category_id' => $this->channel->category_id, 'categoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $this->channel->category_id)->getCategorySlug(), 'subcategory_id' => $this->channel->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $this->channel->subcategory_id)->getCategorySlug(), 'subsubcategory_id' => $this->channel->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $this->channel->subsubcategory_id)->getCategorySlug()), "sitevideo_general_subsubcategory") ?>"><?php echo $this->translate($this->subsubcategory_name); ?></a>
                    <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
    <?php echo $this->channel->getTitle(); ?>
</div>
