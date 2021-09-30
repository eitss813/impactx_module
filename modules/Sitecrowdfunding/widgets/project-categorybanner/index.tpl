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
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<?php $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>
<?php
$category = $this->category;
$backgroupImage = $this->backgroupImage;
$defaultBackground = $baseUrl . 'application/modules/Sitecrowdfunding/externals/images/default-background.jpg';
$bcImage = ($backgroupImage) ? $backgroupImage : $defaultBackground;
?>
<div class='categories_manage sitecrowdfunding_categories_banner_background' id='categories_manage' style="background-image: url('<?php echo $bcImage; ?>');" >
    <div class="sitecrowdfunding_categories_banner_bottom" style="height:<?php echo $this->categoryImageHeight; ?>px;">
        <?php if ($this->category['banner_id']): ?>
            <?php $bannerUrl = $this->storage->get($this->category['banner_id'], '')->getPhotoUrl(); ?>
        <?php else: ?>
            <?php $bannerUrl = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/category_images/banner_images/nobanner_category.jpg"; ?>
        <?php endif; ?>
        <div class="sitecrowdfunding_categories_banner_image">
            <a <?php if ($this->category['banner_url']) : ?> href="<?php echo $this->category['banner_url'] ?>" <?php endif; ?> title="<?php echo $this->translate($this->category['banner_title']) ?>" <?php if ($this->category['banner_url_window'] == 1): ?> target ="_blank" <?php endif; ?>><img alt="" src='<?php echo $bannerUrl; ?>' /></a>
        </div>	
        <div class="sitecrowdfunding_categories_banner_text">
            <div class="sitecrowdfunding_categories_banner_text_container">
                <div class="sitecrowdfunding_categories_banner_top">   
                    <h4><?php echo $this->translate($category->banner_title); ?></h4>
                    <p><?php echo $this->translate($category->banner_description); ?></p>
                </div>
                <div class="sitecrowdfunding_categories_banner_title"> 
                    <?php if ($category->file_id) : ?>
                        <?php echo $this->itemPhoto($this->storage->get($category->file_id, ''), null, null, array('style' => 'width:30px; height:30px;')); ?>
                    <?php elseif ($category->font_icon): ?>
                        <i class="fa <?php echo $category->font_icon; ?>"></i>
                    <?php else: ?>
                        <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/category_images/icons/noicon_category.png" ?> 
                        <img src="<?php echo $src ?>" style="width:30px;height:30px;" alt="">
                    <?php endif; ?>
                    <?php echo $this->htmlLink($category->getHref(), $this->string()->truncate($this->string()->stripTags($this->translate($category->getTitle())), $this->titleTruncation), array('title' => $category->getTitle())); ?>
                </div>
                <div class="sitecrowdfunding_categories_banner_tagline" title="<?php echo $category->featured_tagline ?>">
                    <?php echo $this->string()->truncate($this->string()->stripTags($this->translate($category->featured_tagline)), $this->taglineTruncation); ?>
                </div>
                <div class="sitecrowdfunding_categories_banner_explorebtn">
                    <?php if ($this->showExporeButton) : ?>
                        <?php $url2 = $this->url(Array('module' => 'sitecrowdfunding', 'controller' => 'project', 'action' => 'browse', 'category_id' => $category->category_id), 'default');?>
                        <?php echo $this->htmlLink($url2, $this->translate('Explore Projects'), array('class' => 'common_btn')); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($this->fullWidth) : ?>
    <script>
        en4.core.runonce.add(function () {
          var globalContentElement = en4.seaocore.getDomElements('content');
            if ($$('.layout_main')) {
                var globalContentWidth = $(globalContentElement).getWidth();
                $$('.layout_main').setStyles({
                    'width': globalContentWidth,
                    'margin': '0 auto'
                });
            }
            /*$(globalContentElement).setStyles({
                'width': '100%',
                'margin-top': '-16px'
            });*/
            $(globalContentElement).addClass('global_content_fullwidth');
        });
    </script>
<?php endif; ?> 
