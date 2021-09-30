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
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css'); ?>
<div id="image_view" class="sitevideo_cat_gd_wrap sitevideo_cat_grid_view clr">
    <ul class="sitevideo_cat_gd">
        <?php foreach ($this->categoryParams as $category): ?>  
            <li class="seao_cat_gd_col fleft o_hidden g_b" style="height: <?php echo $this->columnHeight; ?>px; width: <?php echo $this->columnWidth; ?>px;">
                <div class="seao_cat_gd_cnt">
                    <?php $url = $this->url(array('category_id' => $category['category_id'], 'categoryname' => Engine_Api::_()->getItem('sitevideo_video_category', $category['category_id'])->getCategorySlug()), "sitevideo_video_general_category"); ?>            
                    <?php if (!empty($category['video_id'])): ?>
                        <?php
                        $temStorage = $this->storage->get($category['video_id'], '');
                        if (!empty($temStorage)):
                            ?>
                             <?php echo $this->itemBackgroundPhoto($temStorage, null, $temStorage->getTitle(), array(
                                'class' => 'dblock seao_cat_gd_img',
                                'tag' => 'a',
                                'href' => $url,
                            ));
                            ?>
                            <?php
                        endif;
                    else:
                        ?>
                        <a href="<?php echo $url; ?>" class="dblock seao_cat_gd_img" style="background-image: url('<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitevideo/externals/images/novideo_channel_thumb_normal.png');"></a> 
                    <?php endif; ?>
                </div>  
                <div class="seao_cat_gd_title sitevideo_category_icon_text">
                    <div class="sitevideo_category_icon_text_inner">
                        <?php if (!empty($category['file_id'])): ?>
                            <?php
                            $temStorage = $this->storage->get($category['file_id'], '');
                            if (!empty($temStorage) && $this->showIcon):
                                ?>
                                <img src="<?php echo $temStorage->getPhotoUrl(); ?>" style="width:30px;height:30px;"/>

                                <?php
                            endif;
                        endif;
                        ?>
                        <?php echo $this->htmlLink($url, $this->translate($category['title'])); ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<div class="clear">
</div>