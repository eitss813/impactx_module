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
$baseUrl = $this->layout()->staticBaseUrl;
$backgroupImage = $this->backgroupImage;
$defaultBackground = $this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/images/video1.jpg';
$bcImage = ($backgroupImage) ? $backgroupImage : $defaultBackground;
?>
<?php
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css');
?>

<div style="background-image: url('<?php echo $bcImage; ?>');height:<?php echo $this->backgroundImageHeight; ?>px;" class="sitevideo_categories_banner_background"> 
    <div class="sitevideo_categories_banner_container">
        <div class="sitevideo_categories_banner_top">   
            <h3><?php echo $this->category['banner_title']; ?></h3>
            <p><?php echo $this->category['banner_description']; ?></p>
        </div>
        <div class="sitevideo_categories_banner_bottom" style="height:<?php echo $this->categoryImageHeight; ?>px;">
            <div class="sitevideo_categories_banner_image">
                <?php if ($this->category['banner_id']) : ?>
                    <a <?php if ($this->category['banner_url']) : ?> href="<?php echo $this->category['banner_url'] ?>" <?php endif; ?> title="<?php echo $this->category['banner_title'] ?>" <?php if ($this->category['banner_url_window'] == 1): ?> target ="_blank" <?php endif; ?>>
                        <?php echo $this->itemPhoto($this->storage->get($this->category['banner_id'],''), null, null, array()); ?>
                    </a>
                <?php endif; ?>
            </div>
            <div class="sitevideo_categories_banner_text">
                <div class="sitevideo_categories_banner_title">
                    <?php if ($this->category->file_id) : ?>
                        <img alt="" style="width:30px;height:30px;"src='<?php echo $this->storage->get($this->category->file_id, '')->getPhotoUrl(); ?>' />
                    <?php endif; ?>

                    <?php echo  $this->string()->truncate($this->string()->stripTags($this->category->getTitle()), $this->titleTruncation); ?>
                </div>
                <div class="sitevideo_categories_banner_tagline">
                    <?php echo $this->string()->truncate($this->string()->stripTags($this->category['featured_tagline']), $this->taglineTruncation); ?>
                </div>
                <div class="sitevideo_categories_banner_explorebtn">
                    <?php if ($this->showExplore) : ?>
                        <?php $url = $this->url(array('action' => 'browse','category_id' => $this->category->getIdentity(), 'categoryname' => $this->category->getCategorySlug()), "sitevideo_general"); ?>
                        <?php echo $this->htmlLink($url, 'Explore Now'); ?>
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
        $(globalContentElement).setStyles({
            'width': '100%',
            'margin-top': '-16px'
        });
    });
</script>
<?php endif;?>
