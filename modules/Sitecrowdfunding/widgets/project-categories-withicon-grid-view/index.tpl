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

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>
<div id="image_view" class="sitecrowdfunding_cat_gd_wrap sitecrowdfunding_cat_grid_view sitecrowdfunding_cat_sub_grid_view clr">
    <ul class="sitecrowdfunding_cat_gd">
        <?php foreach ($this->categoryParams as $category): ?>
        <li class="seao_cat_gd_col fleft g_b <?php if (!empty($category['subCategories'])): ?>seao_cat_gd_col_links_wrap<?php endif ?>" style="height: <?php echo $this->columnHeight; ?>px; width: <?php echo $this->columnWidth; ?>px;">
            <div class="box" style="height: <?php echo $this->columnHeight; ?>px; width: <?php echo $this->columnWidth; ?>px;">
                <div class="seao_cat_gd_cnt">
                    <?php $url = "#"; ?>
                    <?php if (!empty($category['photo_id'])): ?>
                    <?php $temStorage = $this->storage->get($category['photo_id'], ''); ?>
                    <?php if (!empty($temStorage)): ?>
                    <?php echo $this->itemBackgroundPhoto($temStorage, null, $temStorage->getTitle(), array('class' => 'dblock seao_cat_gd_img', 'tag' => 'a', 'href' => $url)); ?>
                    <?php endif; ?>
                    <?php else: ?>
                    <a href="javascript:void(0);" onclick="goToSearchPage(<?php echo $category['category_id'] ?>)" class="dblock seao_cat_gd_img" style="background-image: url('<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecrowdfunding/externals/images/categories_default_image.jpg');"></a>
                    <?php endif; ?>
                </div>

                <a href="javascript:void(0);" onclick="goToSearchPage(<?php echo $category['category_id'] ?>)">
                    <div class="seao_cat_gd_title sitecrowdfunding_category_icon_text">
                        <div class="sitecrowdfunding_category_icon_text_inner">
                            <?php if (!empty($category['file_id'])): ?>

                                <?php $temStorage = $this->storage->get($category['file_id'], ''); ?>

                                <?php if (!empty($temStorage)): ?>
                                    <img src='<?php echo $temStorage->getPhotoUrl();?>' style="width:30px;height:30px;"/>
                                    <span class = "title_category_with_icon">
                                        <?php echo $this->translate($category['title']);?>
                                        <br>
                                        <?php echo $category['projects_count'].' Projects';?>
                                    </span>
                                <?php endif; ?>

                            <?php elseif($category['font_icon']): ?>
                                <i class="fa <?php echo $category['font_icon'];?> "></i>
                                <span class = "title_category_with_icon">
                                    <?php echo $this->translate($category['title']);?>
                                    <br>
                                    <?php echo $category['projects_count'].' Projects';?>
                                </span>
                            <?php else: ?>
                                <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/category_images/icons/noicon_category.png" ?>
                                <img src="<?php echo $src;?>" style="width:30px;height:30px;"/>
                                <span class = "title_category_with_icon">
                                    <?php echo $this->translate($category['title']);?>
                                    <br>
                                    <?php echo $category['projects_count'].' Projects';?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
<div class="clear">
</div>



<script type="text/javascript">
    var $j = jQuery.noConflict();
    function goToSearchPage(category_id){
        document.getElementsByName('query')[0].value=null;
        $j('#query').val(null);
        $j('#sdg_goal_id').val(null);
        $j('#sdg_target_id').val(null);
        $j('#search_only_in_project').val(true);
        $j('#category_id').val(category_id);
        document.getElementById("global_search_form").submit();
    }
</script>