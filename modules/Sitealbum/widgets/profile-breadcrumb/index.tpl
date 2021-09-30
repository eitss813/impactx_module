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

<div class="sitealbum_album_breadcrumb">
  <a href="<?php echo $this->url(array('action' => 'index'), "sitealbum_general"); ?>"><?php echo $this->translate("Albums Home"); ?></a>
  <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
  <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.category.enabled', 1)) : ?>
    <?php if ($this->category_name): ?>
      <a href="<?php echo $this->url(array('category_id' => $this->album->category_id, 'categoryname' => Engine_Api::_()->getItem('album_category', $this->album->category_id)->getCategorySlug()), 'sitealbum_general_category'); ?>"><?php echo $this->translate($this->category_name); ?></a>
      <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
      <?php if (!empty($this->subcategory_name)): ?>
        <a href="<?php echo $this->url(array('category_id' => $this->album->category_id, 'categoryname' => Engine_Api::_()->getItem('album_category', $this->album->category_id)->getCategorySlug(), 'subcategory_id' => $this->album->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('album_category', $this->album->subcategory_id)->getCategorySlug()), "sitealbum_general_subcategory") ?>"><?php echo $this->translate($this->subcategory_name); ?></a>
        <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
        <?php if (!empty($this->subsubcategory_name)): ?>
          <a href="<?php echo $this->url(array('category_id' => $this->album->category_id, 'categoryname' => Engine_Api::_()->getItem('album_category', $this->album->category_id)->getCategorySlug(), 'subcategory_id' => $this->album->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('album_category', $this->album->subcategory_id)->getCategorySlug(), 'subsubcategory_id' => $this->album->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getItem('album_category', $this->album->subsubcategory_id)->getCategorySlug()), "sitealbum_general_subsubcategory") ?>"><?php echo $this->translate($this->subsubcategory_name); ?></a>
          <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
        <?php endif; ?>
      <?php endif; ?>
    <?php endif; ?>
  <?php endif; ?>
  <?php echo $this->album->getTitle(); ?>
</div>
