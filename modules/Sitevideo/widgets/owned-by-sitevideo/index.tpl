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

<?php
$itemTypeValue = $this->sitevideo->parent_type;
$videoOwnerLeader = 0;
if (isset($this->item->listingtype_id) && $itemTypeValue == 'sitereview_listing_' . $this->item->listingtype_id) {
    $item = Engine_Api::_()->getItem('sitereview_listing', $this->sitevideo->parent_id);
    $itemTypeValue = $itemTypeValue . $item->listingtype_id;
    $videoOwnerLeader = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitevideo.video.leader.owner.$itemTypeValue", 1);
} elseif ($itemTypeValue != 'user') {
    $videoOwnerLeader = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitevideo.video.leader.owner.$itemTypeValue", 1);
}

if (!$videoOwnerLeader)
    $itemTypeValue = 'user';
?>
<ul class="sitevideo_side_widget sitevideo_profile_side_video">
    <?php if ($itemTypeValue != 'user'): ?>
        <?php if (!empty($this->item)) : ?>
            <li>
                <?php echo $this->htmlLink($this->item->getHref(), $this->itemPhoto($this->item, 'thumb.icon')); ?>
                <div class="sitevideo_profile_side_video_info">
                    <div class="sitevideo_profile_side_video_title">
                        <?php echo $this->htmlLink($this->item->getHref(), $this->item->getTitle()); ?>
                    </div>
                    <div class="sitevideo_listings_stats seaocore_txt_light f_small">
                        <?php echo $this->translate($this->title); ?>
                        <?php echo $this->translate(array('%s like', '%s likes', $this->item->like_count), $this->locale()->toNumber($this->item->like_count)) ?>
                    </div>
                </div>
            </li>
        <?php endif; ?>
    <?php else: ?>
        <li class="sitevideo_profile_info_host">
            <?php echo $this->htmlLink($this->sitevideo->getOwner()->getHref(), $this->itemPhoto($this->sitevideo->getOwner(), 'thumb.icon')); ?>

            <div class="sitevideo_profile_side_video_info">
                <div class="sitevideo_profile_side_video_title">
                    <?php echo $this->htmlLink($this->sitevideo->getOwner()->getHref(), $this->sitevideo->getOwner()->getTitle()); ?>
                </div>
                <div class="sitevideo_listings_stats">

                    <?php $tab_id = Engine_Api::_()->sitevideo()->getTabId('sitevideo.profile-sitevideo', 'user_profile_index'); ?>
                    <?php $href = $tab_id ? $this->sitevideo->getOwner()->getHref() . '/tab/' . $tab_id : $this->sitevideo->getOwner()->getHref(); ?>
                    <a class="fleft" href="<?php echo $href ?>" title="<?php echo $this->translate("Show all videos"); ?>">
                        <i class="sitevideo_icon_strip sitevideo_icon sitevideo_icon_calendar"></i>
                    </a>
                </div>
            </div>
        </li>
    <?php endif; ?>
</ul>