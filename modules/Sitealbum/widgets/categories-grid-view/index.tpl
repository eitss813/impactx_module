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
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css'); ?>
<div id="image_view" class="sitealbum_cat_gd_wrap clr">
  <ul class="sitealbum_cat_gd">
    <?php foreach ($this->categoryParams as $category): ?>  
      <li class="seao_cat_gd_col fleft o_hidden g_b <?php if (!empty($category['subCategories'])): ?>seao_cat_gd_col_links_wrap<?php endif ?>" style="height: <?php echo $this->columnHeight; ?>px; width: <?php echo $this->columnWidth; ?>px;">
        <div class="seao_cat_gd_cnt">

          <?php
          $url = $this->url(array('category_id' => $category['category_id'], 'categoryname' => Engine_Api::_()->getItem('album_category', $category['category_id'])->getCategorySlug()), "sitealbum_general_category");
          ?>            

          <?php if (!empty($category['photo_id'])): ?>
            <?php
            $temStorage = $this->storage->get($category['photo_id'], '');
            if (!empty($temStorage)): 
              ?>
             <?php echo $this->itemBackgroundPhoto($temStorage,null,$temStorage->getTitle(),array(
                'class' => 'dblock seao_cat_gd_img',
                'tag' => 'a',
                'href' => $url,
                ));
              ?>
              <?php
            endif;
          else:
            ?>
            <a href="<?php echo $url; ?>" class="dblock seao_cat_gd_img" style="background-image: url('<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitealbum/externals/images/nophoto_album_thumb_normal.png');"></a> 
          <?php endif; ?>

        </div>  
					<div class="seao_cat_gd_title">
            <?php echo $this->htmlLink($url, $this->translate($category['title'])); ?>
          </div>
        <?php if (!empty($category['subCategories'])): ?>
          <div class='seao_cat_gd_col_links'>
            <?php
            foreach ($category['subCategories'] as $subCategory):
              $getUrl = $this->url(array('category_id' => $category['category_id'], 'categoryname' => Engine_Api::_()->getItem('album_category', $category['category_id'])->getCategorySlug(), 'subcategory_id' => $subCategory['sub_category_id'], 'subcategoryname' => Engine_Api::_()->getItem('album_category', $subCategory['sub_category_id'])->getCategorySlug()), "sitealbum_general_subcategory");

              echo '<p>' . $this->htmlLink($getUrl, $subCategory['title']);
              if (!empty($this->count)):
                echo " " . $this->translate("(%s)", $subCategory['count']);
              endif;
              echo '</p>';
            endforeach;

            echo '<p class="view-all">' . $this->htmlLink($this->url(array('category_id' => $category['category_id'], 'categoryname' => Engine_Api::_()->getItem('album_category', $category['category_id'])->getCategorySlug()), "sitealbum_general_category"), $this->translate("View More &raquo;")) . '</p>';
            ?>
          </div>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
<div class="clear">
</div>