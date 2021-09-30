<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>

<?php 

if($this->columnPerRow == 1){
$width = '100%';
} else if($this->columnPerRow == 2) {
$width = '49%';
} else if($this->columnPerRow == 3) {
$width = '33%';
} else if($this->columnPerRow == 4) {
$width = '24%';
} else {
$width = '19%';
}



?>

<ul class="sitealbum_sponsored_categories">
  <?php
  $count = 0;
  foreach ($this->categories as $category): $count++;
    ?>
    <li style="width:<?php echo $width;?>">
      <?php
      $htmlImage = '';
      if ($category->file_id && $this->showIcon):
        ?>
        <?php $src = $this->storage->get($category->file_id, '')->getPhotoUrl(); ?>
        <?php $htmlImage = '<span class="sitealbum_cat_icon">' . $this->itemPhoto($this->storage->get($category->file_id, ''), null , null, array()) . '</span>'; ?>
      <?php endif; ?>
      <?php if ($category->cat_dependency == 0): ?>
        <?php echo $this->htmlLink($this->url(array('category_id' => $category->category_id, 'categoryname' => Engine_Api::_()->getItem('album_category', $category->category_id)->getCategorySlug()), "sitealbum_general_category"), $htmlImage . $this->translate($category->category_name)) ?>
      <?php elseif ($category->cat_dependency != 0): ?>
        <?php $getCatDependancy = $this->tableCategory->getCategory($category->cat_dependency); ?>
        <?php echo $this->htmlLink($this->url(array('category_id' => $getCatDependancy->category_id, 'categoryname' => Engine_Api::_()->getItem('album_category', $getCatDependancy->category_id)->getCategorySlug(), 'subcategory_id' => $category->category_id, 'subcategoryname' => Engine_Api::_()->getItem('album_category', $category->category_id)->getCategorySlug()), "sitealbum_general_subcategory"), $htmlImage . $this->translate($category->category_name)); ?>
      <?php endif; ?>
      <?php if ($count < $this->totalCategories): ?>
      <?php endif; ?>
    </li>  
  <?php endforeach; ?>
</ul>