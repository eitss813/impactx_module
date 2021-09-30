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
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css');
?>

<script type="text/javascript">

  function show_subcat(cat_id)
  {
    if (document.getElementById('subcat_' + cat_id)) {
      if (document.getElementById('subcat_' + cat_id).style.display == 'block') {
        document.getElementById('subcat_' + cat_id).style.display = 'none';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitealbum/externals/images/bullet-right.png';
      }
      else if (document.getElementById('subcat_' + cat_id).style.display == '') {
        document.getElementById('subcat_' + cat_id).style.display = 'none';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitealbum/externals/images/bullet-right.png';
      }
      else {
        document.getElementById('subcat_' + cat_id).style.display = 'block';
        document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitealbum/externals/images/bullet-bottom.png';
      }
    }
  }

</script>

<?php if (count($this->categories)): ?>
  <ul class="sitealbum_browse_side_category">

    <?php foreach ($this->categories as $category): ?>
      <?php $total_subcat = count($category['sub_categories']); ?>

      <?php if ($total_subcat > 0): ?>
        <li>
          <a href="javascript:void(0);" onclick="show_subcat('<?php echo $category['category_id'] ?>');" id='button_<?php echo $category['category_id'] ?>' class="right_bottom_arrow">
            <?php if ($this->category_id != $category['category_id']): ?>
              <img alt=""  src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitealbum/externals/images/bullet-right.png' border='0' id='img_<?php echo $category['category_id'] ?>'/>
            <?php elseif ($this->subcategory_id != 0 && $this->category_id == $category['category_id']): ?>
              <img alt="" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitealbum/externals/images/bullet-bottom.png' border='0' id='img_<?php echo $category['category_id'] ?>'/>
            <?php elseif ($this->category_id != 0 && $this->category_id == $category['category_id']): ?>
              <img alt="" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitealbum/externals/images/bullet-right.png' border='0' id='img_<?php echo $category['category_id'] ?>'/>
            <?php endif; ?>
          </a>
          <?php $category_name = $this->translate($category['category_name']); ?>
          <?php $truncate_category = Engine_Api::_()->seaocore()->seaocoreTruncateText($category_name, $this->catTruncLimit); ?>
          <a <?php if ($this->category_id == $category['category_id']): ?> class="bold"<?php endif; ?> href='<?php echo $this->url(array('category_id' => $category['category_id'], 'categoryname' => Engine_Api::_()->getItem('album_category', $category['category_id'])->getCategorySlug()), "sitealbum_general_category"); ?>'>
            <?php     $this->url(array('action' => 'browse', 'category_id' => $category['category_id']), 'sitealbum_general', true) ?>
            <span class="cat_icon">
              <?php if ($category['file_id']): ?>
               <?php echo $this->itemPhoto($this->storage->get($category['file_id'],''), null, null, array()); ?>
              <?php endif; ?>
             </span>
            <span class="cat_name" title="<?php echo $category_name; ?>"><?php echo $truncate_category; ?></span>
          </a>

          <ul id="subcat_<?php echo $category['category_id'] ?>" <?php if ($this->category_id != $category['category_id']): ?>style="display:none;"<?php endif; ?> >
            <?php foreach ($category['sub_categories'] as $subcategory) : ?>
              <li>
                <?php $subcategory_name = $this->translate($subcategory['sub_cat_name']); ?>
                <?php $truncate_subcategory = Engine_Api::_()->seaocore()->seaocoreTruncateText($subcategory_name, $this->subCatTruncLimit); ?>
                <a <?php if ($this->subcategory_id == $subcategory['sub_cat_id']): ?>class="bold"<?php endif; ?>  href='<?php echo $this->url(array('category_id' => $category['category_id'], 'categoryname' => Engine_Api::_()->getItem('album_category', $category['category_id'])->getCategorySlug(), 'subcategory_id' => $subcategory['sub_cat_id'], 'subcategoryname' => Engine_Api::_()->getItem('album_category', $subcategory['sub_cat_id'])->getCategorySlug()), "sitealbum_general_subcategory"); ?>'>
                  <span class="cat_icon">
                    <?php if ($subcategory['file_id']): ?>
                      <?php echo $this->itemPhoto($this->storage->get($subcategory['file_id'],''), null, null, array()); ?>
                    <?php endif; ?>
                  </span>
                  <span class="cat_name" title="<?php echo $subcategory_name ?>"><?php echo $truncate_subcategory; ?></span>
                </a>
              </li>
              <?php endforeach; ?>
          </ul>
        </li>
      <?php else: ?>
        <li>
          <?php $category_name = $this->translate($category['category_name']); ?>
          <?php $truncate_category = Engine_Api::_()->seaocore()->seaocoreTruncateText($category_name, $this->catTruncLimit); ?>

          <a <?php if ($this->category_id == $category['category_id']): ?> class="bold"<?php endif; ?>  href='<?php echo $this->url(array('category_id' => $category['category_id'], 'categoryname' => Engine_Api::_()->getItem('album_category', $category['category_id'])->getCategorySlug()), "sitealbum_general_category"); ?>'>
            <span class="cat_icon">
              <?php if ($category['file_id']): ?>
               <?php echo $this->itemPhoto($this->storage->get($category['file_id'],''), null, null, array()); ?>
              <?php endif; ?>
            </span>
            <span class="cat_name" title="<?php echo $category_name ?>"><?php echo $truncate_category ?></span>
          </a>
        </li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>