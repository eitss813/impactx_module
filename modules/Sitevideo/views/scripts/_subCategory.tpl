<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _subCategory.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$request = Zend_Controller_Front::getInstance()->getRequest();
$module = $request->getModuleName();
$controller = $request->getControllerName();
$action = $request->getActionName();
?>

<?php if ($module == 'sitevideo' && ($action == 'index' || $action == 'manage' || $action == 'browse')): ?>
    <li id='subcategory_id-wrapper' style='display:none;' > 
        <span ><?php echo $this->translate('Subcategory') ?></span>
        <select name='subcategory_id' id='subcategory_id' onchange="showFields(this.value, 2);
                addOptions(this.value, 'subcat_dependency', 'subsubcategory_id', 0);" ></select>
    </li>

    <li id='subsubcategory_id-wrapper' style='display:none;'>
        <span ><?php echo $this->translate('3%s Level Category', "<sup>rd</sup>") ?></span>
        <select name='subsubcategory_id' id='subsubcategory_id' onchange='showFields(this.value, 3);
                setSubSubCategorySlug(this.value);'></select>
    </li>
<?php elseif ($module == 'siteeventticket' && $controller == 'coupon' && $action == 'index'): ?>

    <div id='subcategory_id-wrapper' style='display:none;' class='form-wrapper'> 
        <span ><?php echo $this->translate('Sub-Category') ?></span>
        <select name='subcategory_id' id='subcategory_id' onchange="addOptions(this.value, 'subcat_dependency', 'subsubcategory_id', 0);" ></select>
    </div>

    <div id='subsubcategory_id-wrapper' style='display:none;' class='form-wrapper'>
        <span ><?php echo $this->translate('3%s Level Category', "<sup>rd</sup>") ?></span>
        <select name='subsubcategory_id' id='subsubcategory_id' onchange='setSubSubCategorySlug(this.value);'></select>
    </div>    
<?php else: ?>

    <div id='subcategory_id-wrapper' class='form-wrapper' style='display:none;'>
        <div class='form-label'><label><?php echo $this->translate('Sub-Category') ?></label></div>
        <div class='form-element'>
            <select name='subcategory_id' id='subcategory_id' onchange="showFields(this.value, 2);
                addOptions(this.value, 'subcat_dependency', 'subsubcategory_id', 0);" ></select>
        </div>
    </div>

    <div id='subsubcategory_id-wrapper' class='form-wrapper' style='display:none;'>
        <div class='form-label'><label><?php echo $this->translate('3%s Level Category', "<sup>rd</sup>") ?></label></div>
        <div class='form-element'>
            <select name='subsubcategory_id' id='subsubcategory_id' onchange='showFields(this.value, 3);
                setSubSubCategorySlug(this.value);'></select>
        </div>
    </div>
<?php endif; ?>

<script type="text/javascript">
        function setSubSubCategorySlug(value) {
            $('subsubcategoryname').value = sitevideo_categories_slug[value];
        }
</script>  