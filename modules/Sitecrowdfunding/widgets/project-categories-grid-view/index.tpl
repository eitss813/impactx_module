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
<?php $tableCategory = $this->tableCategory; ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>
<div id="image_view" class="sitecrowdfunding_cat_gd_wrap sitecrowdfunding_cat_grid_view sitecrowdfunding_cat_sub_grid_view clr">
    <ul class="sitecrowdfunding_cat_gd">
        <?php foreach ($this->categoryParams as $category): ?>  
            <li class="seao_cat_gd_col fleft g_b <?php if (!empty($category['subCategories'])): ?>seao_cat_gd_col_links_wrap<?php endif ?>" style="height: <?php echo $this->columnHeight; ?>px; width: <?php echo $this->columnWidth + 11; ?>px;">
                <div class="box" style="height: <?php echo $this->columnHeight; ?>px; width: <?php echo $this->columnWidth; ?>px;">
                    <div class="seao_cat_gd_cnt">
                        <?php
                        $url = $this->url(array('category_id' => $category['category_id'], 'categoryname' => $category['category_slug']), "sitecrowdfunding_general_category");
                        if (!empty($this->category_id) && !empty($category['category_id'])) {
                            $url = $this->url(array('category_id' => $this->category_id, 'categoryname' => $this->category_slug, 'subcategory_id' => $category['category_id'], 'subcategoryname' => $tableCategory->getCategory($category['category_id'])->getCategorySlug()), "sitecrowdfunding_general_subcategory");
                        }
                        ?>            
                        <?php if (!empty($category['photo_id'])): ?>
                            <?php
                            $temStorage = $this->storage->get($category['photo_id'], '');
                            if (!empty($temStorage)):
                                ?>
                                <?php echo $this->itemBackgroundPhoto($temStorage, null, $temStorage->getTitle(), array('class' => 'dblock seao_cat_gd_img', 'tag' => 'a', 'href' => $url)); ?> 
                                <?php
                            endif;
                        else:
                            ?>
                            <a href="<?php echo $url; ?>" class="dblock seao_cat_gd_img" style="background-image: url('<?php echo $this->layout()->staticBaseUrl ?>application/modules/sitecrowdfunding/externals/images/categories_default_image.jpg');"></a> 
                        <?php endif; ?>
                    </div> 
                    <div class="sitecrowdfunding_category_icon_text">
                    <div class="seao_cat_gd_title sitecrowdfunding_category_subcat_title">
                    <?php if($this->subcategoryExist): ?>
                        <?php $url2 = $this->url(Array('module' => 'sitecrowdfunding', 'controller' => 'project', 'action' => 'browse', 'subcategory_id' => $category['category_id']), 'default');?>
                        <?php echo $this->htmlLink($url2, $this->translate($category['title'])); ?>
                    <?php else: ?>
                        <?php echo $this->htmlLink($url, $this->translate($category['title'])); ?>
                    <?php endif; ?>
                    </div>
                    <?php if (!empty($category['subCategories'])): ?>
                        <div class='seao_cat_gd_col_links'>
                            <?php
                            foreach ($category['subCategories'] as $subCategory):
                                if (!empty($this->category_id) && !empty($category['category_id'])) {
                                    $getUrl = $this->url(array('category_id' => $this->category_id, 'categoryname' => $this->category_slug, 'subcategory_id' => $category['category_id'], 'subcategoryname' => $tableCategory->getCategory($category['category_id'])->getCategorySlug(), 'subsubcategory_id' => $subCategory['sub_category_id'], 'subsubcategoryname' => $tableCategory->getCategory($subCategory['sub_category_id'])->getCategorySlug()), "sitecrowdfunding_general_subsubcategory");
                                } else {
                                    $getUrl = $this->url(array('category_id' => $category['category_id'], 'categoryname' => $category['category_slug'], 'subcategory_id' => $subCategory['sub_category_id'], 'subcategoryname' => $subCategory['category_slug']), "sitecrowdfunding_general_subcategory");
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
                                echo '<p class="view-all">' . $this->htmlLink($this->url(array('category_id' => $category['category_id'], 'categoryname' => $category['category_slug']), "sitecrowdfunding_general_category"), $this->translate("View More &raquo;")) . '</p>';
                            endif;
                            ?>
                        </div>
                    <?php endif; ?>
                  </div> 
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<div class="clear">
</div>