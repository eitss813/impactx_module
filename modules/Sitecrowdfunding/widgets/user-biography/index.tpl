<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js');
?>
<?php
$bio = $this->ownerBio;
$options = $this->options;
$user = $this->user;
?>
<div class="sitecrowdfunding_user_profile_information">
    <!--<div class="sitecrowdfunding_user_profile_photo"> 
        <div class="sitecrowdfunding_user_profile_title">
            <?php echo $this->htmlLink($user->getHref(), $this->translate($user->getTitle())); ?>
        </div>
        
    </div>-->
    <div class="sitecrowdfunding_user_profile_info"> 
        <?php if (is_array($bio)) : ?>

            <?php if (in_array('biography', $options) && $bio['biography']) : ?>
                <div class="sitecrowdfunding_user_profile_bio mbot10">
                    <h3><?php echo $this->translate('Biography:') ?></h3>
                    <?php echo $bio['biography'] ?> 
                </div>
            <?php endif; ?> 
            <?php if ($this->show_phone && $bio['phone']) : ?>
                <div class="sitecrowdfunding_user_profile_phone mbot10">
                    <?php echo $this->translate('Phone:') ?>
                    <?php echo $bio['phone'] ?>
                </div>

            <?php endif; ?>
            <?php if (in_array('review', $options) && $bio['review']) : ?>
                <div class="sitecrowdfunding_user_profile_review mbot10">
                    <?php echo $this->translate(array('%s Review Count:', '%s Review Counts:', $bio['review_count']), $bio['review_count']); ?>
                </div>
            <?php endif; ?> 
        <?php endif; ?> 
        <div class="sitecrowdfunding_user_profile_sociallinks mtop10">
            <?php if ($this->show_social_media): ?>
                <?php if (in_array('facebook', $options) && $bio['facebook_profile_url']) : ?>
                    <div class="seao_icon_facebook_square mtop10">
                        <?php echo $this->htmlLink($bio['facebook_profile_url'], $bio['facebook_profile_url'], array('target' => '_blank')) ?>
                    </div>
                <?php endif; ?>

                <?php if (in_array('twitter', $options) && $bio['twitter_profile_url']) : ?>
                    <div class="seao_icon_twitter_square mtop10">
                        <?php echo $this->htmlLink($bio['twitter_profile_url'], $bio['twitter_profile_url'], array('target' => '_blank')) ?>
                    </div>
                <?php endif; ?>

                <?php if (in_array('instagram', $options) && $bio['instagram_profile_url']) : ?>
                    <div class="seao_icon_instagram_square mtop10">
                        <?php echo $this->htmlLink($bio['instagram_profile_url'], $bio['instagram_profile_url'], array('target' => '_blank')) ?>
                    </div>
                <?php endif; ?>

                <?php if (in_array('vimeo', $options) && $bio['vimeo_profile_url']) : ?>
                    <div class="seao_icon_vimeo_square mtop10">
                        <?php echo $this->htmlLink($bio['vimeo_profile_url'], $bio['vimeo_profile_url'], array('target' => '_blank')) ?>
                    </div>
                <?php endif; ?>

                <?php if (in_array('youtube', $options) && $bio['youtube_profile_url']) : ?>
                    <div class="seao_icon_youtube_square mtop10">
                        <?php echo $this->htmlLink($bio['youtube_profile_url'], $bio['youtube_profile_url'], array('target' => '_blank')) ?>
                    </div>
                <?php endif; ?>

                <?php if (in_array('website', $options) && $bio['website_url']) : ?>
                    <div class="seao_icon_sharelink_square mtop10">
                        <?php echo $this->htmlLink($bio['website_url'], $bio['website_url'], array('target' => '_blank')) ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
