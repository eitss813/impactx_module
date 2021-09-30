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
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css'); ?>
<div id="image_view" class="sitevideo_cat_gd_wrap sitevideo_cat_grid_view sitevideo_cat_sub_grid_view clr">
    <ul class="sitevideo_cat_gd">
        <?php foreach ($this->categoryParams as $category): ?>  
            <li class="seao_cat_gd_col fleft o_hidden g_b <?php if (!empty($category['subCategories'])): ?>seao_cat_gd_col_links_wrap<?php endif ?>" style="height: <?php echo $this->columnHeight; ?>px; width: <?php echo $this->columnWidth; ?>px;">
                <div class="seao_cat_gd_cnt">

                    <?php
                    $url = $this->url(array('category_id' => $category['category_id'], 'categoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $category['category_id'])->getCategorySlug()), "sitevideo_general_category");
                    if (!empty($this->category_id) && !empty($category['category_id'])) {
                        $url = $this->url(array('category_id' => $this->category_id, 'categoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $this->category_id)->getCategorySlug(), 'subcategory_id' => $category['category_id'], 'subcategoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $category['category_id'])->getCategorySlug()), "sitevideo_general_subcategory");
                    }
                    ?>            
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
                <div class="seao_cat_gd_title sitevideo_category_icon_text sitevideo_category_subcat_title">
                    <?php echo $this->htmlLink($url, $this->translate($category['title'])); ?>
                </div>
                <?php if (!empty($category['subCategories'])): ?>
                    <div class='seao_cat_gd_col_links'>
                        <?php
                        foreach ($category['subCategories'] as $subCategory):
                            if (!empty($this->category_id) && !empty($category['category_id'])) {

                                $getUrl = $this->url(array('category_id' => $this->category_id, 'categoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $this->category_id)->getCategorySlug(), 'subcategory_id' => $category['category_id'], 'subcategoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $category['category_id'])->getCategorySlug(), 'subsubcategory_id' => $subCategory['sub_category_id'], 'subsubcategoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $subCategory['sub_category_id'])->getCategorySlug()), "sitevideo_general_subsubcategory");
                            } else {
                                $getUrl = $this->url(array('category_id' => $category['category_id'], 'categoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $category['category_id'])->getCategorySlug(), 'subcategory_id' => $subCategory['sub_category_id'], 'subcategoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $subCategory['sub_category_id'])->getCategorySlug()), "sitevideo_general_subcategory");
                            }
                            echo '<p>' . $this->htmlLink($getUrl, $subCategory['title']);
                            if (!empty($this->count)):
                                echo " " . $this->translate("(%s)", $subCategory['count']);
                            endif;
                            echo '</p>';
                        endforeach;
                        if (!empty($this->category_id)):
                            echo '<p class="view-all">' . $this->htmlLink($url, $this->translate("View More &raquo;")) . '</p>';
                        else :
                            echo '<p class="view-all">' . $this->htmlLink($this->url(array('category_id' => $category['category_id'], 'categoryname' => Engine_Api::_()->getItem('sitevideo_channel_category', $category['category_id'])->getCategorySlug()), "sitevideo_general_category"), $this->translate("View More &raquo;")) . '</p>';
                        endif;
                        ?>
                    </div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<div class="clear">
</div>