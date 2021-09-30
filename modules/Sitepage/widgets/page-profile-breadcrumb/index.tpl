<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div class="sitepage_profile_breadcrumb">
  <?php 
      $temp_general_url = $this->url(array(),'sitepage_general', false );
    
      if($this->category_name):
        $temp_general_category = $this->url(array('category_id' => $this->sitepage->category_id, 'categoryname' => Engine_Api::_()->getItem('sitepage_category', $this->sitepage->category_id)->getCategorySlug()), "sitepage_general_category");
      endif;
      
      if(!empty($this->subcategory_name)):
        $temp_general_subcategory = $this->url(array('category_id' => $this->sitepage->category_id, 'categoryname' => Engine_Api::_()->getItem('sitepage_category', $this->sitepage->category_id)->getCategorySlug(), 'subcategory_id' => $this->sitepage->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('sitepage_category', $this->sitepage->subcategory_id)->getCategorySlug()), "sitepage_general_subcategory");
      endif;
      
      if(!empty($this->subsubcategory_name)):
        $temp_general_subsubcategory = $this->url(array('category_id' => $this->sitepage->category_id, 'categoryname' => Engine_Api::_()->getItem('sitepage_category', $this->sitepage->category_id)->getCategorySlug(), 'subcategory_id' => $this->sitepage->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('sitepage_category', $this->sitepage->subcategory_id)->getCategorySlug(), 'subsubcategory_id' => $this->sitepage->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getItem('sitepage_category', $this->sitepage->subsubcategory_id)->getCategorySlug()), "sitepage_general_subsubcategory");
      endif;
  ?>
  <a href="<?php echo $temp_general_url;?>">
    <?php echo $this->translate("Pages Home");?>
  </a>
  <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
  <?php if ($this->category_name): ?>
    <a href="<?php echo $temp_general_category; ?>"><?php echo $this->translate($this->category_name); ?></a>
    <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
    <?php if (!empty($this->subcategory_name)): ?>
      <a href="<?php echo $temp_general_subcategory; ?>"><?php echo $this->translate($this->subcategory_name); ?></a>
      <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
      <?php if (!empty($this->subsubcategory_name)):?>
        <a href="<?php echo $temp_general_subsubcategory; ?>"><?php echo $this->translate($this->subsubcategory_name); ?></a>
        <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
      <?php endif; ?>
    <?php endif; ?>
  <?php endif; ?>
  <?php echo $this->sitepage->getTitle(); ?>
</div>

<style type="text/css">

.sitepage_profile_breadcrumb{
  font-size:11px;
  margin-bottom:10px;
}
.sitepage_profile_breadcrumb .brd-sep{
  margin:0 3px;
}

</style>