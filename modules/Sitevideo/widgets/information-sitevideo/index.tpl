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
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css'); ?>
<div id="sitevideo_stats" class="sitevideo_side_widget sitevideo_profile_channel_info">
    <!-- CHANNEL INFO WORK -->
    <?php if (!empty($this->showContent)) : ?>
        <?php
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.category.enabled', 1) && in_array('categoryLink', $this->showContent) && $this->sitevideo->category_id) :
            $categoryName = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo')->getCategoryName($this->sitevideo->category_id);
            ?>
            <div class="seao_listings_stats"><i class="seao_icon_strip seao_icon seao_icon_category" title="<?php echo $this->translate('Category') ?>"></i>
                <div class="o_hidden">
                    <a href="<?php echo $this->url(array('category_id' => $this->sitevideo->category_id, 'categoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $this->sitevideo->category_id)->getCategorySlug()), 'sitevideo_general_category', true) ?>">
                        <span><?php echo $categoryName; ?></span>
                    </a> 
                </div>
            </div>
        <?php endif; ?>
        <?php if (strlen(trim($this->sitevideo->description)) > 0 && in_array('description', $this->showContent)): ?>
            <div class="seao_listings_stats">
                <i class="seao_icon_strip seao_icon seao_icon_video" title="Description"></i>
                <div title="<?php echo $this->translate('%s Description', $this->sitevideo->description) ?>" class="o_hidden"><?php echo $this->translate('%s Description', $this->sitevideo->description) ?></div>
            </div>
        <?php endif; ?>
        <?php if (in_array('totalVideos', $this->showContent)): ?>
            <div class="seao_listings_stats">
                <i class="seao_icon_strip seao_icon seao_icon_video" title="Videos"></i>
                <div title="<?php echo $this->translate(array('%s video', '%s videos', $this->sitevideo->videos_count), $this->locale()->toNumber($this->sitevideo->videos_count)) ?>" class="o_hidden"><?php echo $this->translate(array('%s video', '%s videos', $this->sitevideo->videos_count), $this->locale()->toNumber($this->sitevideo->videos_count)) ?></div>
            </div>
        <?php endif; ?>

        <?php if (in_array('creationDate', $this->showContent)) : ?>
            <div class="seao_listings_stats"><i class="seao_icon_strip seao_icon seao_icon_time" title="<?php echo $this->translate("Creation Date") ?>"></i><div class="o_hidden"><?php echo $this->translate("Created on: %1s", $this->timestamp($this->sitevideo->creation_date)); ?>
                </div></div>
        <?php endif;
        ?>
        <?php if (in_array('updateDate', $this->showContent)) : ?>
            <div class="seao_listings_stats"><i class="seao_icon_strip seao_icon seao_icon_edit" title="<?php echo $this->translate("Updated Date") ?>"></i><div class="o_hidden"><?php
                    echo $this->translate('Updated on %1s', $this->timestamp($this->sitevideo->modified_date)) . '</div></div>';
                endif;
                ?>
                <?php
                $statistics = '';

                if (!empty($this->showContent) && in_array('commentCount', $this->showContent)) {
                    $statistics .= $this->translate(array('%s comment', '%s comments', $this->sitevideo->comment_count), $this->locale()->toNumber($this->sitevideo->comment_count)) . ', ';
                }

                if (!empty($this->showContent) && in_array('likeCount', $this->showContent)) {
                    $statistics .= $this->translate(array('%s like', '%s likes', $this->sitevideo->like_count), $this->locale()->toNumber($this->sitevideo->like_count)) . ', ';
                }
                $statistics = trim($statistics);
                $statistics = rtrim($statistics, ',');
                ?>
                <?php if (!empty($statistics)) : ?>
                    <div class="seao_listings_stats">
                        <div class="seao_listings_stats">
                            <i class="seao_icon_strip seao_icon seao_icon_stats" title="<?php echo $this->translate("Statistics") ?>"></i>
                            <div class="o_hidden">
                                <?php echo $statistics; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.tags.enabled', 1) && !empty($this->showContent) && in_array('tags', $this->showContent) && count($this->sitevideoTags) > 0): $tagCount = 0; ?>
                <div class="seao_listings_stats">
                    <i class="seao_icon_strip seao_icon sitevideo_icon_tag_link" title="<?php echo $this->translate("Tags") ?>"></i>
                    <div class="o_hidden">
                        <?php foreach ($this->sitevideoTags as $tag): ?>
                            <?php if (!empty($tag->getTag()->text)): ?>
                                <?php $tag->getTag()->text = $this->string()->escapeJavascript($tag->getTag()->text) ?>
                                <?php if (empty($tagCount)): ?>
                                    <a href='<?php echo $this->url(array('action' => 'browse'), "sitevideo_general"); ?>?tag=<?php echo urlencode($tag->getTag()->text) ?>&tag_id=<?php echo $tag->getTag()->tag_id ?>'>#<?php echo $tag->getTag()->text ?></a>
                                    <?php
                                    $tagCount++;
                                else:
                                    ?>
                                    <a href='<?php echo $this->url(array('action' => 'browse'), "sitevideo_general"); ?>?tag=<?php echo urlencode($tag->getTag()->text) ?>&tag_id=<?php echo $tag->getTag()->tag_id ?>'>#<?php echo $tag->getTag()->text ?></a>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!empty($this->showContent) && in_array('socialShare', $this->showContent)): ?>  
                <div class="seao_listings_stats">
                    <div class="o_hidden"> 
                        <div class="sitevideo_social_share">
                            <?php echo $this->code; ?>
                        </div>
                    </div>
                </div>    
            <?php endif; ?>  
        </div>