<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _subCategory.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$request = Zend_Controller_Front::getInstance()->getRequest();
$module = $request->getModuleName();
$action = $request->getActionName();
?>

<?php if ($module == 'sitealbum' && ($action == 'index' || $action == 'manage' || $action == 'browse')): ?>
  <li id='subcategory_id-wrapper' style='display:none;'>
    <div> 
      <?php echo $this->translate('Subcategory') ?></div>
    <div><select name='subcategory_id' id='subcategory_id' onchange="showFields(this.value, 2);
        setSubCategorySlug(this.value);" ></select>
    </div>
  </li>

<?php else: ?>

  <div id='subcategory_id-wrapper' class='form-wrapper' style='display:none;'>
    <div class='form-label'><label><?php echo $this->translate('Subcategory') ?></label></div>
    <div class='form-element'>
      <select name='subcategory_id' id='subcategory_id' onchange="showFields(this.value, 2);
        setSubCategorySlug(this.value);" ></select>
    </div>
  </div>

<?php endif; ?>

<script type="text/javascript">
    function setSubCategorySlug(value) {
      $('subcategoryname').value = sitealbum_categories_slug[value];
    }
</script>  
