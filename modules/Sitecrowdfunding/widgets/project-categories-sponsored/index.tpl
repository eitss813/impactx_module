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
<ul class="sitecrowdfunding_sponsored_categories">
    <?php 
    $count = 0;
    foreach ($this->categories as $category): $count++;
        ?>
        <li>

    <?php if ($category->file_id): ?>
        <?php $url = Engine_Api::_()->storage()->get($category->file_id)->getPhotoUrl(); ?>
         <img src="<?php echo $url ?>" style="width: 16px; height: 16px;" alt="">
    <?php elseif($category->font_icon): ?>
      <i class="fa <?php echo $category->font_icon; ?>"></i>
    <?php else: ?>
         <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/category_images/icons/noicon_category.png" ?> 
         <img src="<?php echo $src ?>" style="width: 16px; height: 16px;" alt="">
    <?php endif; ?> 

            
            <?php $htmlImage = ''; ?>
            <?php if($this->showIcon): ?>
                <?php if ($category->file_id): ?> 
                    <?php $src = $this->storage->get($category->file_id, '')->getPhotoUrl(); ?>
                    <?php $htmlImage = '<span class="sitecrowdfunding_cat_icon">' . $this->htmlImage($src) . '</span>';?>
                <?php elseif($category->font_icon): ?>
                    <?php $icon = '<i class="fa <?php echo $category->font_icon; ?>"></i>'; ?>
                    <?php $htmlImage = '<span class="sitecrowdfunding_cat_icon">' . $icon . '</span>';?>
                <?php else: ?>
                    <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/category_images/icons/noicon_category.png" ?> 
                    <?php $htmlImage = '<span class="sitecrowdfunding_cat_icon">' . $this->htmlImage($src) . '</span>';?>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($category->cat_dependency == 0 && $category->subcat_dependency == 0): ?>
                <?php echo $this->htmlLink($this->url(array('category_id' => $category->category_id, 'categoryname' => Engine_Api::_()->getItem('sitecrowdfunding_category', $category->category_id)->getCategorySlug()), "sitecrowdfunding_general_category"), $htmlImage . $this->translate($category->category_name)); ?>
            <?php elseif ($category->cat_dependency != 0 && $category->subcat_dependency == 0): ?>
                <?php $getCatDependancy = $this->tableCategory->getCategory($category->cat_dependency); ?>
                <?php echo $this->htmlLink($this->url(array('category_id' => $getCatDependancy->category_id, 'categoryname' => Engine_Api::_()->getItem('sitecrowdfunding_category', $getCatDependancy->category_id)->getCategorySlug(), 'subcategory_id' => $category->category_id, 'subcategoryname' => Engine_Api::_()->getItem('sitecrowdfunding_category', $category->category_id)->getCategorySlug()), "sitecrowdfunding_general_category"), $htmlImage . $this->translate($category->category_name)) ?>
            <?php else: ?>
                <?php $getSubCatDependancy = $this->tableCategory->getCategory($category->cat_dependency); ?>
                <?php $getCatDependancy = $this->tableCategory->getCategory($getSubCatDependancy->cat_dependency); ?>
                <?php echo $this->htmlLink($this->url(array('category_id' => $getCatDependancy->category_id, 'categoryname' => Engine_Api::_()->getItem('sitecrowdfunding_category', $getCatDependancy->category_id)->getCategorySlug(), 'subcategory_id' => $getSubCatDependancy->category_id, 'subcategoryname' => Engine_Api::_()->getItem('sitecrowdfunding_category', $getSubCatDependancy->category_id)->getCategorySlug(), 'subsubcategory_id' => $category->category_id, 'subsubcategoryname' => Engine_Api::_()->getItem('sitecrowdfunding_category', $category->category_id)->getCategorySlug()), "sitecrowdfunding_general_subcategory"), $htmlImage . $this->translate($category->category_name)) ?>
            <?php endif; ?> 
            <?php if ($count < $this->totalCategories): ?>
                <span>|</span>
            <?php endif; ?>
        </li>  
    <?php endforeach; ?>
</ul>